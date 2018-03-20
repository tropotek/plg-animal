<?php
namespace An\Controller;

use App\Controller\AdminManagerIface;
use Dom\Template;
use Tk\Form\Field;
use Tk\Request;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Report extends AdminManagerIface
{

    /**
     * @var \App\Db\Profile
     */
    private $profile = null;


    /**
     * Manager constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setPageTitle('Animal Type Report');
    }

    /**
     * @param Request $request
     * @throws \Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Form\Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        if (!$this->profile && $this->getSubject())
            $this->profile = $this->getSubject()->getProfile();

        $this->table = \App\Config::getInstance()->createTable(\Tk\Object::basename($this).'_reportingList');
        $this->table->setRenderer(\App\Config::getInstance()->createTableRenderer($this->table));

        $this->table->addCell(new \Tk\Table\Cell\Text('companyName'))->addCss('key');
        $this->table->addCell(new \Tk\Table\Cell\Boolean('academic'));
        $this->table->addCell(new \Tk\Table\Cell\Text('species'));
        $this->table->addCell(new \Tk\Table\Cell\Text('duration'))->setLabel('Avg. Duration');
        $this->table->addCell(new \Tk\Table\Cell\Text('rotationCount'))->setLabel('Rotations Per Year');
        $this->table->addCell(new \Tk\Table\Cell\Text('studentPerRotation'));
        $this->table->addCell(new \Tk\Table\Cell\Text('animalCount'))->setLabel('Patients Examined');

        // Filters
        $this->table->addFilter(new Field\Input('dateStart'))->addCss('date')->setAttr('placeholder', 'Start Date');
        $this->table->addFilter(new Field\Input('dateEnd'))->addCss('date')->setAttr('placeholder', 'End Date');

        // Actions
        $this->table->addAction(\Tk\Table\Action\Csv::create());

        $this->table->setList($this->getList());

    }

    /**
     * @return \Tk\Db\Map\ArrayObject
     * @throws \Exception
     * @throws \Tk\Db\Exception
     */
    public function getList()
    {
        $db = $this->getConfig()->getDb();

        // Cells Required:     | companyName | isAcademic | animalName | avgUnits | numPlacements | numAnimals |
        //                     ---------------------------------------------------------------------------------

        $tool = $this->table->getTool('d.name, a.name');
        $filter = $this->table->getFilterValues();
        $filter['profileId'] = $this->getSubject()->profileId;
        $filter['subjectId'] = $this->getSubject()->getId();

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

        if (!empty($filter['dateStart']) && !empty($filter['dateEnd'])) {     // Contains
            $start = $filter['dateStart'];
            $end = $filter['dateEnd'];
            $where .= sprintf('c.dateStart >= %s AND ', $db->quote(\Tk\Date::floor($start)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
            $where .= sprintf('c.dateStart <= %s AND ', $db->quote(\Tk\Date::ceil($end)->format(\Tk\Date::FORMAT_ISO_DATETIME)) );
        }
        if ($where) {
            $where = substr($where, 0, -4);
        }

        // Query
        $toolStr = '';
        if ($tool) {
            $toolStr = $tool->toSql();
        }

        $sql = sprintf('SELECT SQL_CALC_FOUND_ROWS d.name as \'companyName\', a.name as \'species\', ROUND(AVG(c.units), 1) as \'duration\', 
            COUNT(c.id) as \'rotationCount\', 1 as \'studentPerRotation\', SUM(a.value) AS \'animalCount\', e.academic
FROM animal_value a, animal_type b, placement c, company d,
 (
   SELECT a.id, c.academic as \'academic\'
   FROM company a, company_has_supervisor b, supervisor c
   WHERE a.id = b.company_id AND b.supervisor_id = c.id
   GROUP BY a.id
 ) e

WHERE a.type_id = b.id AND a.type_id > 0 AND b.del = 0 AND a.placement_id = c.id AND
      c.company_id = d.id AND d.id = e.id AND c.del = 0 AND d.del = 0 AND %s
GROUP BY c.company_id, a.type_id
%s', $where, $toolStr);


        $res = $db->query($sql);
        return \Tk\Db\Map\ArrayObject::create($res, $tool);
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->replaceTemplate('table', $this->table->getRenderer()->show());

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"><i class="fa fa-paw"></i> Animal Type Report</h4>
    </div>
    <div class="panel-body">
      <div var="table"></div>
    </div>
  </div>

</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}

