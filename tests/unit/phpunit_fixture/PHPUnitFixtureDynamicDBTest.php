<?php
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

/**
 * PHPUnit_Fixture_Transactions test case.
 */
class PHPUnitFixtureDynamicDBTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var PHPUnit_Fixture_Transactions
	 */
	private $_dynamicDB;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->_dynamicDB = new PHPUnit_Fixture_DynamicDB();
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->_dynamicDB = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests PHPUnit_Fixture_Transactions->__construct()
	 */
	public function test__construct() {
		$this->assertNotNull($this->_dynamicDB);
	}

}

