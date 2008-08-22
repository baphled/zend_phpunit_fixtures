<?php
/**
 * Functional_Zend_Test_PHPUnit_ControllerTestCase
 * 
 * Parent Functional test class, houses commonally used
 * functionality, ie initialising the bootstrap.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package TestSuiteCaliBetx
 * @subpackage FunctionalTestCase
 */
set_include_path('.' . PATH_SEPARATOR . dirname(__FILE__).'/../../library/' . PATH_SEPARATOR . dirname(__FILE__).'/../../application/default/models/' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

class Functional_Zend_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    
    public function appBootstrap()
    {
        $this->_frontController->registerPlugin(new BootstrapInitialize('home', realpath(dirname(__FILE__) .'/../../')));
    }
}