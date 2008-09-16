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
 * Date:21/08/08
 * Fixed configFile & renamed to configPath, was not
 * working properly due to an silly oversight, will
 * now build properly and retrieve settings.ini
 * 
 * Date:19/08/08
 * Made getParam private, seeing as it should
 * only be used internally.
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

class TestConfigSettings {
	
	/**
	 * Used to store our configurations
	 *
	 * @var Zend_Config
	 */
	static protected $_config;
	
	/**
	 * Sets up our configurations, retrieves settings
	 * and registers them to zend.
	 *
	 * @param String $env  The settings environment 
	 *                      we want to configure.
	 * 
	 */
    static function setUpConfig() {
        $root = realpath(dirname(__FILE__) . '/../../configs/'); // smelly, could be anything
        $configPath = $root .'/settings.ini';          
        $general = new Zend_Config_Ini( $configPath, 'general');
        self::$_config = new Zend_Config_Ini( $configPath, $general->environment);
        Zend_Registry::set('config',self::$_config);
    }
    
    static function getDBParams($config) {
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
        $params = self::getDBParams($config);
        $db = Zend_Db::factory($adapter, $params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db',$db);
    }
}