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
     * Check the user has access to this controller
     *
     * @param \Tk\Event\ControllerEvent $event
     */
    public function onBuildForm(\Tk\Event\ControllerEvent $event)
    {
        $plugin = Plugin::getInstance();
        $config = $plugin->getConfig();
        //$config->getLog()->info($plugin->getName() . ': onControllerAccess(\'profile\', '.$this->profileId.') ');

        /** @var \App\Controller\Placement\ReportEdit $controller */
        $controller = $event->getController();

        if ($controller->getUser()->isStaff()) {
            $form = $controller->getForm();

            $typesList = \An\Db\TypeMap::create()->findFiltered(array(
                'profileId' => $controller->getPlacement()->getCourse()->profileId
            ));

            $form->addField(new \Tk\Form\Field\Checkbox('nonAnimal'))->setFieldset('Animal Types')->setNotes('Is this a non-animal placement?');
            $form->addField(new \An\Form\Field\Animals('animals', $typesList, $controller->getPlacement()))->setFieldset('Animal Types');

        }

    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \App\UiEvents::PLACEMENT_REPORT_BUILD_FORM => array('onBuildForm', 0)
        );
    }
    
}