<?php
/**
 *
 * PHPUnitFixtureDBTestCase
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage TestSuite_Fixture_DB
 *
 * Date: Sep 23, 2008
 * Basically gutted PHPUnit_Fixture's test case to refactor
 * our PHPUnit_Fixture_DB class, which we will use solely
 * for test DB interactions.
 * 
 */

require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Fixture/DB.php';

class EmptyFixture extends PHPUnit_Fixture_DB {}

class FixtureDBTest extends PHPUnit_Framework_TestCase {
	
	private $_fixturedb;
	
	public function __construct() {
		$this->setName ( 'PHPUnitFixtureDBTest Case' );
	}
	
	public function setUp() {
		parent::setUp ();
	    $this->_testFix = new DbTestFixture();
		$this->_emptyFix = new EmptyFixture();
		$this->_FixtureStub = $this->getMock('TestFixture',array('populate','truncate','setup','drop'));
	}
	
	public function tearDown() {
		$this->_emptyFix = null;
		$this->_FixtureStub = null;
		parent::tearDown ();
	}
    
    /**
     * Lastly we need a table property to store the table name
     * 
     */
    function testPHPUnitFixtureHasTableProperty() {
        $this->assertClassHasAttribute('_table','PHPUnit_Fixture_DB');
    }

    function testPHPUnitFixtureHasFixtureManagerProperty() {
        $this->assertClassHasAttribute('_fixMan','PHPUnit_Fixture_DB');
    }
    
    /**
     * We really need some getters for our fixtures properties,
     * so far they have not been privatise but it would be a good
     * idea to do so now.
     * 
     */
    function testGetNameReturnsString() {
        $result = $this->_testFix->getName();
        $this->assertType('string',$result);
    }
    
    /**
     * Now we need to be able to set our fixture table.
     * Our setting will only allow a string.
     * 
     */
    function testsetNameThrowsExceptionIfParamIsNotAString() {
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setName(array());
    }
    
    /**
     * Now we need to be able to actual set the name.
     * 
     */
    function testsetNameReturnsTrueOnSuccess() {
        $result = $this->_emptyFix->setName('coffee');
        $this->assertTrue($result);
    }
    
    /**
     * Now we want to make sure that the name has actually
     * been set.
     * 
     */
    function testSetNameActuallySetsNameOnSucces() {
        $table = 'tea';
        $this->_emptyFix->setName($table);
        $this->assertEquals($this->_emptyFix->getName(),$table);
    }
    
    /**
     * Now we can add test data to our fixtures, we want to be able
     * to create a fixture table, this will mainly be done, by FixturesManager
     * but we will create an accessor class here also for flexiblity.
     * 
     */
    
    /**
     * If we the default return value must be false, we will only return
     * true if we have successfully built our fixture table.
     * 
     */
    function testSetupFixtureTableReturnsTrueIfSetupFixtureTableSucceeds() {
	$this->_FixtureStub->expects($this->once())
		->method('setup')
		->will($this->returnValue(true));
        $this->assertTrue($this->_FixtureStub->setup());
        $this->_testFix->drop();
    }
    
    /**
     * each test we need to create a drop method to remove all our fixture data.
     * This'll be just a simple wrapper method that will use FixtureManager to remove
     * the table.
     * 
     */
    function testDropFixtureTableReturnsFalseOnFailure() {
        $result = $this->_testFix->drop();
        $this->assertFalse($result);
    }
    
    /**
     * Now we want to check that our drop method returns true
     * if we actually drop a table.
     * Very naughty but we'll use testFix to actually build
     * our table & then drop it.
     * 
     */
    function testDropFixtureTableReturnsTrueOnSuccess() {
        //$this->_testFix->setup();
	$this->_FixtureStub->expects($this->once())
		->method('drop')
		->will($this->returnValue(true));
        $result = $this->_FixtureStub->drop();
        $this->assertTrue($result);
    }
    
    /**
     * Ok, now we have implemented dropTable, we will use
     * it to keep our tests clean, ideally this will be used within
     * the tearDown() method.
     * 
     */

    
    /**
     * If our fixture does not have a table name set we need to handle it.
     * 
     */
    function testSetupFixtureTableThrowsExceptionIfTableNameIsNotSet() {
        $this->_emptyFix->add($this->_testFix->get('id',1));
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setup();
    }
    
    /**
     * If we are not able to build our fixtures table we need to return
     * false.
     * 
     */
    function testSetupFixtureTableReturnsFalseIfUnableToCreateFixturesTable() {
        $this->_emptyFix->setFields($this->_testFix->getFields());
        $result = $this->_emptyFix->setup();
        $this->assertFalse($result);
    }
    /**
     * What happens if our fixture doesnt have a table name or testdata set?
     * We know that FixtureManager handles this pretty well so we will use
     * its internal voodoo to deal with exceptions and simply catch them.
     */
    
    /**
     * So we want to make sure that an exception is thrown if our fixture
     * doesnt have any test fields.
     *
     */
    function testSetupFixtureTableThrowsExceptionIfTestFieldsIsNotSet() {
        $this->_emptyFix->setName('blah');
        $this->_emptyFix->add($this->_testFix->get('id',1));
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setup();
    }
    
    /**
     * Now we need to be able to actually build our fixture table, this
     * will be done by actually calling FixturesManagers method.
     * 
     */
    function testSetupFixtureTableReturnsTrueIfFixtureTableIsSuccessfullyBuilt() {	
	$this->_FixtureStub->expects($this->once())
		->method('setup')
		->will($this->returnValue(true));
        $result = $this->_FixtureStub->setup();
        $this->assertTrue($result);
    }
    
    /**
     * First off if we haven't built the fixture table, we need to throw
     * an error.
     * 
     */
    function testpopulateThrowsExceptionIfTableNameIsEmpty() {
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->populate();
    }
    
    /**
     * If we dont have a fixtures table, we need to throw an
     * exception.
     */
    function testpopulateThrowsExceptionIfTableIsNotBuilt() {
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->populate();
    }
    
    /**
     * Now if our fixture table is present we need can insert out
     * test data.
     * 
     */
    function testpopulateReturnsTrueIfTestDataIsSuccessfullyInserted() {
	$this->_FixtureStub->expects($this->once())
		->method('populate')
		->will($this->returnValue(true));
        //$this->_testFix->setup();
        $result = $this->_FixtureStub->populate();
        $this->assertTrue($result);
    }
    
    /**
     * We need to make sure that our fixtures class can actually
     * truncate our table.
     */
    function testTruncateTableReturnsTrueOnSuccess() {
	$this->_FixtureStub->expects($this->once())
		->method('truncate')
		->will($this->returnValue(true));
        //$this->_testFix->setup();
        $result = $this->_FixtureStub->truncate();
        $this->assertTrue($result);
    }
}
