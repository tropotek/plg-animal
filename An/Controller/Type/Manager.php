<?php
namespace An\Controller\Type;

use Dom\Template;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Manager extends \App\Controller\AdminManagerIface
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
        $this->setPageTitle('Animal Type Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->profile = \App\Db\ProfileMap::create()->find($request->get('profileId'));
        /** @var \App\Db\Subject $subject */
        $subject = $this->getConfig()->getSubject();
        if (!$this->profile && $subject)
            $this->profile = $subject->getProfile();

        $this->setTable(\An\Table\Type::create());
        $this->getTable()->setEditUrl(\App\Uri::createHomeUrl('/animalTypeEdit.html'));
        $this->getTable()->init();

        $filter = array(
            'profileId' => $this->profile->getId()
        );
        $this->getTable()->setList($this->getTable()->findList($filter));

    }

    /**
     *
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Type',
            $this->getTable()->getEditUrl()->set('profileId', $this->profile->getId()), 'fa fa-paw fa-add-action'));
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

