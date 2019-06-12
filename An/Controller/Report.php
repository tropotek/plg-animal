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
     * @var \App\Db\Profile
     */
    private $profile = null;


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
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));

        $this->subject = $this->getConfig()->getSubject();
        if (!$this->profile && $this->subject)
            $this->profile = $this->subject->getProfile();


        $this->setTable(\An\Table\Report::create());
        $this->getTable()->init();

        $filter = array(
            'profileId' => $this->profile->getId(),
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

