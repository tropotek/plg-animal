<?php
$config = \Tk\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('An\\', dirname(__FILE__));

/** @var \Tk\Routing\RouteCollection $routes */
$routes = $config['site.routes'];
if (!$routes) return;

$params = array('role' => 'staff');

$routes->add('Animal Type Manager', new \Tk\Routing\Route('/staff/animalTypeManager.html', 'An\Controller\Type\Manager::doDefault', $params));
$routes->add('Animal Type Edit', new \Tk\Routing\Route('/staff/animalTypeEdit.html', 'An\Controller\Type\Edit::doDefault', $params));

$params['subjectCode'] = '';
$routes->add('Animal Type Report', new \Tk\Routing\Route('/staff/{subjectCode}/animalTypeReport.html', 'An\Controller\Report::doDefault', $params));



