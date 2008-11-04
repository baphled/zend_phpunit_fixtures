<?php
/**
 * Simple test suite, allowsing us to test our Fixtures specific
 * test cases.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 * $LastChangedBy$
 * 
 */
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

require_once 'FixturesManagerTest.php';
require_once 'PHPUnitFixtureDynamicDBTest.php';
require_once 'DevelopmentHandlerTest.php';
require_once 'FixtureTest.php';
require_once 'FixtureDBTest.php';

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
		
		$this->addTestSuite ( 'DevelopmentHandlerTest' );
		$this->addTestSuite ( 'FixturesManagerTest' );
		$this->addTestSuite ( 'FixtureTest' );
		$this->addTestSuite ( 'PHPUnitFixtureTransactionsTest' );
		$this->addTestSuite ( 'FixtureDBTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}
