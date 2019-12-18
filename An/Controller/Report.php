<?php
namespace An\Controller;

use Dom\Template;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Report extends \App\Controller\AdminManagerIface
{

    /**
     * @var \App\Db\Subject
     */
    private $subject = null;

    /**
     * @var \App\Db\Course
     */
    private $course = null;


    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Animal Type Report');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->course = $this->getConfig()->getCourseMapper()->find($request->get('courseId'));
        $this->subject = $this->getConfig()->getSubject();
        if (!$this->course && $this->subject)
            $this->course = $this->subject->getCourse();

        $this->setTable(\An\Table\Report::create());
        $this->getTable()->init();

        $filter = array(
            'courseId' => $this->course->getId(),
            'subjectId' => $this->subject->getId()
        );
        $this->getTable()->setList($this->getTable()->findList($filter));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->appendTemplate('panel', $this->getTable()->show());

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
<div class="tk-panel" data-panel-title="Animal Type Report" data-panel-icon="fa fa-paw" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}

