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
     * @throws \Tk\Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        if (!$this->profile && $this->getCourse())
            $this->profile = $this->getCourse()->getProfile();


        $this->table = \App\Factory::createTable(\Tk\Object::basename($this).'_reportingList');
        $this->table->setParam('renderer', \App\Factory::createTableRenderer($this->table));

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
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->replaceTemplate('table', $this->table->getParam('renderer')->show());

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

