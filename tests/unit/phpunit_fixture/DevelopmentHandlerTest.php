<?php
/**
 * DevelopmentHandler
 * 
 * Testcase for building our Development Handler, which we'll use to setup
 * our development DB tables. As we know what our DB structures will be for
 * PHPUnit_Fixture_DB, it seems to make sense to create functionalit that will
 * also create and populate our development DB.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 */
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'DevelopmentHandler.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class BlankFixture extends PHPUnit_Fixture_DB {}

class DevelopmentHandlerTest extends PHPUnit_Framework_TestCase {
	
	private $_devHandler;
	
	public function __construct() {
		$this->setName('DevelopmentHandler Testcase');
	}
	
	public function setUp() {
		parent::setUp();
		$this->_devHandler = new DevelopmentHandler('development');
		$this->_testFix = new TestFixture();
		$this->_blankFix = new BlankFixture();
	}
	
	public function tearDown() {
		parent::tearDown();
		$this->_devHandler = null;
		$this->_testFix = null;
		$this->_blankFix = null;
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
		$result = $this->_devHandler->build($this->_testFix);
		$this->assertTrue($result);
		$this->_devHandler->drop();
	}
	
	/**
	 * We will want to sometimes drop our table, only really come
	 * across this when testing but will implement anyway for cleanliness.
	 */
	function testBuildDBReturnsFalseIfNoTables() {
		$result = $this->_devHandler->drop();
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
		$this->assertFalse($result);
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
		$this->_devHandler->build($this->_testFix);
		$result = $this->_devHandler->populate($this->_testFix);
		$this->assertTrue($result);
		$this->_devHandler->drop();
	}
}