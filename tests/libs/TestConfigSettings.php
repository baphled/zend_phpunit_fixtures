<?php
/**
 * TestConfigSettings
 * 
 * Basic little class that handles all our config settings, we basically
 * call each method when we need setup a particular setting.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package IntraBetxTestSuite
 *
 * Date:19/08/08
 * Made getParam private, seeing as it should
 * only be used internally.
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

class TestConfigSettings {
	
	static protected $_config;
	
    static function setUpConfig($env = 'development') {
        $root = realpath(dirname(__FILE__) . '/../');
        $configFile = $root .'/../configs/settings.ini';               // smell?
        self::$_config = new Zend_Config_Ini( $configFile, $env);
        Zend_Registry::set('config',self::$_config);
    }
    
    private static function _getDBParams($config) {
        $params = array( 'host'     => $config->database->hostname,
                 'username' => $config->database->username,
                 'password' => $config->database->password,
                 'dbname'   => $config->database->database);
        return $params;
    }
    
    static function setUpDBAdapter() {
    	$config = self::$_config;
    	if(null === $config->type ) {
    		$adapter = 'PDO_MYSQL';
    	}
        $params = self::_getDBParams($config);
        $db = Zend_Db::factory($adapter, $params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db',$db);
    }
}