<?php
namespace An\Controller;

use App\Controller\AdminManagerIface;
use Dom\Template;
use Tk\Form\Field;
use Tk\Request;



/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
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
     * @throws \Tk\Form\Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        if (!$this->profile && $this->getCourse())
            $this->profile = $this->getCourse()->getProfile();


        $this->table = \App\Config::getInstance()->createTable(\Tk\Object::basename($this).'_reportingList');
        $this->table->setRenderer(\App\Config::getInstance()->createTableRenderer($this->table));

        $this->table->addCell(new \Tk\Table\Cell\Text('name'))->addCss('key');
        $this->table->addCell(new \Tk\Table\Cell\Text('total'))->setLabel('Animals');
        $this->table->addCell(new \Tk\Table\Cell\Text('count'))->setLabel('Placements');

        // Filters
        //$this->table->addFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Keywords');
        $list = array('This Course' => 'course', 'All Courses' => 'profile');
        $this->table->addFilter(new Field\Select('courseOnly', $list));
        $this->table->addFilter(new Field\Input('dateStart'))->addCss('date')->setAttr('placeholder', 'Start Date');
        $this->table->addFilter(new Field\Input('dateEnd'))->addCss('date')->setAttr('placeholder', 'End Date');

        // Actions
        //$this->table->addAction(\Tk\Table\Action\ColumnSelect::create()->setDisabled(array('name', 'total')));
        $this->table->addAction(\Tk\Table\Action\Csv::create());

        $this->table->setList($this->getList());

    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getList()
    {
        $filter = $this->table->getFilterValues();
        $filter['profileId'] = $this->profile->getId();
        if (!isset($filter['courseOnly']) || $filter['courseOnly'] == 'course') {
            $filter['courseId'] = $this->getCourse()->getId();
        }
        $typeList = \An\Db\ValueMap::create()->findTotals($filter, $this->table->makeDbTool('a.order_by'));
        return $typeList;
    }

    /**
     * @throws \Exception
     * @throws \Tk\Db\Exception
     */
    public function getListNew()
    {
        $db = $this->getConfig()->getDb();


        // Cells Required:     | companyName | isAcademic | animalName | avgUnits | numPlacements | numAnimals |
        //                     ---------------------------------------------------------------------------------

        // \Tk\Db\Tool::clearSession();
        $tool = $this->table->getDbTool('d.`name`, a.`name`', 0);

        $filter = $this->table->getFilterValues();
        $filter['courseId'] = $this->term->courseId;
        $filter['termId'] = $this->term->id;

        $where = '';

        if (!empty($filter['companyId'])) {
            $where .= sprintf('c.companyId = %d AND ', (int)$filter['companyId']);
        }
        if (!empty($filter['termId'])) {
            $where .= sprintf('c.termId = %d AND ', (int)$filter['termId']);
        } else {
            if (!empty($filter['courseId'])) {
                $where .= sprintf('b.courseId = %d AND ', (int)$filter['courseId']);
            }
        }

        if (!empty($filter['dateFrom']) && !empty($filter['dateTo'])) {     // Contains
            $dtef = $filter['dateFrom'];
            $dtet = $filter['dateTo'];
            $where .= sprintf('c.`dateTo` >= %s AND ', $db->quote($dtef->floor()->toString()) );
            $where .= sprintf('c.`dateTo` <= %s AND ', $db->quote($dtet->floor()->toString()) );
        }
        if ($where) {
            $where = substr($where, 0, -4);
        }

        // Query
        $toolStr = '';
        if ($tool) {
            $toolStr = $tool->getSql();
        }

        $sql = sprintf('SELECT d.`name` as \'companyName\', a.`name` as \'species\', ROUND(AVG(c.`units`), 1) as \'duration\', 
            COUNT(c.`id`) as \'rotationCount\', 1 as \'studentPerRotation\', SUM(a.`value`) AS \'animalCount\', e.isAcademic
FROM animalsValue a, animalsType b, placement c, company d,
 (
   SELECT a.id, c.academicAssociate as \'isAcademic\'
   FROM company a, company_supervisor b, supervisor c
   WHERE a.id = b.companyId AND b.supervisorId = c.id
   GROUP BY a.id
 ) e

WHERE a.`typeId` = b.`id` AND a.`typeId` > 0 AND b.`del` = 0 AND a.`placementId` = c.`id` AND 
      c.`companyId` = d.`id` AND d.id = e.id AND c.`del` = 0 AND d.`del` = 0 AND %s
GROUP BY c.`companyId`, a.`typeId`
%s', $where, $toolStr);


        $res = $db->query($sql);
        return $res->fetchAll(\PDO::FETCH_ASSOC);
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

