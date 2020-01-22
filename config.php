<?php

$config = \App\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('An\\', dirname(__FILE__));

$routes = $config->getRouteCollection();
if (!$routes) return;

$params = array();
$routes->add('Animal Type Manager', new \Tk\Routing\Route('/staff/animalTypeManager.html', 'An\Controller\Type\Manager::doDefault', $params));
$routes->add('Animal Type Edit', new \Tk\Routing\Route('/staff/animalTypeEdit.html', 'An\Controller\Type\Edit::doDefault', $params));

$params['subjectCode'] = '';
$routes->add('Animal Type Report', new \Tk\Routing\Route('/staff/{subjectCode}/animalTypeReport.html', 'An\Controller\Report::doDefault', $params));




