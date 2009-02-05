<?php
/**
 * DevelopmentHandler
 * 
 * Testcase for building our Development Handler, which we'll use to setup
 * our development DB tables. As we know what our DB structures will be for
 * PHPUnit_Fixture_DB, it seems to make sense to create functionalit that will
 * also create and populate our development DB.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @subpackage TestSuite_Fixture_DynamicDB
 * @package Zend_PHPUnit_Scaffolding
 *
 */
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'DevelopmentHandler.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class BlankFixture extends PHPUnit_Fixture_DB {}

class DynamicFixture extends PHPUnit_Fixture_DynamicDB {}

class FakeDevelopmentHandler extends DevelopmentHandler {
	function genStagingStructure(PHPUnit_Fixture_DynamicDB $fixture) {
		throw new Zend_Exception('No schema found.');
	}
}

class DevelopmentHandlerTest extends PHPUnit_Framework_TestCase {
	
	private $_devHandler;
	
	public function __construct() {
		$this->setName('DevelopmentHandler Testcase');
	}
	
	public function setUp() {
		parent::setUp();
		$this->_devHandler = new DevelopmentHandler('development');
		$this->_testFix = new TestFixture();
		$this->_dynamicFix = new DynamicFixture();
		$this->_fakeDevHandler = new FakeDevelopmentHandler('development');
		$this->_blankFix = new BlankFixture();
		$this->_devHandlerStub = $this->getMock('DevelopmentHandler',array('build','populate','drop'));
	}
	
	public function tearDown() {
		parent::tearDown();
		$this->_devHandler = null;
		$this->_testFix = null;
		$this->_blankFix = null;
		$this->_devHandlerStub = null;
	}
	
	function testConstructor() {
		$this->assertNotNull($this->_devHandler);
	}
	
	function testDevelopmentHandlerHasFixtureManagerProperty() {
		$this->assertClassHasAttribute('_fixMan','DevelopmentHandler');
	}
	
	/**
	 * we'll be passing our method a fixture, so
	 * we will need to check that we actually have
	 * one, else we throw an exception.
	 *
	 */
	function testBuildDBTakesFixtureAsClass() {
		$this->setExpectedException('ErrorException');
		$this->_devHandler->build(array());
	}
	
	/**
	 * We now need to make sure that the fixture has
	 * the nessary properties to create our development
	 * DB, this would include, the table name & fields.
	 * Actual test data will be optional.
	 */
	
	/**
	 * First we'll check that we have a table name.
	 * 
	 */
	function testBuildDBThrowsExceptionIfFixtureHasNoTableName() {
		$this->setExpectedException('ErrorException');
		$this->_devHandler->build($this->_blankFix);
	}
	
	/**
	 * What if our fields property is empty, we expect an
	 * exception to be thrown.
	 * 
	 */
	function testBuildDBThrowsExceptionIfFixtureHasNoFieldsData() {
		$this->_blankFix->setName('chicken');
		$this->setExpectedException('ErrorException');
		$this->_devHandler->build($this->_blankFix);
	}
	
	/**
	 * We now want to check that when we create the new
	 * development table that we retrieve true.
	 * 
	 */
	function testBuildDBReturnsTrueOnSuccess() {
		$this->_devHandlerStub->expects($this->once())
			->method('build')
			->will($this->returnValue(true));
		$result = $this->_devHandlerStub->build($this->_testFix);
		$this->assertTrue($result);
		//$this->_devHandler->drop();
	}
	
	/**
	 * We will want to sometimes drop our table, only really come
	 * across this when testing but will implement anyway for cleanliness.
	 */
	function testBuildDBReturnsFalseIfNoTables() {
		$this->_devHandlerStub->expects($this->once())
			->method('drop')
			->will($this->returnValue(false));
		$result = $this->_devHandlerStub->drop();
		$this->assertFalse($result);
	}
	
	/**
	 * We need to make sure that we can actually populate our development table
	 * 
	 */
	function testPopulateThrowsExceptionIfHasNoTestData() {
		$this->_blankFix->setName('another');
		$this->setExpectedException('ErrorException');
		$result = $this->_devHandler->populate($this->_blankFix);
	}
	
	function testPopulateThrowsExceptionIfFixtureNotASubclassOfPHPUnitFixtureDB() {
		$this->setExpectedException('ErrorException');
		$this->_devHandler->populate(NULL);
	}
	
	function testPopulateThrowsExceptionIfFixtureHasNotFieldData() {
		$this->_blankFix->setName('blah');
		$this->setExpectedException('ErrorException');
        $this->_devHandler->populate($this->_blankFix);
	}
	
	function testPopulateReturnsTrueIfSuccessfullyPopulatesTable() {
		$this->_devHandlerStub->expects($this->once())
			->method('populate')
			->will($this->returnValue(true));
		//$this->_devHandler->build($this->_testFix);
		$result = $this->_devHandlerStub->populate($this->_testFix);
		$this->assertTrue($result);
		//$this->_devHandler->drop();
	}
	
	/**
	 * We want developmentHandler to be able to create our staging DB & tables on the fly.
	 * First we only want to do this with PHPUnit_Fixture_DynamicDB.
	 */
	function testGenStagingStructureOnlyAcceptsPHPUnit_Fixture_DynamicDB() {
		//$this->assertFalse($this->_devHandler->genStagingStructure($this->_dynamicFix));
	}
		
	function testGenStagingStructureThrowsExceptionIfNoSchemasAreFound() {
		$this->setExpectedException('Zend_Exception');
		$this->_fakeDevHandler->genStagingStructure($this->_dynamicFix);
	}
	
	function testGenStagingStructureReturnsTrueIfSchemaAndTablesCreated() {
		$this->markTestIncomplete('Will always return false, until we have a complete valid list of schemas.');
		$this->assertTrue($this->_devHandler->genStagingStructure($this->_dynamicFix));
	}
}
