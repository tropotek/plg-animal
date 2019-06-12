<?php
namespace An\Table;


/**
 * Example:
 * <code>
 *   $table = new Domain::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2019-01-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Report extends \App\TableIface
{

    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {

        $this->appendCell(new \Tk\Table\Cell\Text('companyName'))->addCss('key');
        $this->appendCell(new \Tk\Table\Cell\Boolean('academic'));
        $this->appendCell(new \Tk\Table\Cell\Text('species'));
        $this->appendCell(new \Tk\Table\Cell\Text('placementCount'))->setLabel('Placements');
        $this->appendCell(new \Tk\Table\Cell\Text('units'))->setLabel('Total ' . $this->getConfig()->getProfile()->unitLabel);
        $this->appendCell(new \Tk\Table\Cell\Text('duration'))->setLabel('Avg. Duration');
        //$this->appendCell(new \Tk\Table\Cell\Text('studentPerRotation'));
        $this->appendCell(new \Tk\Table\Cell\Text('animalCount'))->setLabel('Patients Examined');

        // Filters
        $this->appendFilter(new \Tk\Form\Field\Input('dateStart'))->addCss('date')->setAttr('placeholder', 'Start Date');
        $this->appendFilter(new \Tk\Form\Field\Input('dateEnd'))->addCss('date')->setAttr('placeholder', 'End Date');

        // Actions
        $this->appendAction(\Tk\Table\Action\Csv::create());


        return $this;
    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\stdClass[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool('d.name, a.name');
        $filter = array_merge($this->getFilterValues(), $filter);


        $db = $this->getConfig()->getDb();

        // Cells Required:     | companyName | isAcademic | animalName | avgUnits | numPlacements | numAnimals |
        //                     ---------------------------------------------------------------------------------
        $where = '';

        if (!empty($filter['companyId'])) {
            $where .= sprintf('c.company_id = %d AND ', (int)$filter['companyId']);
        }
        if (!empty($filter['subjectId'])) {
            $where .= sprintf('c.subject_id = %d AND ', (int)$filter['subjectId']);
        } else {
            if (!empty($filter['profileId'])) {
                $where .= sprintf('b.profile_id = %d AND ', (int)$filter['profileId']);
            }
        }

//        if (!empty($filter['dateStart']) && !empty($filter['dateEnd'])) {     // Contains
//            $start = \Tk\Date::create($filter['dateStart']);
//            $end = \Tk\Date::create($filter['dateEnd']);
//            $where .= sprintf('c.date_start >= %s AND ', $db->quote(\Tk\Date::floor($start)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
//            $where .= sprintf('c.date_start <= %s AND ', $db->quote(\Tk\Date::ceil($end)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
//        }

        if (!empty($filter['dateStart'])) {     // starts with
            $start = \Tk\Date::createFormDate($filter['dateStart']);
            $where .= sprintf('c.date_start >= %s AND ', $db->quote(\Tk\Date::floor($start)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if (!empty($filter['dateEnd'])) {
            $end = \Tk\Date::createFormDate($filter['dateEnd']);
            $where .= sprintf('c.date_start <= %s AND ', $db->quote(\Tk\Date::ceil($end)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }

        if ($where) {
            $where = substr($where, 0, -4);
        }

        // Query
        $toolStr = '';
        if ($tool) {
            $toolStr = $tool->toSql();
        }

        $sql = sprintf('SELECT SQL_CALC_FOUND_ROWS d.name as \'companyName\', a.name as \'species\', ROUND(AVG(c.units), 1) as \'duration\', SUM(c.units) as \'units\', 
            COUNT(c.id) as \'placementCount\', SUM(a.value) AS \'animalCount\', e.academic
FROM animal_value a, animal_type b, placement c, company d LEFT JOIN
 (
   SELECT a.id, IFNULL(c.academic, 0) as \'academic\'
   FROM company a, company_has_supervisor b, supervisor c
   WHERE a.id = b.company_id AND b.supervisor_id = c.id
   GROUP BY a.id
 ) e ON (d.id = e.id)

WHERE a.type_id = b.id AND a.type_id > 0 AND b.del = 0 AND a.placement_id = c.id AND
      c.company_id = d.id AND c.del = 0 AND d.del = 0 AND %s
GROUP BY c.company_id, a.type_id
%s', $where, $toolStr);


        $res = $db->query($sql);
        //vd($sql);
        return \Tk\Db\Map\ArrayObject::create($res, $tool);
    }

}