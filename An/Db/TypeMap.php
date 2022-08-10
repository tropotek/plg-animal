<?php
namespace An\Db;

use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Tk\Db\Map\ArrayObject;
use Tk\Db\Tool;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class TypeMap extends \App\Db\Mapper
{

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) {
            $this->setTable('animal_type');
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('courseId', 'course_id'));
            $this->dbMap->addPropertyMap(new Db\Text('name'));
            $this->dbMap->addPropertyMap(new Db\Text('description'));
            $this->dbMap->addPropertyMap(new Db\Integer('min'));
            $this->dbMap->addPropertyMap(new Db\Integer('max'));
            $this->dbMap->addPropertyMap(new Db\Text('notes'));
            $this->dbMap->addPropertyMap(new Db\Integer('orderBy', 'order_by'));
            $this->dbMap->addPropertyMap(new Db\Date('modified'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));
        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('courseId'));
            $this->formMap->addPropertyMap(new Form\Text('name'));
            $this->formMap->addPropertyMap(new Form\Text('description'));
            $this->formMap->addPropertyMap(new Form\Integer('min'));
            $this->formMap->addPropertyMap(new Form\Integer('max'));
            $this->formMap->addPropertyMap(new Form\Text('notes'));
            $this->formMap->addPropertyMap(new Form\Integer('orderBy'));
        }
        return $this->formMap;
    }
    /**
     * @param array|\Tk\Db\Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Type[]
     * @throws \Exception
     */
    public function findFiltered($filter, $tool = null)
    {
        return $this->selectFromFilter($this->makeQuery(\Tk\Db\Filter::create($filter)), $tool);
    }

    /**
     * @param \Tk\Db\Filter $filter
     * @return \Tk\Db\Filter
     */
    public function makeQuery(\Tk\Db\Filter $filter)
    {
        $filter->appendFrom('%s a ', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.name LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $filter->appendWhere('(%s) AND ', substr($w, 0, -3));
        }

        if (!empty($filter['id'])) {
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        if (!empty($filter['courseId'])) {
            $filter->appendWhere('a.course_id = %s AND ', (int)$filter['courseId']);
        }

        if (!empty($filter['name'])) {
            $filter->appendWhere('a.name = %s AND ', $this->quote($filter['name']));
        }

        if (!empty($filter['dateFrom'])) {
            /** @var \DateTime $dtef */
            $dtef = \Tk\Date::floor($filter['dateFrom']);
            $filter->appendWhere('a.created >= %s AND ', $this->quote($dtef->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if (!empty($filter['dateTo'])) {
            /** @var \DateTime $dtet */
            $dtet = \Tk\Date::ceil($filter['dateTo']);
            $filter->appendWhere('a.created <= %s AND ', $this->quote($dtet->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }


}