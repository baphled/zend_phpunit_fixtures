<?php
/**
 *
 * modelTestCase
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: Aug 21, 2008
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR .realpath(dirname(__FILE__) .'/../libs/') . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR .dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../application/default/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class modelTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_model;
	
	public function __construct() {
		$this->setName ( 'modelTest Case' );
	}
	
	public function setUp() {
		$this->_setUpConfig ();
		parent::setUp ();
		$this->_model = new Model();
	}
	
	public function tearDown() {
		$this->_model = null;
		parent::tearDown ();
	}
	
	function testAssert() {
		$this->assertEquals(1,1);
		$this->assertNotNull($this->_model);
	}
}