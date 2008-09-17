<?php
// Setup controller
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory(
	array(
		'default' => realpath(dirname(__FILE__) . '/../../application/default/controllers'),
		'api' => realpath(dirname(__FILE__) . '/../../application/api/controllers'),
		'features' => realpath(dirname(__FILE__) . '/../../application/features/controllers')
	)
);

$layoutPath = realpath(dirname(__FILE__) . '/../../application/default/layouts');
Zend_Layout::startMvc(array(
     'layoutPath' => $layoutPath,
     'layout' => 'main'
));