<?php
/**
 * My new Zend Framework project
 * 
 * @author Yomi (baphled) Akindayini
 * @version $Id$
 
 */

set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . '../application/features/models/' . PATH_SEPARATOR . '../application/default/models/' . PATH_SEPARATOR . get_include_path());

require 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Layout.php';

// Setup controller
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory(
	array(
		'default' => '../application/default/controllers',
		'api' => '../application/api/controllers',
		'features' => '../application/features/controllers'
	)
);
$controller->throwExceptions(true); // should be turned on in development time 

// bootstrap layouts
Zend_Layout::startMvc(array(
    'layoutPath' => '../application/default/layouts',
    'layout' => 'main'
	));

$root = realpath(dirname(__FILE__) . '/../configs/'); // smelly, could be anything
$configPath = realpath($root .'/settings.ini'); 

$config = new Zend_Config_Ini($configPath, 'localmachine');

// setup Zend_Db_Table
$params = array( 'host'     => $config->database->hostname,
                 'username' => $config->database->username,
                 'password' => $config->database->password,
                 'dbname'   => $config->database->database);

$db = Zend_Db::factory('PDO_MYSQL', $params);
$db->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Registry::set('db', $db);
Zend_Db_Table::setDefaultAdapter($db);

// run!
$controller->dispatch();
