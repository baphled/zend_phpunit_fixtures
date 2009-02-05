<?php

/*
 * InvalidFixture 
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @subpackage TestSuite_Fixture_DynamicDB
 * @package Zend_PHPUnit_Scaffolding
 *
 */

/**
 * Used to fake our thrown Zend_Exception 
 * being returned by retrieveSQLSchema
 *
 * @subpackage TestSuite_Fixture_DynamicDB
 * @package Zend_PHPUnit_Scaffolding
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
 * As PHPUnit_Fixture_DynamicDB, we'll use this to test
 * the class.
 *
 * @subpackage TestSuite_Fixture_DynamicDB
 * @package Zend_PHPUnit_Scaffolding
 *
 */
class DynamicTestDB extends PHPUnit_Fixture_DynamicDB {}

/**
 * PHPUnit_Fixture_Transactions test case.
 *
 * @subpackage TestSuite_Fixture_DynamicDB
 * @package Zend_PHPUnit_Scaffolding
 * 
 */
class PHPUnitFixturesDynamicDBTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Used to test our PHPUnit_Fixture_DynamicDB
	 * @access private
	 * @var PHPUnit_Fixture_DynamicDB
	 * 
	 */
	private $_dynamicDB;
	
	/**
	 * Extended version of PHPUnit_Fixture_DynamicDB
	 * so we can test that special circumstances.
	 *
	 * @var DynamicDBTester
	 */
	private $_dynamicDBTest;
	
	/**
	 * Stubbed version of our PHPUnit_Fixture_DynamicDB
	 *
	 * @var Mock
	 */
	private $_dynamicDBStub;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->_dynamicDB = new DynamicTestDB();
		$this->_dynamicDBTest = new DynamicDBTester();
		$this->_dynamicDBStub = $this->getMock('PHPUnit_Fixture_DynamicDB',array('retrieveSQLSchema', 'findSchema','getSchemas'));
		
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

	function _callDynamicDBStub() {
		$this->_dynamicDBStub->expects($this->once())
			->method('findSchema')
			->will($this->returnValue("CREATE TABLE IF NOT EXISTS `betting_index`.`event`"));
	}
	/*
	 * We want to be able to retrieve a list of SQL queries which
	 * will be used to create our test DB's, as this data is 
	 * created by a DB Admin, it would make sense to automate
	 * this process so that we can we don't need to do it manually.
	 * Making the process error prone & labourious.
	 */
	
	/**
	 * Testing properties
	 *
	 */
	function testDynamicDBHasASchemasAttribute() {
		$this->assertClassHasAttribute('_schemas','PHPUnit_Fixture_DynamicDB');
	}
	
	function testDynamicDBHasAFixManagerAttribute() {
		$this->assertClassHasAttribute('_fixMan','PHPUnit_Fixture_DynamicDB');
	}
	
	function testDynamicDBHasAConfigAttribute() {
		$this->assertClassHasAttribute('_general','PHPUnit_Fixture_DynamicDB');
	}
	
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
		$this->_dynamicDBStub->expects($this->once())
			->method('retrieveSQLSchema')
			->will($this->returnValue(true));
		$this->assertNotNull($this->_dynamicDBStub->retrieveSQLSchema());
	}
	
	/**
	 * We now have our URI, we now want to make sure that our response is === 200
	 * 
	 */
	function testRetrieveSQLSchemaThrowExceptionIfURIConnectionTimeOut() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDBStub->expects($this->once())
			->method('retrieveSQLSchema')
			->will($this->throwException(new Zend_Exception));
		$this->_dynamicDBStub->retrieveSQLSchema('http://blah.com');
	}
	
	/**
	 * Our results must come in an array format.
	 *
	 */
	function testRetrieveSQLSchemaReturnsAnTrueOnSuccess() {
		$this->_dynamicDBStub->expects($this->once())
			->method('retrieveSQLSchema')
			->will($this->returnValue(true));
		$this->assertEquals(true, $this->_dynamicDBStub->retrieveSQLSchema());
	}
	
	/**
	 * As these schemas are used to create DB tables we onlt want
	 * CREATE statements, so we need to make sure that each element
	 * contains this keyword.
	 *
	 */
	function testRetrieveSQLSchemaThatEachResultStartsWithCREATE() {
		$this->_dynamicDBStub->expects($this->once())
			->method('getSchemas')
			->will($this->returnValue(array('CREATE TABLE blah','CREATE TABLE blah2')));
		$result = $this->_dynamicDBStub->getSchemas();
		foreach ($result as $stmt) {
			$this->assertContains('CREATE', $stmt);
		}
	}
	
	/**
	 * We'll need to parse our HTML body so that we only actually have the pieces
	 * of data relevant to us (<td class="ddl_field"><pre> & </pre>) 
	 */
	function testRetreieveSQLSchemaThrowsExceptionIfPatternNotFoundAtAll() {
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('http://justanother.url');
	}
	
	/**
	 * If any of our elements are not a CREATE statement we want to throw
	 * an exception.
	 *
	 */
	function testRetrieveSQLSchemaIfNoCREATEInResultsThrowsException() {
		$this->markTestIncomplete('Need to find a site with pre\'s to test this or refactor search pattern out of code.');
		$this->setExpectedException('Zend_Exception');
		$this->_dynamicDB->retrieveSQLSchema('http://justanother.url');
	}
	
	/**
	 * We still have some cleaning up to do, in each element
	 * we still have a BR HTML tag, we'll need to remove this.
	 *
	 */
	function testRetrieveSQLSchemaThatEachResultHasNoBRs() {
		$this->_dynamicDBStub->expects($this->once())
			->method('getSchemas')
			->will($this->returnValue(array('CREATE TABLE blah','CREATE TABLE blah2')));
		$result = $this->_dynamicDBStub->getSchemas();
		foreach ($result as $stmt) {
			$this->assertNotContains('<br>', $stmt);			
		}
	}
	
	/**
	 * Now we want to make sure that we have more than 1 entry in our schema results
	 * @todo improve this test, is abit of a hack atm.
	 */
	function testGetSchemasReturnArrayWithElements() {
		$this->_dynamicDBStub->expects($this->once())
			->method('getSchemas')
			->will($this->returnValue(array('CREATE TABLE blah','CREATE TABLE blah2')));
		$this->assertGreaterThan(1,count($this->_dynamicDBStub->getSchemas()));
	}
	
	/**
	 * We want to be able to pass a parameter to retrieveSQLSchema which will
	 * allow us to retrieve only a single schema, we'll the DB table name as a
	 * search point.
	 *
	 * To do this I'll refactor retrieveSQLSchema to return bool & populate
	 * our private schema property which will be accessed via getSchema & findSchema
	 *  
	 */
	function testGetSchemaReturnsEmptyArrayIfNotPopulated() {
		$this->_dynamicDBStub->expects($this->once())
			->method('getSchemas')
			->will($this->returnValue(array()));
		$this->assertType('array', $this->_dynamicDBStub->getSchemas());
	}
	
	function testFindSchemaReturnsFalseIfNoResultsAreFound() {
		$this->_dynamicDBStub->expects($this->once())
			->method('findSchema')
			->will($this->returnValue(false));
		$this->assertFalse($this->_dynamicDBStub->findSchema('events'));
	}
	/**
	 * We want to be able to find a single schema and return it as a string
	 * 
	 */
	function testFindSchemaReturnsAsAString() {
		$this->_callDynamicDBStub();
		$this->assertType('string', $this->_dynamicDBStub->findSchema('event'));
	}
	
	function testFindSchemaResultsAreNotEmpty() {
		$this->_callDynamicDBStub();
		$this->assertNotSame('' ,$this->_dynamicDBStub->findSchema('event'));
	}
	
	/**
	 * Want to make sure that our schema
	 *
	 */
	function testFindSchemaReturnsTheExpectedResult() {
		$expected = "CREATE TABLE IF NOT EXISTS `betting_index`.`event`";
		$this->_callDynamicDBStub();
		$actual = $this->_dynamicDBStub->findSchema('event');
		$this->assertContains($expected,$actual);
	}
	
	
}
