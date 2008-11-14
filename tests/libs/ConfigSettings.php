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

class ConfigSettings {
	
	/**
	 * Used to store our configurations
	 *
	 * @var    Zend_Config
	 */
	static protected $_config;

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
    static public function setUpConfig($path='/../../configs', $file='settings.ini') {
        $configPath = self::_setPath($path, $file);
        self::setUpConfigEnv('general');
        $general = self::$_config;
        self::$_config = new Zend_Config_Ini( $configPath, $general->environment);
        Zend_Registry::set('general', self::$_config);
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
    static public function setUpConfigEnv($env='development', $path='/../../configs', $file='/settings.ini') {
        $configPath = self::_setPath($path, $file);        
        self::$_config = new Zend_Config_Ini( $configPath, $env);
        Zend_Registry::set('config', self::$_config);
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
    	self::setUpConfigEnv('general');
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
    	if (null === $config->type ) {
    		$adapter = 'PDO_MYSQL';
    	}
        $params = self::getDBParams($config);
        $db = Zend_Db::factory($adapter, $params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }
}