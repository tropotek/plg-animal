<?php
namespace An\Controller\Type;

use Dom\Template;
use Tk\Request;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \App\Controller\AdminManagerIface
{

    /**
     * @var \App\Db\Course
     */
    private $course = null;


    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Animal Type Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->course = $this->getConfig()->getCourseMapper()->find($request->get('courseId'));
        /** @var \App\Db\Subject $subject */
        $subject = $this->getConfig()->getSubject();
        if (!$this->course && $subject)
            $this->course = $subject->getCourse();

        $this->setTable(\An\Table\Type::create());
        $this->getTable()->setEditUrl(\Uni\Uri::createHomeUrl('/animalTypeEdit.html'));
        $this->getTable()->init();

        $filter = array(
            'courseId' => $this->course->getId()
        );
        $this->getTable()->setList($this->getTable()->findList($filter));

    }

    /**
     *
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Type',
            $this->getTable()->getEditUrl()->set('courseId', $this->course->getId()), 'fa fa-paw fa-add-action'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        $template->appendTemplate('panel', $this->table->getRenderer()->show());

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
<div class="tk-panel" data-panel-title="Animal Types" data-panel-icon="fa fa-paw" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }


}

