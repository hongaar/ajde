<?php

/**
 * Default to extend Ajde_Acl_Controller for enhanced security.
 */
class _coreCrudController extends Ajde_Acl_Controller
{
    /************************
     * Ajde_Component_Crud
     ************************/

    public function beforeInvoke($allowed = [])
    {
        // disable cache and auto translations
        Ajde_Cache::getInstance()->disable();
        Ajde_Lang::getInstance()->disableAutoTranslationOfModels();

        // try to get the crud instance
        $crud = $this->hasCrudInstance() ? $this->getCrudInstance() : false;
        if (!$crud && Ajde::app()->getRequest()->has('crudId')) {
            $session = new Ajde_Session('AC.Crud');
            $crud = $session->getModel(Ajde::app()->getRequest()->getParam('crudId'));
        }
        if ($crud) {
            /* @var $crud Ajde_Crud */
            $this->setAclParam($crud->getSessionName());
        }

        return parent::beforeInvoke();
    }

    public function listHtml()
    {
        if (Ajde::app()->getRequest()->has('edit') || Ajde::app()->getRequest()->has('new')) {
            return $this->editDefault();
        }

        if (Ajde::app()->getRequest()->has('output') && Ajde::app()->getRequest()->get('output') == 'table') {
            Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));
        }

        $crud = $this->getCrudInstance();
        /* @var $crud Ajde_Crud */
        if (!$crud) {
            Ajde::app()->getResponse()->redirectNotFound();
        }

        $session = new Ajde_Session('AC.Crud');
        $session->setModel($crud->getHash(), $crud);

        // just preload it
        $crud->getCollectionView();

        if (Ajde::app()->getRequest()->has('output') && Ajde::app()->getRequest()->get('output') == 'excel') {
            $url = '_core/crud:exportBuffer';

            $exporter = $crud->export('excel');

            $exportSession = new Ajde_Session('export');
            $exportSession->setModel('exporter', $exporter);

            $this->redirect($url);

            return false;
        }

        $view = $crud->getTemplate();
        $view->assign('crud', $crud);

        return $view->render();
    }

    public function revisionsHtml()
    {
        $this->setAction('edit/revisions');

        $this->getView()->assign('revisions', $this->getRevisions());
        $this->getView()->assign('model', $this->getModel());
        $this->getView()->assign('crud', $this->getCrudInstance());

        return $this->render();
    }

    public function exportBuffer()
    {
        $exportSession = new Ajde_Session('export');

        $exporter = $exportSession->getModel('exporter');
        /* @var $exporter Ajde_Crud_Export_Interface */
        echo $exporter->send();
        exit;
    }

    public function editDefault()
    {
        $this->setAction('edit');

        $crud = $this->getCrudInstance();
        /* @var $crud Ajde_Crud */
        if (!$crud) {
            Ajde::app()->getResponse()->redirectNotFound();
        }

        $editOptions = $crud->getOptions('edit');
        if ($crud->getOperation() === 'list') {
            if (!empty($editOptions) &&
                isset($editOptions['action'])
            ) {
                $crud->setAction($editOptions['action']);
            } else {
                if ($crud->getOption('edit.layout')) {
                    $crud->setAction('edit/layout');
                } else {
                    // Automatically switch to layouts now
                    //					$crud->setAction('edit');

                    // Insert layout and set action
                    $show = $crud->getOption('edit.show');
                    $editOptions = new Ajde_Crud_Options_Edit();
                    $editOptions->selectLayout()->addRow()->addColumn()->setSpan(12)->addBlock()->setShow($show)->finished();
                    $crud->setOption('edit', $editOptions->getArray());
                    $crud->setAction('edit/layout');
                }
            }
        }

        if (!$crud->hasId()) {
            $crud->setId(Ajde::app()->getRequest()->getParam('edit', false));
        }

        // get view for crud instance, load from request, but do not persist
        $crudView = $crud->getCollectionView();

        // current mainfilter from view
        $mainFilter = $crudView->getMainFilter();

        if ($mainFilter) {

            // get current filter
            $currentFilter = $crudView->getFilterForField($mainFilter);

            // update mainfilter for new records
            if (Ajde::app()->getRequest()->has('new')) {
                $crud->setOption('fields.'.$mainFilter.'.value', $currentFilter);
            }

            // hide mainfilter fields
            $crud->setOption('fields.'.$mainFilter.'.hidden', true);
        }

        // Set prefilled, disabled and hidden fields from request
        $disable = Ajde::app()->getRequest()->getParam('disable', []);
        $hide = Ajde::app()->getRequest()->getParam('hide', []);
        $prefill = Ajde::app()->getRequest()->getParam('prefill', []);
        foreach ($prefill as $field => $value) {
            $crud->setOption('fields.'.$field.'.value', $value);
        }
        foreach ($disable as $field => $value) {
            if ($value) {
                $crud->setOption('fields.'.$field.'.readonly', true);
            }
        }
        foreach ($hide as $field => $value) {
            if ($value) {
                $crud->setOption('fields.'.$field.'.hidden', true);
            }
        }

        // Read only entire view?
        if ($crud->getOption('edit.readonly', false)) {
            $crud->setReadOnlyForAllFields();
        }

        // Reload Crud fields in case they were already loaded
        $crud->loadFields();

        $session = new Ajde_Session('AC.Crud');
        $session->setModel($crud->getHash(), $crud);

        $view = $crud->getTemplate();

        $view->requireJsFirst('component/shortcut', 'html', MODULE_DIR.'_core/');
        $view->assign('crud', $crud);

        // Editor
        if (config('layout.textEditor')) {
            $editorClassName = 'Ajde_Crud_Editor_'.ucfirst(config('layout.textEditor'));
            $textEditor = new $editorClassName();
            /* @var $textEditor Ajde_Crud_Editor */
            $textEditor->getResources($view);
        }

        return $view->render();
    }

    public function mainfilterHtml()
    {
        $crud = $this->getCrudInstance();
        $this->getView()->assign('crud', $crud);
        $this->getView()->assign('refresh', $this->getRefresh());

        return $this->render();
    }

    public function commitJson()
    {
        $operation = Ajde::app()->getRequest()->getParam('operation');
        $crudId = Ajde::app()->getRequest()->getParam('crudId');
        $id = Ajde::app()->getRequest()->getPostParam('id', false);
        if (Ajde::app()->getRequest()->getPostParam('form_submission', false)) {
            $operation = 'submission';
        }

        switch ($operation) {
            case 'delete':
                return $this->delete($crudId, $id);
                break;
            case 'submission':
                return $this->submission($crudId, $id);
                break;
            case 'save':
                return $this->save($crudId, $id);
                break;
            case 'sort':
                return $this->sort($crudId, $id);
                break;
            case 'deleteMultiple':
                return $this->deleteMultiple($crudId, $id);
                break;
            case 'addMultiple':
                return $this->addMultiple($crudId, $id);
                break;
            case 'getMultipleRow':
                return $this->getMultipleRow($crudId);
                break;
            case 'purgeRevisions':
                return $this->purgeRevisions($crudId);
                break;
            default:
                return ['operation' => $operation, 'success' => false];
                break;
        }
    }

    private function delete($crudId, $id)
    {
        $session = new Ajde_Session('AC.Crud');
        $crud = $session->getModel($crudId);
        $model = $crud->getModel();

        if (!is_array($id)) {
            $id = [$id];
        }

        $success = true;
        $deleted = 0;
        foreach ($id as $elm) {
            $model->loadByPK($elm);
            if ($result = $model->delete()) {
                $deleted++;
            }
            $success = $success * $result;
        }

        return [
            'operation' => 'delete',
            'success'   => (bool) $success,
            'message'   => Ajde_Component_String::makePlural($deleted, 'record').' deleted',
        ];
    }

    private function sort($crudId, $id)
    {
        $session = new Ajde_Session('AC.Crud');
        /* @var $crud Ajde_Crud */
        $crud = $session->getModel($crudId);
        $model = $crud->getModel();

        // Extra careful handling of parameters, as we are baking crude SQL here

        if (Ajde::app()->getRequest()->hasPostParam('table')) {

            // Only allow alfanumeric, ., _ and - in table and field names
            $sortField = preg_replace("/[^0-9a-zA-Z_\-\.]/i", '', Ajde::app()->getRequest()->getPostParam('field'));
            $sortPK = preg_replace("/[^0-9a-zA-Z_\-\.]/i", '', Ajde::app()->getRequest()->getPostParam('pk'));
            $sortTable = preg_replace("/[^0-9a-zA-Z_\-\.]/i", '', Ajde::app()->getRequest()->getPostParam('table'));

            if (!is_array($id)) {
                $id = [$id];
            }

            // Make sure ids is a array of integers
            $ids = [];
            foreach ($id as $elm) {
                if ($elm) {
                    $ids[] = (int) $elm;
                }
            }

            // Get lowest current sort values
            $idString = implode(', ', $ids);
            $sql = 'SELECT MIN('.$sortField.') AS min FROM '.$sortTable.' WHERE '.$sortPK.' IN ('.$idString.')';

            $statement = $model->getConnection()->prepare($sql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result === false || empty($result)) {
                $sortValue = 0;
            } else {
                $sortValue = $result['min'];
            }

            $success = true;
            foreach ($ids as $id) {
                $values = [$sortValue, $id];
                $sql = 'UPDATE '.$sortTable.' SET '.$sortField.' = ? WHERE '.$sortPK.' = ?';

                $statement = $model->getConnection()->prepare($sql);
                $success = $success * $statement->execute($values);
                $sortValue++;
            }
        } else {
            // Get and validate sort field
            $sortField = Ajde::app()->getRequest()->getPostParam('field');
            $sortTable = (string) $model->getTable();
            $field = $crud->getField($sortField); // throws exception when not found

            if (!is_array($id)) {
                $id = [$id];
            }

            // Make sure ids is a array of integers
            $ids = [];
            foreach ($id as $elm) {
                if (!empty($elm)) {
                    $ids[] = (int) $elm;
                }
            }

            // Get lowest current sort values
            $idString = implode(', ', $ids);
            $sql = 'SELECT MIN('.$sortField.') AS min FROM '.$sortTable.' WHERE '.$model->getTable()->getPK().' IN ('.$idString.')';

            $statement = $model->getConnection()->prepare($sql);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result === false || empty($result)) {
                $sortValue = 0;
            } else {
                $sortValue = $result['min'];
            }

            $success = true;
            foreach ($ids as $id) {
                $model->loadByPK($id);
                $model->set($sortField, $sortValue);
                // don't update fields with name 'updated'
                $model->set('updated', new Ajde_Db_Function('updated'));
                $success = $success * $model->save();
                $sortValue++;
                if ($field->has('sort_children')) {
                    // TODO: implement parent recursive sorting
                }
            }

            // Call afterSort once on model
            if (method_exists($model, 'afterSort')) {
                $model->afterSort();
            }
        }

        return [
            'operation' => 'sort',
            'success'   => (bool) $success,
            'message'   => 'Records sorted',
        ];
    }

    private function save($crudId, $id)
    {
        $session = new Ajde_Session('AC.Crud');

        /* @var $crud Ajde_Crud */
        $crud = $session->getModel($crudId);

        // verify that we have a valid crud model
        if (!$crud) {
            return ['success' => false];
        }

        /* @var $model Ajde_Model */
        $model = $crud->getModel();
        $model->setOptions($crud->getOptions('model'));

        // Get POST params
        $post = Ajde_Http_Request::globalPost();
        foreach ($post as $key => $value) {
            // Include empty values, so we can set them to null if the table structure allows us
            //			if (empty($value)) {
            //				unset($post[$key]);
            //			}
        }
        $id = issetor($post['id']);
        $operation = empty($id) ? 'insert' : 'save';

        if ($operation === 'save') {
            $model->loadByPK($id);
        }
        $model->populate($post);

        Ajde_Event::trigger($model, 'beforeCrudSave', [$crud]);

        if (!$model->validate($crud->getOptions('fields'))) {
            return ['operation' => $operation, 'success' => false, 'errors' => $model->getValidationErrors()];
        }
        //		if (!$model->autocorrect($crud->getOptions('fields'))) {
        //			return array('operation' => $operation, 'success' => false, 'errors' => $model->getAutocorrectErrors());
        //		}

        $success = $model->{$operation}();

        Ajde_Event::trigger($model, 'afterCrudSave', [$crud]);

        // Multiple field SimpleSelector
        foreach ($crud->getOption('fields') as $key => $field) {
            if (isset($field['crossReferenceTable']) && isset($field['simpleSelector']) && $field['simpleSelector'] === true) {
                $this->deleteMultiple($crud, null, $model->getPK(), $key, true);
                if (isset($post[$key]) && is_array($post[$key])) {
                    foreach ($post[$key] as $item) {
                        $this->addMultiple($crud, $item, $model->getPK(), $key);
                    }
                }
            }
        }

        if ($success === true) {
            if (Ajde::app()->getRequest()->getParam('fromAutoSave', '0') != 1) {

                // Destroy reference to crud instance
                $session->destroy($crudId);

                if (Ajde::app()->getRequest()->getParam('fromIframe', '0') != 1) {
                    // Set flash alert
                    Ajde_Session_Flash::alert('Record '.($operation == 'insert' ? 'added' : 'saved').': '.$model->get($model->getDisplayField()));
                }
            }
        }

        return [
            'operation'    => $operation,
            'id'           => $model->getPK(),
            'displayField' => $model->get($model->getDisplayField()),
            'success'      => $success,
        ];
    }

    private function submission($crudId, $id)
    {
        $session = new Ajde_Session('AC.Crud');

        /* @var $crud Ajde_Crud */
        $crud = $session->getModel($crudId);

        // verify that we have a valid crud model
        if (!$crud) {
            return ['success' => false];
        }

        /* @var $model FormModel */
        $model = $crud->getModel();
        $model->setOptions($crud->getOptions('model'));

        // Get POST params
        $post = Ajde_Http_Request::globalPost();
        $id = issetor($post['id']);

        // verify that we have a valid form model
        if (!$id) {
            return ['success' => false];
        }

        // load form
        $model->loadByPK($id);
        $model->populate($post);

        // validate form
        Ajde_Event::trigger($model, 'beforeCrudSave', [$crud]);
        if (!$model->validate($crud->getOptions('fields'))) {
            return ['operation' => 'save', 'success' => false, 'errors' => $model->getValidationErrors()];
        }

        // prepare submission
        $values = [];
        foreach ($post as $key => $value) {
            if (substr($key, 0, 5) === 'meta_') {
                $metaId = str_replace('meta_', '', $key);
                $metaName = MetaModel::getNameFromId($metaId);
                $values[$metaName] = $value;
            }
        }

        $entryText = '';
        foreach ($values as $k => $v) {
            $entryText .= $k.': '.$v.PHP_EOL;
        }

        $submission = new SubmissionModel();
        $submission->form = $id;
        $submission->ip = $_SERVER['REMOTE_ADDR'];
        $submission->user = Ajde_User::getLoggedIn();
        $submission->entry = json_encode($values);
        $submission->entry_text = $entryText;

        $success = $submission->insert();

        if ($success === true) {

            // Destroy reference to crud instance
            $session->destroy($crudId);

            // set message for next page
            Ajde_Session_Flash::alert(trans('Form submitted successfully'));

            $mailer = new Ajde_Mailer();

            // send email to administrator
            $body = 'Form: '.$model->displayField().'<br/><br/>'.nl2br($entryText);
            $mailer->SendQuickMail(config('app.email'), config('app.email'), config('app.title'),
                'New form submission', $body);

            // send email to user
            $email = $model->getEmail();
            /* @var $email EmailModel */
            $email_to = $model->getEmailTo();
            /* @var $email MetaModel */
            $email_address = issetor($post['meta_'.$email_to->getPK()]);
            if ($email->hasLoaded() && $email_to->hasLoaded() && $email_address) {
                $mailer->sendUsingModel($email->getIdentifier(), $email_address, $email_address, [
                    'entry' => nl2br($entryText),
                ]);
            }
        }

        return [
            'operation'    => 'save',
            'id'           => $model->getPK(),
            'displayField' => $model->get($model->getDisplayField()),
            'success'      => $success,
        ];
    }

    private function deleteMultiple($crudId, $id, $parentId = null, $fieldName = null, $all = false)
    {
        /* @var $crud Ajde_Crud */
        if ($crudId instanceof Ajde_Crud) {
            $crud = $crudId;
        } else {
            $session = new Ajde_Session('AC.Crud');
            $crud = $session->getModel($crudId);
        }

        /* @var $model Ajde_Model */
        $model = $crud->getModel();

        $parentId = isset($parentId) ? $parentId : Ajde::app()->getRequest()->getPostParam('parent_id');
        $fieldName = isset($fieldName) ? $fieldName : Ajde::app()->getRequest()->getPostParam('field');

        // Get field properties
        $fieldProperties = $crud->getOption('fields.'.$fieldName);

        $success = false;
        $modelName = $crud->getOption('fields.'.$fieldName.'.modelName', $fieldName);
        if (isset($fieldProperties['crossReferenceTable'])) {
            if ($all === true) {
                $parentField = (string) $model->getTable();
                $sql = 'DELETE FROM '.$fieldProperties['crossReferenceTable'].' WHERE '.$parentField.' = ?';
                $values = [$parentId];

                // Setup constraints
                if (isset($fieldProperties['crossRefConstraints'])) {
                    foreach ($fieldProperties['crossRefConstraints'] as $k => $v) {
                        $sql .= ' AND '.$k.' = ?';
                        $values[] = $v;
                    }
                }

                $statement = $model->getConnection()->prepare($sql);
                $success = $statement->execute($values);
            } else {
                $childField = isset($fieldProperties['childField']) ? $fieldProperties['childField'] : $modelName;
                $parentField = (string) $model->getTable();
                $sql = 'DELETE FROM '.$fieldProperties['crossReferenceTable'].' WHERE '.$parentField.' = ? AND '.$childField.' = ?';
                $values = [$parentId, $id];

                // Setup constraints
                if (isset($fieldProperties['crossRefConstraints'])) {
                    foreach ($fieldProperties['crossRefConstraints'] as $k => $v) {
                        $sql .= ' AND '.$k.' = ?';
                        $values[] = $v;
                    }
                }

                $sql .= ' LIMIT 1';
                $statement = $model->getConnection()->prepare($sql);
                $success = $statement->execute($values);
            }
        } else {
            $childClass = ucfirst($modelName).'Model';
            $child = new $childClass();
            /* @var $child Ajde_Model */
            $child->loadByPK($id);
            $success = $child->delete();
        }

        return [
            'operation' => 'deleteMultiple',
            'success'   => $success,
            'message'   => ucfirst($modelName).(isset($fieldProperties['crossReferenceTable']) ? ' disconnected' : ' deleted'),
        ];
    }

    private function addMultiple($crudId, $id, $parentId = null, $fieldName = null)
    {
        /* @var $crud Ajde_Crud */
        if ($crudId instanceof Ajde_Crud) {
            $crud = $crudId;
        } else {
            $session = new Ajde_Session('AC.Crud');
            $crud = $session->getModel($crudId);
        }

        /* @var $model Ajde_Model */
        $model = $crud->getModel();

        $parentId = isset($parentId) ? $parentId : Ajde::app()->getRequest()->getPostParam('parent_id');
        $fieldName = isset($fieldName) ? $fieldName : Ajde::app()->getRequest()->getPostParam('field');

        // Get field properties
        $fieldProperties = $crud->getOption('fields.'.$fieldName);

        $success = false;
        $modelName = $crud->getOption('fields.'.$fieldName.'.modelName', $fieldName);
        if (isset($fieldProperties['crossReferenceTable'])) {
            $childField = isset($fieldProperties['childField']) ? $fieldProperties['childField'] : $modelName;

            // Already in there?
            $parentField = (string) $model->getTable();
            $values = [$parentId, $id];
            $sql = 'SELECT * FROM '.$fieldProperties['crossReferenceTable'];
            $sql .= ' WHERE '.$parentField.' = ? AND '.$childField.' = ?';
            if (isset($fieldProperties['crossRefConstraints'])) {
                foreach ($fieldProperties['crossRefConstraints'] as $k => $v) {
                    $sql .= ' AND '.$k.' = ?';
                    $values[] = $v;
                }
            }
            $sql .= ' LIMIT 1';
            $statement = $model->getConnection()->prepare($sql);
            $success = $statement->execute($values);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($success === true && $result !== false && !empty($result)) {
                return [
                    'operation' => 'addMultiple',
                    'success'   => false,
                    'message'   => ucfirst($fieldName).' already connected',
                ];
            }

            // Sql to use when no sorting
            $fieldList = '';
            $valueList = '?, ?';
            $values = [$parentId, $id];

            // Sort fields?
            if (isset($fieldProperties['tableFields'])) {
                foreach ($fieldProperties['tableFields'] as $extraField) {
                    if ($extraField['type'] == 'sort') {
                        // Get highest current sort value
                        $sql = 'SELECT MAX('.$extraField['name'].') AS max FROM '.$fieldProperties['crossReferenceTable'];

                        $statement = $model->getConnection()->prepare($sql);
                        $statement->execute();
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        if ($result === false || empty($result)) {
                            $sortValue = 999;
                        } else {
                            $sortValue = $result['max'] + 1;
                        }
                        $fieldList .= ', '.$extraField['name'];
                        $valueList = '?, ?, ?';
                        $values[] = $sortValue;
                    }
                }
            }

            // Setup constraints
            if (isset($fieldProperties['crossRefConstraints'])) {
                foreach ($fieldProperties['crossRefConstraints'] as $k => $v) {
                    $fieldList .= ', '.$k;
                    $valueList .= ', ?';
                    $values[] = $v;
                }
            }

            $sql = 'INSERT INTO '.$fieldProperties['crossReferenceTable'].' ('.$parentField.', '.$childField.$fieldList.') VALUES ('.$valueList.')';
            $statement = $model->getConnection()->prepare($sql);
            $success = $statement->execute($values);
            $lastId = $model->getConnection()->lastInsertId();
        } else {
            // Not possible
        }

        return [
            'operation' => 'addMultiple',
            'success'   => $success,
            'lastId'    => $lastId,
            'message'   => $success ? ucfirst($modelName).' added' : 'An error occured',
        ];
    }

    private function getMultipleRow($crudId)
    {
        $session = new Ajde_Session('AC.Crud');
        /* @var $crud Ajde_Crud */
        $crud = $session->getModel($crudId);
        /* @var $model Ajde_Model */
        $model = $crud->getModel();

        // Get field properties
        $id = Ajde::app()->getRequest()->getParam('id');
        $fieldName = Ajde::app()->getRequest()->getParam('field');
        $fieldProperties = $crud->getOption('fields.'.$fieldName);
        $modelName = $crud->getOption('fields.'.$fieldName.'.modelName', $fieldName);

        // Get child model
        $className = ucfirst($modelName).'Model';
        $child = new $className();
        /* @var $child Ajde_Model */
        $child->loadByPK($id);

        $ret = [];
        if (isset($fieldProperties['tableFields'])) {
            foreach ($fieldProperties['tableFields'] as $extraField) {
                $value = $child->has($extraField['name']) ? $child->get($extraField['name']) : false;
                $type = $extraField['type'];
                $html = false;
                if ($type == 'file' && $value) {
                    $extension = pathinfo($value, PATHINFO_EXTENSION);
                    if ($isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $thumbDim = isset($fieldProperties['thumbDim']) ? $fieldProperties['thumbDim'] : ['width'  => 75,
                                                                                                          'height' => 75,
                        ];
                        $html = "<a class='imagePreview img' title='".esc($value)."' href='".$extraField['saveDir'].$value."' target='_blank'>";
                        $image = new Ajde_Resource_Image($extraField['saveDir'].$value);
                        $image->setWidth($thumbDim['width']);
                        $image->setHeight($thumbDim['height']);
                        $image->setCrop(true);
                        $html = $html."<img src='".$image->getLinkUrl()."' width='".$thumbDim['width']."' height='".$thumbDim['height']."' />";
                        $html = $html.'</a>';
                    } else {
                        $html = "<img class='icon' src='".Ajde_Resource_FileIcon::_($extension)."' />";
                        $html = $html." <a class='filePreview preview' href='".$extraField['saveDir'].$value."' target='_blank'>".$value.'</a>';
                    }
                } else {
                    if ($type == 'text') {
                        $html = $value;
                    }
                }
                if ($html) {
                    $ret[] = $html;
                }
            }
        }

        $success = true;

        return [
            'operation'    => 'getMultipleRow',
            'success'      => $success,
            'displayField' => $child->get($child->getDisplayField()),
            'data'         => $ret,
        ];
    }

    private function purgeRevisions($crudId)
    {
        $session = new Ajde_Session('AC.Crud');
        /* @var $crud Ajde_Crud */
        $crud = $session->getModel($crudId);
        /* @var $model Ajde_Model */
        $model = $crud->getModel();

        $success = $model->purgeRevisions();

        return [
            'operation' => 'purgeRevisions',
            'success'   => $success,
            'message'   => 'Revisions purged',
        ];
    }
}
