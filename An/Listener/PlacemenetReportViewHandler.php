<?php
namespace An\Listener;

use Tk\Event\Subscriber;
use An\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class PlacemenetReportViewHandler implements Subscriber
{

    /**
     * @var \App\Controller\Placement\ReportEdit
     */
    private $controller = null;


    /**
     * @param \Tk\Event\Event $event
     * @throws \Dom\Exception
     * @throws \Tk\Db\Exception
     */
    public function onControllerInit(\Tk\Event\Event $event)
    {
        /** @var \App\Controller\Student\Placement\View $controller */
        $controller = $event->get('controller');
        if ($controller instanceof \App\Controller\Student\Placement\View) {
            $this->controller = $controller;
            $view = $this->controller->getReportView();
            $template = $view->getTemplate();
            $report = $view->getReport();
            $placement = $report->getPlacement();


            $list = \An\Db\ValueMap::create()->findFiltered(array('placementId' => $placement->getId()));
            if(count($list) == 1 && current($list)->name == '') {
                // Non-Annimal placement
                $template->appendHtml('report-info',
                    sprintf('<dt>Animal Types:</dt> <dd>Non-Animal</dd>'));
            } else {
                $animalView = \An\Ui\AnimalList::create($list);

                $template->appendHtml('report-info',
                    sprintf('<dt>Animal Types:</dt> <dd>%s</dd>', $animalView->show()->toString()));
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
            \Tk\PageEvents::CONTROLLER_INIT => array('onControllerInit', 0),
            \Tk\PageEvents::CONTROLLER_SHOW => array('onControllerShow', 0)
        );
    }

}