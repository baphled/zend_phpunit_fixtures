<?php
/**
 *
 * DataTypeCheckerTestCase
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: 02/09/2008
 * Will copy our test cases from FixturesManagerTest to here, seeing as we will
 * be refactoring its accompanying functionality to DataTypeChecker.
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../libs/' ) . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../application/default/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class DataTypeCheckerTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_datatypechecker;
	
	public function __construct() {
		$this->setName ( 'DataTypeCheckerTest Case' );
	}
	
	public function setUp() {
		$this->_setUpConfig ();
		parent::setUp ();
	}
	
	public function tearDown() {
		$this->_datatypechecker = null;
		parent::tearDown ();
	}
}