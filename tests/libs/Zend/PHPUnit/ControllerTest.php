<?php
/**
 * 
 * Zend_PHPUnit_ControllerTest
 *
 * Bootstrapper for Controller tests, integrates Initializer which determines the development environment.
 * 
 * @author Yomi (baphled) Akindayini <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright GPL 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage Zend_PHPUnit_ControllerTest
 *
 */

class Zend_PHPUnit_ControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	/**
	 * Construct our Controller.
	 * 
	 * @access public
	 * @return void
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Assigns our bootstrap.
	 *
	 * @access public
	 * @return void
	 *
	 */
	protected function setUp() {
		$this->bootstrap = array ($this, 'appBootstrap' );
		parent::setUp();
	}
	
	/**
	 * Registers Initializer to the Controller.
	 *
	 * @access public
	 * @return void
	 *
	 */
	public function appBootstrap() {		
		$this->frontController->registerPlugin(new Initializer());		
	}
}
