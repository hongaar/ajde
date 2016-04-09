<?php

class AdminNodeController extends AdminController
{
    public function beforeInvoke($allowed = [])
    {
        return parent::beforeInvoke($allowed);
    }

    public function view()
    {
        Ajde::app()->getDocument()->setTitle("Nodes");

        return $this->render();
    }

    public function panel()
    {
        $item = $this->getItem();
        $this->getView()->assign('item', $item);

        return $this->render();
    }

    public function quickJson()
    {
        $parent    = Ajde::app()->getRequest()->getPostParam('parent');
        $title     = Ajde::app()->getRequest()->getPostParam('title');
        $due       = Ajde::app()->getRequest()->getPostParam('due');
        $allocated = Ajde::app()->getRequest()->getPostParam('allocated');

        $model = new NodeModel();

        $model->populate([
            'parent'   => $parent,
            'title'    => $title,
            'user'     => UserModel::getLoggedIn()->getPK(),
            'nodetype' => NodeModel::NODETYPE_ISSUE
        ]);

        Ajde_Event::trigger($model, 'beforeCrudSave', []);
        $success = $model->insert();
        Ajde_Event::trigger($model, 'afterCrudSave', []);

        $model->saveMetaValue(NodeModel::META_ISSUESTATUS, NodeModel::ISSUESTATUS_NEW);
        $model->saveMetaValue(NodeModel::META_ISSUEDUE, $due);
        $model->saveMetaValue(NodeModel::META_ALLOCATED, $allocated);

        return [
            'success' => $success,
            'message' => $success ? 'Node added' : 'Something went wrong'
        ];
    }

    public function updateJson()
    {
        $id = Ajde::app()->getRequest()->getParam('id');

        $meta  = Ajde::app()->getRequest()->getPostParam('meta');
        $key   = Ajde::app()->getRequest()->getPostParam('key');
        $value = Ajde::app()->getRequest()->getPostParam('value');

        $model = new NodeModel();

        $model->loadByPK($id);
        $success = false;
        if ($meta) {
            $model->saveMetaValue($key, $value);
            $success = true;
        } else {
            $model->set($key, $value);
            $success = $model->save();
        }

        return [
            'success' => true,
            'message' => $success ? 'Node updated' : 'Something went wrong'
        ];
    }

    public function searchJson()
    {
        $q = Ajde::app()->getRequest()->getParam('query');

        $collection = new NodeCollection();

        // split search terms
        $terms = explode(" ", $q);

        // search on node fields
        $searchGroup = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
        foreach ($terms as $term) {
            $termGroup = $collection->getTextFilterGroup($term, Ajde_Query::OP_OR);
            if ($termGroup !== false) {
                $searchGroup->addFilter($termGroup);
            }
        }
        $collection->addFilter($searchGroup);

        // search on meta search
        $collection->addFilter(new Ajde_Filter_LeftJoin('node_meta', 'node_meta.node', 'node.id'));
        $searchGroup = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
        foreach ($terms as $term) {
            $searchGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter::FILTER_LIKE,
                '%' . $term . '%', Ajde_Query::OP_OR));
        }
        $collection->addFilter($searchGroup);
        $collection->getQuery()->addGroupBy('node.id');

        // join in nodetype info
        $collection->joinNodetype();
        $collection->getQuery()->addSelect('nodetype.name AS nodetype_name');
        $collection->getQuery()->addSelect('nodetype.icon AS nodetype_icon');

        $suggestions = [];
        foreach ($collection as $node) {
            /* @var $node NodeModel */
            $suggestions[] = [
                'value' => "<i class='" . $node->get('nodetype_icon') . "'></i> " . $node->displayField(),
                'data'  => $node->getPK()
            ];
        }

        return [
            'query'       => $q,
            'suggestions' => $suggestions
        ];
    }

}
