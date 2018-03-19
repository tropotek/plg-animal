<?php
namespace An\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class TypeMap extends \App\Db\Mapper
{

    /**
     * @return \Tk\DataMap\DataMap
     * @throws \Tk\Db\Exception
     */
    public function getDbMap()
    {
        if (!$this->dbMap) {
            $this->setTable('animal_type');
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('profileId', 'profile_id'));
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
            $this->formMap->addPropertyMap(new Form\Integer('profileId'));
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
     * Find filtered records
     *
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject|Type[]
     * @throws \Tk\Db\Exception
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        if (!$tool) $tool = \Tk\Db\Tool::create('orderBy');

        list($from, $where) = $this->processFilter($filter);
        $r = $this->selectFrom($from, $where, $tool);
        return $r;
    }

    /**
     * @param $filter
     * @return array
     */
    protected function processFilter($filter)
    {
        $from = sprintf('%s a ', $this->quoteTable($this->getTable()));
        $where = '';

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.name LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.description LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['profileId'])) {
            $where .= sprintf('a.profile_id = %s AND ', (int)$filter['profileId']);
        }

        if (!empty($filter['name'])) {
            $where .= sprintf('a.name = %s AND ', $this->quote($filter['name']));
        }

        if (!empty($filter['dateFrom'])) {
            /** @var \DateTime $dtef */
            $dtef = \Tk\Date::floor($filter['dateFrom']);
            $where .= sprintf('a.created >= %s AND ', $this->quote($dtef->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if (!empty($filter['dateTo'])) {
            /** @var \DateTime $dtet */
            $dtet = \Tk\Date::ceil($filter['dateTo']);
            $where .= sprintf('a.created <= %s AND ', $this->quote($dtet->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) {
                $where .= '('. $w . ') AND ';
            }
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        $r = array($from, $where);
        return $r;

    }




}