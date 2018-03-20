<?php
namespace An\Controller\Type;

use App\Controller\AdminEditIface;
use Dom\Template;
use Tk\Form\Event;
use Tk\Form\Field;
use Tk\Request;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends AdminEditIface
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
        parent::__construct();
        $this->setPageTitle('Animal Type Edit');
    }

    /**
     *
     * @param Request $request
     * @throws \Exception
     * @throws \Tk\Db\Exception
     * @throws \Tk\Form\Exception
     */
    public function doDefault(Request $request)
    {
        $this->type = new \An\Db\Type();
        $this->type->profileId = (int)$request->get('profileId');
        if ($request->get('typeId')) {
            $this->type = \An\Db\TypeMap::create()->find($request->get('typeId'));
        }

        $this->buildForm();

        $this->form->load(\An\Db\TypeMap::create()->unmapForm($this->type));
        $this->form->execute($request);
    }

    /**
     * @throws \Tk\Exception
     * @throws \Tk\Form\Exception
     */
    protected function buildForm() 
    {
        $this->form = \App\Config::getInstance()->createForm('animalTypeEdit');
        $this->form->setRenderer(\App\Config::getInstance()->createFormRenderer($this->form));

        $this->form->addField(new Field\Input('name'));
        $this->form->addField(new \App\Form\Field\MinMax('min', 'max'));
        $this->form->addField(new Field\Textarea('description'));
        $this->form->addField(new Field\Textarea('notes'));

        $this->form->addField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->form->addField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->form->addField(new Event\Link('cancel', \Uni\Ui\Crumbs::getInstance()->getBackUrl()));

    }

    /**
     * @param \Tk\Form $form
     * @throws \ReflectionException
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function doSubmit($form)
    {
        // Load the object with data from the form using a helper object
        \An\Db\TypeMap::create()->mapForm($form->getValues(), $this->type);

        $form->addFieldErrors($this->type->validate());

        if ($form->hasErrors()) {
            return;
        }
        $this->type->save();

        \Tk\Alert::addSuccess('Record saved!');
        if ($form->getTriggeredEvent()->getName() == 'update') {
            \Uni\Ui\Crumbs::getInstance()->getBackUrl()->redirect();
        }
        \Tk\Uri::create()->set('typeId', $this->type->getId())->redirect();
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        // Render the form
        $template->insertTemplate('form', $this->form->getRenderer()->show());

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
      <h4 class="panel-title"><i class="fa fa-paw"></i> <span var="panel-title">Animal Type Edit</span></h4>
    </div>
    <div class="panel-body">
      <div var="form"></div>
    </div>
  </div>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}