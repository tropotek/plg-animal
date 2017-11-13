<?php
namespace An;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Plugin extends \App\Plugin\Iface
{

    /**
     * A helper method to get the Plugin instance globally
     *
     * @return \Tk\Plugin\Iface
     */
    static function getInstance()
    {
        return \Tk\Config::getInstance()->getPluginFactory()->getPlugin('ems-animal');
    }

    /**
     * Init the plugin
     *
     * This is called when the session first registers the plugin to the queue
     * So it is the first called method after the constructor...
     */
    function doInit()
    {
        include dirname(__FILE__) . '/config.php';

        // Register the plugin for the different client areas if they are to be enabled/disabled/configured by those roles.
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_INSTITUTION);
        $this->getPluginFactory()->registerZonePlugin($this, self::ZONE_COURSE_PROFILE);
        //$this->getPluginFactory()->registerZonePlugin($this, self::ZONE_COURSE);

        \App\Factory::getEventDispatcher()->addSubscriber(new \Skill\Listener\SetupHandler());
    }
    
    /**
     * Activate the plugin, essentially
     * installing any DB and settings required to run
     * Will only be called when activating the plugin in the
     * plugin control panel
     */
    function doActivate()
    {
        // Init Plugin Settings
        $config = \Tk\Config::getInstance();
        $db = \App\Factory::getDb();

        $migrate = new \Tk\Util\SqlMigrate($db);
        $migrate->setTempPath($config->getTempPath());
        $migrate->migrate(dirname(__FILE__) . '/sql');



    }

    /**
     * Deactivate the plugin removing any DB data and settings
     * Will only be called when deactivating the plugin in the
     * plugin control panel
     */
    function doDeactivate()
    {
        // TODO: Implement doDeactivate() method.
        $db = \App\Factory::getDb();


    }


}