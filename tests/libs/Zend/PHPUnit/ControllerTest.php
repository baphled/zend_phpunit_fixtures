<?php
/**
 * 
 * Zend_PHPUnit_ControllerTest
 *
 * Bootstrapper for Controller tests, integrates Initializer which determines the development environment.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008-2009
 * @author Nadjaha Wohedally <nadjaha@ibetx.com>
 * @version $Id$
 * @copyright GPL 2008-2009
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

	/**
	 * Dispatch helper function
	 * 
	 * Takes our data payload, location and method type & dispatches
	 * to the appropriate location.
	 *
	 * @param Array 	$data		Parameters used to pass on to dispatch.
	 * @param String 	$location	Location of the dispatch.
	 * @param String 	$method		POST or GET.
	 * 
	 */
	public function doDispatch($data,$location,$method='POST') {
		$this->_request = $this->getRequest();
		$this->_request->setMethod($method)
		               ->setPost($data);
		$this->dispatch($location);
	}
}
