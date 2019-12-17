<?php
namespace An\Controller\Type;

use Dom\Template;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends \App\Controller\AdminEditIface
{

    /**
     * @var \An\Db\Type
     */
    protected $type = null;

    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Animal Type Edit');
    }

    /**
     *
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->type = new \An\Db\Type();
        $this->type->setCourseId((int)$request->get('courseId'));
        if ($request->get('typeId')) {
            $this->type = \An\Db\TypeMap::create()->find($request->get('typeId'));
        }

        $this->setForm(\An\Form\Type::create()->setModel($this->type));
        $this->initForm($request);
        $this->getForm()->execute();
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        // Render the form
        $template->appendTemplate('panel', $this->getForm()->show());

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
<div class="tk-panel" data-panel-title="Animal Type Edit" data-panel-icon="fa fa-paw" var="panel"></div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}