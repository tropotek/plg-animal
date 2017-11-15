<?php
namespace An\Listener;

use Tk\Event\Subscriber;
use An\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ReportEditHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;

    /**
     * @var \Tk\Form
     */
    private $form = null;

    /**
     * @var \An\Db\Type[]|\Tk\Db\Map\ArrayObject
     */
    private $animalTypes = null;


    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormPreInit(\Tk\Event\FormEvent $event)
    {
        /** @var \App\Controller\Placement\ReportEdit $controller */
        $controller = $event->getForm()->get('controller');
        if ($controller instanceof \App\Controller\Placement\ReportEdit) {
            if ($controller->getUser()->isStaff() && $controller->getCourse() && $controller->getPlacement()) {
                $this->animalTypes = \An\Db\TypeMap::create()->findFiltered(array('profileId' => $controller->getPlacement()->getCourse()->profileId));
                if (!$this->animalTypes->count()) return;
                $this->controller = $controller;
                $this->form = $controller->getForm();
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormInit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $this->form->addField(new \Tk\Form\Field\Checkbox('nonAnimal'))->setFieldset('Animal Types')->setNotes('Is this a non-animal placement?<br/><em>(Note: Checking this box will delete any existing animal data)</em>');
            $this->form->addField(new \An\Form\Field\Animals('animals', $this->animalTypes, $this->controller->getPlacement()))->setFieldset('Animal Types')->setNotes('If this is an animal placement, add the type and number of animals seen.');

            $formRenderer = $this->form->getRenderer();
            $template = $formRenderer->getTemplate();
            $js = <<<JS
jQuery(function($) {
  
  var animalField = $('.tk-animals-field').animalField({}).data('animalField');
  var f = animalField.getElement().closest('.AnimalTypes').find('input[type=checkbox]').on('change', function (e) {
      animalField.enable(!$(this).prop('checked'));
  });
  animalField.enable(!f.prop('checked'));
  
});
JS;
            $template->appendJs($js, array('data-jsl-priority' => 10));


        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormLoad(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $valueList = \An\Db\ValueMap::create()->findFiltered(array('placementId' => $this->controller->getPlacement()->getId()));
            if ($valueList->current() && $valueList->current()->typeId == 0) {
                $this->form->setFieldValue('nonAnimal', true);
            } else {
                // Map to field value
                $vals = array();
                /** @var \An\Db\Value $value */
                foreach ($valueList as $value) {
                    $vals[$value->typeId] = $value->value;
                }
                $this->form->setFieldValue('animals', $vals);
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     */
    public function onFormSubmit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $placement = $this->controller->getPlacement();
            $list = $this->form->getFieldValue('animals');
            $nonAnimal = $this->form->getFieldValue('nonAnimal');

            // Check if animals are required
            if (!$nonAnimal && !count($list)) {
                $this->form->addFieldError('animals', 'Please enter the type and number of animals.');
                $this->form->addError('Please enter the type and number of animals.');
            }

            if ($this->form->hasErrors()) {
                return;
            }

            // Remove existing animals
            \An\Db\ValueMap::create()->removeAllByPlacementId($placement->id);

            // re-add all animals in the list
            if ($nonAnimal) {
                $valueObj = new \An\Db\Value();
                $valueObj->placementId = $placement->id;
                $valueObj->typeId = 0;
                $valueObj->name = '';
                $valueObj->notes = 'Non Animal Placement';
                $valueObj->save();
            } else {
                foreach ($list as $typeId => $value) {
                    /** @var \An\Db\Type $type */
                    $type = \An\Db\Type::getMapper()->find($typeId);
                    $valueObj = \An\Db\Value::create($placement, $type, $value);
                    $valueObj->save();
                }
            }

        }
    }

    /**
     * Check the user has access to this controller
     *
     * @param \Tk\Event\Event $event
     */
    public function onControllerShow(\Tk\Event\Event $event) { }

    /**
     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\Form\FormEvents::FORM_INIT => array(array('onFormPreInit', 0), array('onFormInit', 0)),
            \Tk\Form\FormEvents::FORM_LOAD => array('onFormLoad', 0),
            \Tk\Form\FormEvents::FORM_SUBMIT => array('onFormSubmit', 0),
            \Tk\PageEvents::CONTROLLER_SHOW => array('onControllerShow', 0)
        );
    }

}