<?php
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

/**
 * Used to fake our thrown Zend_Exception 
 * being returned by retrieveSQLSchema
 *
 */
class DynamicDBTester extends PHPUnit_Fixture_DynamicDB {
	
	/**
	 * A fake implementation of our PHPUnit_Fixture_DynamicDB::retrieveSQLSchema
	 *
	 * @param String $url
	 */
	public function retrieveSQLSchema($url='') {
		throw new Zend_Exception('Must submit a URL, via param or database.info');
	}
}
/**
 * PHPUnit_Fixture_Transactions test case.
 */
class PHPUnitFixturesDynamicDBTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var PHPUnit_Fixture_Transactions
	 */
	private $_dynamicDB;
	
	private $_dynamicDBTest;
	
	private $_dynamicDBStub;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->_dynamicDB = new PHPUnit_Fixture_DynamicDB();
		$this->_dynamicDBTest = new DynamicDBTester();
		$this->_dynamicDBStub = $this->getMock('PHPUnit_Fixture_DynamicDB',array('retrieveSQLSchema'));
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->_dynamicDB = null;
		$this->_dynamicDBTest = null;
		$this->_dynamicDBStub = null;
		parent::tearDown ();
	}
	
	/**
	 * Tests PHPUnit_Fixture_Transactions->__construct()
	 */
	public function test__construct() {
		$this->assertNotNull($this->_dynamicDB);
	}

	/*
	 * We want to be able to retrieve a list of SQL queries which
	 * will be used to create our test DB's, as this data is 
	 * created by a DB Admin, it would make sense to automate
	 * this process so that we can we don't need to do it manually.
	 * Making the process error prone & labourious.
	 */
	
	/**
	 * First we make sure that we return false if we can't locate
	 * the HTML file.
	 * 
	 */
	function testRetrieveSQLSchemaReturnsFalseIfURLNotValid() {
		$this->_dynamicDBStub->expects($this->once())
							 ->method('retrieveSQLSchema')
							 ->will($this->returnValue(false));
		$this->assertFalse($this->_dynamicDBStub->retrieveSQLSchema('http://false.url'));
	}
	
	/**
	 * If a URL is not passed & a configured URL is not in
	 * settings.ini we want to throw an exception.
	 * 
	 */
	function testRetrieveSQLSchemaThrowExceptionIfParamAndConfigURLsDoNotExists() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDBTest->retrieveSQLSchema();
	}
	
	/**
	 * We want to make sure that our URL has a HTTP prefix
	 *  
	 */
	function testRetrieveSQLSchemaThrowsExceptionIfNoHTTPPrefixInURL() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('https://not.valid.org');
	}
	
	/**
	 * If our URI is not empty and is set, we want to pass it to Zend_Http_Client
	 * 
	 */
	function testRetrieveSQLSchemaReturnsTrueIfURIIsSetAndIsValid() {
		$this->assertNotNull($this->_dynamicDB->retrieveSQLSchema());
	}
	
	/**
	 * We now have our URI, we now want to make sure that our response is === 200
	 * 
	 */
	function testRetrieveSQLSchemaThrowExceptionIfURIConnectionTimeOut() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('http://blah.com');
	}
	
	/**
	 * We'll need to parse our HTML body so that we only actually have the pieces
	 * of data relevant to us (<td class="ddl_field"><pre> & </td>) 
	 */
	function testRetrieveSQLSchemaReturnsAnArrayOnSuccess() {
		$this->assertType('array', $this->_dynamicDB->retrieveSQLSchema());
	}
	
	function testRetrieveSQLSchemaThatEachResultStartsWithCREATE() {
		$result = $this->_dynamicDB->retrieveSQLSchema();
		foreach ($result as $stmt) {
			$this->assertContains('CREATE', $stmt);
		}
	}
	function testRetreieveSQLSchemaThrowsExceptionIfPatternNotFoundAtAll() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('http://auto.ibetx.com');
	}
	
	function testRetrieveSQLSchemaIfNoCREATEInResultsThrowsException() {
		$this->markTestIncomplete('Need to find a site with pre\'s to test this or refactor search pattern out of code.');
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('http://auto.ibetx.com');
	}
	
	function testRetrieveSQLSchemaThatEachResultHasNoBRs() {
		$result = $this->_dynamicDB->retrieveSQLSchema();
		foreach ($result as $stmt) {
			$this->assertNotContains('<br>', $stmt);			
		}
	}
	/**
	 * Now we want to make sure that we have more than 1 entry in our schema results
	 * 
	 */
	function testRetrieveSQLSchemaReturnsMoreThanOneResultInArray() {
		$result = $this->_dynamicDB->retrieveSQLSchema();
		$this->assertGreaterThan(10, count($result));
	}
	
}