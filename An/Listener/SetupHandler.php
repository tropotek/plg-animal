<?php
namespace An\Listener;

use Tk\Event\Subscriber;
use Symfony\Component\HttpKernel\KernelEvents;
use An\Plugin;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class SetupHandler implements Subscriber
{

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @throws \Tk\Db\Exception
     * @throws \Tk\Exception
     */
    public function onRequest($event)
    {
        /* NOTE:
         *  If you require the Institution object for an event
         *  be sure to subscribe events here.
         *  As any events fired before this event do not have access to
         *  the institution object, unless you manually save the id in the
         *  session on first page load?
         */
        $dispatcher = \App\Config::getInstance()->getEventDispatcher();
        $plugin = Plugin::getInstance();

//        $institution = \Uni\Config::getInstance()->getInstitution();
//        if($institution && $plugin->isZonePluginEnabled(Plugin::ZONE_INSTITUTION, $institution->getId())) {
//            \Tk\Log::debug($plugin->getName() . ': Sample init client plugin stuff: ' . $institution->name);
//            $dispatcher->addSubscriber(new \Ems\Listener\ExampleHandler(Plugin::ZONE_INSTITUTION, $institution->getId()));
//        }

//        $subject = \Uni\Config::getInstance()->getSubject();
//        if ($subject && $plugin->isZonePluginEnabled(Plugin::ZONE_SUBJECT, $subject->getId())) {
//            \Tk\Log::debug($plugin->getName() . ': Sample init subject plugin stuff: ' . $subject->name);
//            $dispatcher->addSubscriber(new \Ems\Listener\ExampleHandler(Plugin::ZONE_SUBJECT, $subject->getId()));
//        }

        $profile = \App\Config::getInstance()->getProfile();
        if ($profile && $plugin->isZonePluginEnabled(Plugin::ZONE_SUBJECT_PROFILE, $profile->getId())) {
            //\Tk\Log::debug($plugin->getName() . ': Animals init subject profile plugin stuff: ' . $profile->name);
            $dispatcher->addSubscriber(new \An\Listener\ProfileEditHandler());

            if (\Uni\Config::getInstance()->getSubject()) {
                $dispatcher->addSubscriber(new \An\Listener\StaffSideMenuHandler());
                $dispatcher->addSubscriber(new \An\Listener\ReportEditHandler());
                $dispatcher->addSubscriber(new \An\Listener\PlacemenetViewHandler());
//                $dispatcher->addSubscriber(new \An\Listener\PlacementManagerButtonHandler($subject));
//                $dispatcher->addSubscriber(new \An\Listener\SubjectUserListButtonHandler($subject));
            }
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
            KernelEvents::REQUEST => array('onRequest', -10)
        );
    }
    
    
}