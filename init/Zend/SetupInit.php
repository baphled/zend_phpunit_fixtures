<?php
/**
 * 
 * Generic setup initialising.
 * 
 * Static methods used to handle all our generic initialisations.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net>
 * @copyright 2008
 * @version $Id$
 * @package Zend_PHPUnit_Scaffolding
 *   
 */

class Zend_SetupInit {
	
	/**
	 * @access 	protected
     * @var 	Zend_Config
     * 
     */
    static protected $_general;
    
    /**
     * @access 	public
     * @var 	String	Current environment
     * 
     */
    static public $_env;
    
    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     * 
     * @access 	public
     * @param 	String	$path	Path to configuration file
     * @param 	String	$file	Configuration file.
     * @return 	void
     * 
     */
    static function setupInit($path=null,$file=null)
    {
        Zend_ConfigSettings::setUpConfig();
        self::$_general = Zend_Registry::get('general');
        self::_setEnv();	    
    }
    
    /**
     * Initialize environment
     * 
     * @access public
     * @return void
     * 
     */
    static protected function _setEnv() 
    {
    	self::$_env = self::$_general->environment;
    }
    
    /**
     * Configure Error Reporting, in local environment we want to report all errors,
     * otherwise we turn all errors off.
     * 
     * @access private
     * @return void
     * 
     */
    static function initErrorReporting() {
    	if ('local' === self::$_env) {
		    // Enable all errors so we'll know when something goes wrong. 
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_startup_errors', 1);  
			ini_set('display_errors', 1);
		} else {
			error_reporting(0);
		}
    }
}