<?php
/**
 * Simple test suite, allowsing us to test our Fixtures specific
 * test cases.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package TestSuite_PHPUnit_FixturesManager
 *
 * $LastChangedBy$
 * 
 */
require_once dirname(__FILE__) .'/../../../tests/TestHelper.php';

require_once 'FixtureTest.php';
require_once 'FixturesManagerTest.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

/**
 * Static test suite.
 */
class PHPUnitFixturesUnitSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'PHPUnitFixturesUnitSuite' );
		
		$this->addTestSuite ( 'FixturesManagerTest' );
		$this->addTestSuite ( 'FixtureTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}