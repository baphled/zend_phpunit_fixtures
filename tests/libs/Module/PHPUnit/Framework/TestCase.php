<?php
/**
 * Modules_PHPUnit_Framework_TestCase
 * 
 * Extends PHPUnit_Framework_TestCase, built to house our general
 * internals, which are needed to setup our DB & config.
 * 
 * We need to setup config before we initialise the DB
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package IntraBetXTestSuite
 * 
 * Date: 16/08/08
 * Subclassed PHPUnit_Extensions_Database_TestCase, which will
 * allow us to test the db directly, we will now setup, functionality
 * to connect to sqlite. Well run this in memory to speed up our testing.
 * 
 * Date: 14/08/08
 * Refactored to use TestConfigSettings, also added commenting
 * where needed.
 * Moved file to library, so that would be picked up automatically
 * 
 * Date: 10/08/08
 * Created Models parent testcase class, which will be used
 * by all model testcases.
 * 
 * 
 * @todo Should really seperate DB & config setups, as not
 *       all models need both or possibly either.
 *
 */
require_once './TestConfigSettings.php';

class Module_PHPUnit_Framework_TestCase  extends PHPUnit_Framework_TestCase {
    
	/**
	 * Our configuration file
	 */
	private $_config;
    
	/**
	 * gets our DB parameters from our configuration file.
	 * 
	 * @todo refactor, can remove the param and use $this->_config
	 * 
	 */
    protected function _getDBParams($config) {
        return TestConfigSettings::getDBParams($config);
    }
    
    /**
     * sets up the DB adapter, using TestConfigSettings
     */
    protected function _setUpDBAdapter() {
        TestConfigSettings::setUpDBAdapter();
    }
    
    /*
     * Sets up out configurations.
     */
    protected function _setUpConfig($env = 'development') {
        TestConfigSettings::setUpConfig($env);
    }
    
    /**
     * Prepares the environment before running a test.
     */
    public function setUp() {
        parent::setUp ();
        //$this->_setUpConfig();
        $this->_setUpDBAdapter();
        // need to create our test table
    }
    
    /**
     * Tear down our setup, we need to remove
     * our registery settings, sessions and
     * the such like should also be done here.
     * 
     */
    public function tearDown() {
    	Zend_Registry::_unsetInstance();
    	// need to remove our test table
    	parent::tearDown();
    }
}
 ?>