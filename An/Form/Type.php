<?php
namespace An\Form;

use Tk\Form;
use Tk\Form\Event;
use Tk\Form\Field;

/**
 * Example:
 * <code>
 *   $form = new CompanyCategory::create();
 *   $form->setModel($obj);
 *   $formTemplate = $form->getRenderer()->show();
 *   $template->appendTemplate('form', $formTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2019-06-06
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Type extends \App\FormIface
{

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->appendField(new Field\Input('name'));
        $this->appendField(new Field\MinMax('min', 'max'));
        $this->appendField(new Field\Textarea('description'));
        $this->appendField(new Field\Textarea('notes'));

        $this->appendField(new Event\Submit('update', array($this, 'doSubmit')));
        $this->appendField(new Event\Submit('save', array($this, 'doSubmit')));
        $this->appendField(new Event\Link('cancel', $this->getConfig()->getBackUrl()));

    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function execute($request = null)
    {
        $this->load(\An\Db\TypeMap::create()->unmapForm($this->getType()));
        parent::execute($request);
    }

    /**
     * @param Form $form
     * @param Event\Iface $event
     * @throws \Exception
     */
    public function doSubmit($form, $event)
    {
        // Load the object with form data
        \An\Db\TypeMap::create()->mapForm($form->getValues(), $this->getType());

        // Do Custom Validations

        $form->addFieldErrors($this->getType()->validate());
        if ($form->hasErrors()) {
            return;
        }
        
        $isNew = (bool)$this->getType()->getId();
        $this->getType()->save();

        // Do Custom data saving

        \Tk\Alert::addSuccess('Record saved!');
        $event->setRedirect($this->getBackUrl());
        if ($form->getTriggeredEvent()->getName() == 'save') {
            $event->setRedirect(\Tk\Uri::create()->set('typeId', $this->getType()->getId()));
        }
    }

    /**
     * @return \Tk\Db\ModelInterface|\An\Db\Type
     */
    public function getType()
    {
        return $this->getModel();
    }

    /**
     * @param \An\Db\Type $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setModel($type);
    }
    
}