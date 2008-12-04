<?php
/**
 * 
 * ControllerTest instantiate the Ibetx Api Client
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright GPL 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage Zend_PHPUnit_ControllerTest
 *
 */

class PHPUnit_ControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	protected $_client;
	
	/**
	 * instantiate the Api Client
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->bootstrap = array ($this, 'appBootstrap' );
		parent::setUp();
	}
	
	/**
	 * Prepares the environment before running a test.
	 */
	public function appBootstrap() {		
		$this->frontController->registerPlugin(new Initializer());		
	}
}