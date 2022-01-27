<?php
namespace An\Listener;

use Tk\Db\Tool;
use Tk\Event\Subscriber;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
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
     * @throws \Exception
     */
    public function onFormPreInit(\Tk\Event\FormEvent $event)
    {
        /** @var \App\Controller\Placement\ReportEdit $controller */
        $controller = $event->getForm()->get('controller');
        if ($controller && $controller instanceof \App\Controller\Placement\ReportEdit) {
            if ($controller->getSubject() && $controller->getPlacement()) {
                $this->animalTypes = \An\Db\TypeMap::create()
                    ->findFiltered(array('courseId' => $controller->getPlacement()->getSubject()->getCourseId()), Tool::create('order_by'));
                if (!$this->animalTypes->count()) return;
                $this->controller = $controller;
                $this->form = $controller->getForm();
            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     * @throws \Exception
     */
    public function onFormInit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $this->form->appendField(new \Tk\Form\Field\Checkbox('nonAnimal'))->setFieldset('Animal Types')
                ->setNotes('Is this a non-animal placement?<br/><em>(Note: Checking this box will delete any existing animal data)</em>');

            $this->form->appendField(new \An\Form\Field\AnimalSelect('animals', $this->animalTypes, $this->controller->getPlacement()))
                ->setFieldset('Animal Types')->setNotes('Report the species and number of cases you were involved with while on your '.
                    \App\Db\Phrase::findValue('placement', $this->controller->getPlacement()->getSubject()->getCourseId()).'.');

            $formRenderer = $this->form->getRenderer();
            $template = $formRenderer->getTemplate();

            $js = <<<JS
jQuery(function($) {
  
  $('.tk-animal-select').animalSelect();
  
  $('.tk-nonanimal input[type="checkbox"]').on('change', function () {
    if ($(this).prop('checked')) {
      $('.tk-animal-select').find('input, select, button').attr('disabled', 'disabled');
    } else {
      $('.tk-animal-select').find('input, select, button').removeAttr('disabled');
    }
  }).trigger('change');
  
});
JS;
            $template->appendJs($js, array('data-jsl-priority' => 10));

        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     * @throws \Exception
     */
    public function onFormLoad(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $valueList = \An\Db\ValueMap::create()->findFiltered(array('placementId' => $this->controller->getPlacement()->getId()));
            /** @var \An\Db\Value $currentValue */
            $currentValue = $valueList->current();
            $valueList->rewind();
            if ($currentValue && $currentValue->typeId == 0) {
                $this->form->setFieldValue('nonAnimal', true);
            } else {
                // Map to field value
                $vals = array(
                    'animals-typeId' => array(),
                    'animals-value' => array()
                );
                /** @var \An\Db\Value $value */
                foreach ($valueList as $value) {
                    $vals['animals-typeId'][] = $value->typeId;
                    $vals['animals-value'][] = $value->value;
                }
                $this->form->load($vals);

            }
        }
    }

    /**
     * @param \Tk\Event\FormEvent $event
     * @throws \Exception
     */
    public function onFormSubmit(\Tk\Event\FormEvent $event)
    {
        if ($this->form) {
            $placement = $this->controller->getPlacement();
            $list = $this->form->getFieldValue('animals');
            $nonAnimal = $this->form->getFieldValue('nonAnimal');

            // Check if animals are required
            if (!$nonAnimal && (!is_array($list) || !count($list))) {
                $this->form->addFieldError('animals', 'Please enter the type and number of animals.');
                $this->form->addError('Please enter the type and number of animals.');
            }

            if ($this->form->hasErrors()) {
                return;
            }

            // re-add all animals in the list
            if ($nonAnimal) {
                $valueObj = new \An\Db\Value();
                $valueObj->placementId = $placement->id;
                $valueObj->typeId = 0;
                $valueObj->name = '';
                $valueObj->notes = 'Non Animal Placement';
                $valueObj->save();
            } else {
                // Remove existing animals
                \An\Db\ValueMap::create()->removeAllByPlacementId($placement->id);
                foreach ($list as $typeId => $value) {
                    if (!$typeId || !$value) continue;
                    /** @var \An\Db\Type $type */
                    $type = \An\Db\TypeMap::create()->find($typeId);
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