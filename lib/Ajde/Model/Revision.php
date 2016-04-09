<?php

abstract class Ajde_Model_Revision extends Ajde_Model
{
    protected $_ignoreFieldInRevision        = [];
    protected $_ignoreFieldInRevisionIfEmpty = [];

    const DEFAULT_LIMIT = 50;

    public function getRevisionsHtml($crud = null)
    {
        if (!$this->getPK()) {
            return;
        }

        $revisions = new RevisionCollection();
        $revisions->addFilter(new Ajde_Filter_Where('model', Ajde_Filter::FILTER_EQUALS, $this->getModelName()));
        $revisions->addFilter(new Ajde_Filter_Where('foreignkey', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
        $revisions->orderBy('time', 'DESC');
        $revisions->limit(self::DEFAULT_LIMIT);

        $controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/crud:revisions'));
        $controller->setRevisions($revisions);
        $controller->setModel($this);
        $controller->setCrudInstance($crud);

        return $controller->invoke();
    }

    private function getModelName()
    {
        $modelNameCC = str_replace('Model', '', get_class($this));

        return $this->_tableName = $this->fromCamelCase($modelNameCC);
    }

    public function purgeRevisions()
    {
        if (!$this->getPK()) {
            return false;
        }

        $revisions = new RevisionCollection();
        $revisions->addFilter(new Ajde_Filter_Where('model', Ajde_Filter::FILTER_EQUALS, $this->getModelName()));
        $revisions->addFilter(new Ajde_Filter_Where('foreignkey', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
        $revisions->deleteAll();

        return true;
    }

    public function save()
    {
        // check all changed fields
        $modelName   = get_class($this);
        $shadowModel = new $modelName;
        /* @var $shadowModel Ajde_Model */
        $shadowModel->loadByPK($this->getPK());
        if ($shadowModel->_hasMeta) {
            $shadowModel->populateMeta();
        }

        // old values
        $oldValues = $shadowModel->values();
        foreach ($oldValues as &$oldValue) {
            @$oldValue = (string)$oldValue;
        }

        // populate meta of current model, but don't override
        if ($this->_hasMeta) {
            $this->populateMeta(false, false);
        }

        // new values
        $newValues = $this->values();
        foreach ($newValues as $k => &$newValue) {
            if ($k == 'meta_4') {
                //                die('hier');
            }
            @$newValue = (string)$newValue;
        }

        // ignore fields
        foreach ($this->_ignoreFieldInRevision as $ignoreField) {
            unset($oldValues[$ignoreField]);
            unset($newValues[$ignoreField]);
        }

        // ignore fields
        foreach ($this->_ignoreFieldInRevisionIfEmpty as $ignoreField) {
            if (!isset($newValues[$ignoreField]) || empty($newValues[$ignoreField])) {
                unset($oldValues[$ignoreField]);
                unset($newValues[$ignoreField]);
            }
        }

        if ($diffs = array_diff_assoc($oldValues, $newValues)) {
            foreach ($diffs as $diffField => $diffValue) {
                $revision             = new RevisionModel();
                $revision->model      = $this->getModelName();
                $revision->foreignkey = $this->getPK();
                $revision->user       = UserModel::getLoggedIn();
                $revision->field      = $diffField;
                $revision->old        = issetor($oldValues[$diffField]);
                $revision->new        = issetor($newValues[$diffField]);
                $revision->insert();
            }
        }

        return parent::save();
    }
}
