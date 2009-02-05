<?php
/**
 * TestConfigSettings
 * 
 * Basic little class that handles all our config settings, we basically
 * call each method when we need setup a particular setting.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 * Date:21/08/08
 * Fixed configFile & renamed to configPath, was not
 * working properly due to an silly oversight, will
 * now build properly and retrieve settings.ini
 * 
 * Date:19/08/08
 * Made getParam private, seeing as it should
 * only be used internally.
 * 
 */
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

class Zend_ConfigSettings {
	
	/**
	 * Used to store our configurations
	 *
	 * @var    Zend_Config
	 */
	static protected $_config;
	static public $_currentEnv;
	static protected $_environments;
	/**
	 * Checks if the environment has been set in the registry
	 * 
	 * @return 	boolean
	 */
	static private function _hasEnvironment() {
        //$environments = array('staging', 'local', 'development', 'production', 'test');
        
        foreach (self::getEnvironments() as $environment) {
	        if (Zend_Registry::isRegistered($environment)) {
			    return true;
			}
        }
        return false;
	}
	
	static function getEnvironments($file = '/../../configs/environments.ini') {
		self::$_environments = new Zend_Config_Ini(realpath(dirname(__FILE__) .$file));
		
		foreach(self::$_environments->toArray() as $environment=>$values) {
			$environments[] = $environment;
		}
		return $environments;
	}

	/**
	 * Sets Registry Settings
	 *
	 * @param string $path
	 * @param string $env
	 */
	static function _setRegistry($path, $env) {
		self::$_config = new Zend_Config_Ini($path, $env);
	    Zend_Registry::set($env, self::$_config);
	}

	/**
	 * Sets the configuration path.
	 * 
	 * @access private
	 * @param  $path           Configurations path.
	 * @param  $file           Name of configuration file.
	 * @return $configPath     The whole configuration path in string format.
	 * 
	 */
    static function _setPath($path, $file) {
        $root = realpath(dirname(__FILE__) . $path); // smelly, could be anything
        $configPath = realpath($root . '/'.$file);
        
        return $configPath;
    }
    
	/**
	 * Sets up our configurations, retrieves settings
	 * and registers them to zend.
	 *
	 * @access public
	 * @param  String $path Path of configuration direction in relation to working directory.
	 * @param  String $file Configuration file name.
	 * 
	 */
    static public function setUpConfig($path='/../../configs', $file='') {           
        if (!Zend_Registry::isRegistered('general')) {
        	self::setUpConfigEnv('general', '/../../configs', '/settings.ini'); 
        }
              
        if (!self::_hasEnvironment()) {
	        $general = Zend_Registry::get('general');
	        $file = (empty($file)) ? 'environments.ini' : $file;  
	        $configPath = self::_setPath($path, $file);
	        self::_setRegistry($configPath, $general->environment);	        	        
        }
    }
    
    /**
     * Sets up our configuration settings depending on environment
     * will be needed when we want to specify config settings for a
     * particular situation. ie. needing a diff DB for development.
     * 
     * @access  public
     * @param   String $env   Environment used for our tests.
     * @param   String $path  Configurations path (relative to working directory).
     * @param   String $file  Configuration file name.
     * 
     */
    static public function setUpConfigEnv($env='development', $path='/../../configs', $file='/environments.ini') {
       	$configPath = self::_setPath($path, $file); 
       	self::$_currentEnv = $env;
        self::_setRegistry($configPath, $env);	  
    }
    
    /**
     * Sets up our timezone, using our configuration
     * settings.
     * 
     * @access  public
     * @return  String
     * 
     */
    static public function setupTimeZone() {
    	$flag = false;
    	self::setUpConfig();
    	$tmz = self::$_config->timezone;
        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            if ($timezone === $tmz) {
                $flag = true;
            }
        }
        if ($flag === false) {
            throw new ErrorException('Time zone invalid.');
        }
        return $tmz;
    }
    
    /**
     * Returns our DB parameters from out configuration file.
     * 
     * @access  public
     * @param   Zend_Config $config    	Our zend configuration object
     * @return  Array       $params		Our DB configuration parameters.
     * 
     */
    static public function getDBParams($config) {
        $params = array( 
                    'host'     => $config->database->hostname,
                    'username' => $config->database->username,
                    'password' => $config->database->password,
                    'dbname'   => $config->database->database);
        return $params;
    }
    
    /**
     * Sets up our DB adapter, if none are found
     * we use PDO MySQL.
     * 
     * @access  public
     * 
     */
    static public function setUpDBAdapter() {
    	$config = self::$_config;
    	if (null === $config->database->type ) {
    		$adapter = 'PDO_MYSQL';
    	} else {
    		$adapter = $config->database->type;
    	}
    	
        $params = self::getDBParams($config);
        $db = Zend_Db::factory($adapter, $params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }
}