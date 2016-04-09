<?php

/**
 * @property bool ignoreAccessControl
 * @property bool autoRedirect
 */
class NodeCollection extends Ajde_Collection_With_AclI18n
{
    public function filterPublished()
    {
        $this->addFilter(new Ajde_Filter_Where('published', Ajde_Filter::FILTER_EQUALS, 1));
        $startGroup = new Ajde_Filter_WhereGroup();
        $startGroup->addFilter(new Ajde_Filter_Where('published_start', Ajde_Filter::FILTER_IS, null));
        $startGroup->addFilter(new Ajde_Filter_Where('published_start', Ajde_Filter::FILTER_LESSOREQUAL,
            new Ajde_Db_Function('NOW()'), Ajde_Query::OP_OR));
        $endGroup = new Ajde_Filter_WhereGroup();
        $endGroup->addFilter(new Ajde_Filter_Where('published_end', Ajde_Filter::FILTER_IS, null));
        $endGroup->addFilter(new Ajde_Filter_Where('published_end', Ajde_Filter::FILTER_GREATEROREQUAL,
            new Ajde_Db_Function('NOW()'), Ajde_Query::OP_OR));
        $this->addFilter($startGroup);
        $this->addFilter($endGroup);
    }

    public function filterByType($type, $operator = Ajde_Query::OP_AND)
    {
        if (is_numeric($type)) {
            $this->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALS, $type, $operator));
        } else {
            $niceName = str_replace('_', ' ', $type);
            $nodetype = new NodetypeModel();
            if ($nodetype->loadByField('name', $niceName)) {
                $this->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALS, $nodetype->getPK(),
                    $operator));
            }
        }

        return $this;
    }

    public function filterByParent($nodeId)
    {
        if ($nodeId instanceof NodeModel) {
            $nodeId = $nodeId->getPK();
        }
        $this->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, $nodeId));

        return $this;
    }

    public function filterChildrenOfParent($parentId, $levels = 6)
    {
        $this->getQuery()->setDistinct(true);
        $subqueryFragment = "(SELECT t%.id FROM node AS t1";
        for ($i = 2; $i < $levels + 1; $i++) {
            $subqueryFragment .= " LEFT JOIN node AS t$i ON t$i.parent = t" . ($i - 1) . ".id";
        }
        $subqueryFragment .= " WHERE";
        for ($i = 1; $i < $levels + 1; $i++) {
            $subqueryFragment .= " t$i.parent = " . (int)(string)$parentId;
            if ($i < $levels) {
                $subqueryFragment .= " OR";
            }
        }
        $subqueryFragment .= ')';
        $filterGroup = new Ajde_Filter_WhereGroup();
        for ($i = 1; $i < $levels + 1; $i++) {
            $subquery = str_replace('%', $i, $subqueryFragment);
            $filterGroup->addFilter(new Ajde_Filter_Where('node.id', Ajde_Filter_Where::FILTER_IN,
                new Ajde_Db_Function($subquery), Ajde_Query::OP_OR));
        }
        $this->addFilter($filterGroup);
    }

    public function filterByMetaValue($meta, $value)
    {
        $lookupField = 'meta.id';
        if (!is_numeric($meta)) {
            $lookupField = 'meta.name';
            $meta        = str_replace('_', ' ', $meta);
        }
        $this->addFilter(new Ajde_Filter_Where($lookupField, Ajde_Filter::FILTER_EQUALS, $meta));
        $this->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter::FILTER_EQUALS, $value));
        $this->joinMeta();

        return $this;
    }

    public function filterByMetaValues($meta, $values)
    {
        $lookupField = 'meta.id';
        if (!is_numeric($meta)) {
            $lookupField = 'meta.name';
            $meta        = str_replace('_', ' ', $meta);
        }
        $whereGroup = new Ajde_Filter_WhereGroup();
        foreach ($values as $value) {
            $metaWhereGroup = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
            $metaWhereGroup->addFilter(new Ajde_Filter_Where($lookupField, Ajde_Filter::FILTER_EQUALS, $meta));
            $metaWhereGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter::FILTER_EQUALS, $value));
            $whereGroup->addFilter($metaWhereGroup);
        }
        $this->addFilter($whereGroup);
        $this->joinMeta();

        return $this;
    }

    public function filterByNodetypeCategory($category, $operator = Ajde_Query::OP_AND)
    {
        $nodetypes = new NodetypeCollection();
        $nodetypes->addFilter(new Ajde_Filter_Where('category', Ajde_Filter::FILTER_EQUALS, $category));
        $ids = [];
        foreach ($nodetypes as $nodetype) {
            $ids[] = $nodetype->id;
        }
        $this->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter_Where::FILTER_IN,
            new Ajde_Db_Function('(' . implode(',', $ids) . ')'), $operator));
    }

    public function filterByLevel($level)
    {
        $this->addFilter(new Ajde_Filter_Where('level', Ajde_Filter::FILTER_EQUALS, (int)$level));

        return $this;
    }

    public function joinMeta()
    {
        $this->addFilter(new Ajde_Filter_LeftJoin('node_meta', 'node_meta.node', 'node.id'));
        $this->addFilter(new Ajde_Filter_LeftJoin('meta', 'meta.id', 'node_meta.meta'));

        return $this;
    }

    public function joinMetaConditional($metaId, Ajde_Filter_WhereGroup $wheregroup)
    {
        $this->addFilter(new Ajde_Filter_Join('node_meta', 'node_meta.node', 'node.id'));
        $this->addFilter(new Ajde_Filter_Join('meta', 'meta.id', 'node_meta.meta'));
        $this->addFilter(new Ajde_Filter_Where('meta.id', Ajde_Filter::FILTER_EQUALS, $metaId));
        $this->addFilter($wheregroup);

        return $this;
    }

    public function joinMetaAs($metaId, $as)
    {
        $nodemeta = 'table' . md5('nodemeta' . rand(0, 999) . microtime(true));
        $meta     = 'table' . md5('meta' . rand(0, 999) . microtime(true));
        $this->addFilter(new Ajde_Filter_Join('node_meta ' . $nodemeta, $nodemeta . '.node', 'node.id'));
        $this->addFilter(new Ajde_Filter_Join('meta ' . $meta, $meta . '.id', $nodemeta . '.meta'));
        $this->addFilter(new Ajde_Filter_Where($meta . '.id', Ajde_Filter::FILTER_EQUALS, $metaId));
        $this->getQuery()->addSelect($nodemeta . '.value AS ' . $as);

        return $this;
    }

    public function joinNodetype()
    {
        $this->addFilter(new Ajde_Filter_Join('nodetype', 'nodetype.id', 'node.nodetype'));

        return $this;
    }

    public function concatTags()
    {
        $this->addFilter(new Ajde_Filter_Join('node_tag', 'node.id', 'node_tag.node'));
        $this->addFilter(new Ajde_Filter_Join('tag', 'tag.id', 'node_tag.tag'));
        $this->getQuery()->addSelect(new Ajde_Db_Function(
            'GROUP_CONCAT(tag.name)' .
            'AS tags'
        ));
        $this->getQuery()->addGroupBy('node.id');

        return $this;
    }

    public function load()
    {
        if (Ajde::app()->getRequest()->getParam('filterPublished', false) == true) {
            $this->filterPublished();
        }

        return parent::load();
    }
}
