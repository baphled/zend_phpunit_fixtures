<?php
/**
 * My new Zend Framework project
 * 
 * @author  
 * @version 
 */

set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . './application/default/models/' . PATH_SEPARATOR . get_include_path());

require 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Layout.php';

// Setup controller
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory(
	array(
		'default' => '../application/default/controllers',
		'api' => '../application/api/controllers'
	)
);
$controller->throwExceptions(false); // should be turned on in development time 

// bootstrap layouts
Zend_Layout::startMvc(array(
    'layoutPath' => '../application/default/layouts',
    'layout' => 'main'
	));

// run!
$controller->dispatch();
