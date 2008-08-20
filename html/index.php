<?php
/**
 * My new Zend Framework project
 * 
 * @author  
 * @version 
 */

set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . './application/default/models/' . PATH_SEPARATOR . get_include_path());
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Layout.php';

// Setup controller
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory('../application/default/controllers');
$controller->throwExceptions(false); // should be turned on in development time 

// bootstrap layouts
Zend_Layout::startMvc(array(
    'layoutPath' => '../application/default/layouts',
    'layout' => 'main'
	));

// run!
$controller->dispatch();
