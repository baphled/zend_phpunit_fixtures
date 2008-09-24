<?php
/**
 *
 * PHPUnitFixtureDBTestCase
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package PHPUnit_Fixture
 * @subpackage TestSuite_PHPUnit_Fixture_DB
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

class AnotherFixture extends PHPUnit_Fixture_DB {
    public function fixtureMethodCheck() {
        $this->_fixtureMethodCheck('blah');
    }
}

class FixtureDBTest extends PHPUnit_Framework_TestCase {
	
	private $_fixturedb;
	
	public function __construct() {
		$this->setName ( 'PHPUnitFixtureDBTest Case' );
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_fixturedb = new PHPUnit_Fixture_DB();
        $this->_testFix = new TestFixture();
		$this->_emptyFix = new EmptyFixture();
	}
	
	public function tearDown() {
		$this->_phpunitfixturedb = null;
		$this->_emptyFix = null;
		parent::tearDown ();
	}
	
	function testConstructor() {
		$this->assertNotNull($this->_fixturedb);
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
    function testGetTableNameReturnsString() {
        $result = $this->_testFix->getTableName();
        $this->assertType('string',$result);
    }
    
    /**
     * Now we need to be able to set our fixture table.
     * Our setting will only allow a string.
     * 
     */
    function testSetTableNameThrowsExceptionIfParamIsNotAString() {
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setTableName(array());
    }
    
    /**
     * Now we need to be able to actual set the name.
     * 
     */
    function testSetTableNameReturnsTrueOnSucces() {
        $result = $this->_emptyFix->setTableName('coffee');
        $this->assertTrue($result);
    }
    
    /**
     * Now we want to make sure that the name has actually
     * been set.
     * 
     */
    function testSetTableNameActuallySetsNameOnSucces() {
        $table = 'tea';
        $this->_emptyFix->setTableName($table);
        $this->assertEquals($this->_emptyFix->getTableName(),$table);
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
        $result = $this->_testFix->setup();
        $this->assertTrue($result);
        $this->_testFix->drop();
    }

    /**
     * We want to be able to check that fixtureMethodCheck throws
     * an exception, even though we have implemented the funcionality
     * we should still be able to test this by subclassing fixture
     * and _fixtureMethodCheck, passing it an invalid call parameter.
     *
     */
    function testFixureMethodCheckThrowsExceptionIfInvalidCall() {
        $this->setExpectedException('ErrorException');
        $this->_anotherFix = new AnotherFixture();
        $this->_anotherFix->fixtureMethodCheck();
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
        $this->_testFix->setup();
        $result = $this->_testFix->drop();
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
        $this->_emptyFix->addTestData($this->_testFix->getTestData('id',1));
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setup();
    }
    
    /**
     * If we are not able to build our fixtures table we need to return
     * false.
     * 
     */
    function testSetupFixtureTableReturnsFalseIfUnableToCreateFixturesTable() {
        $this->_emptyFix->setFields($this->_testFix->getTableFields());
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
        $this->_emptyFix->setTableName('blah');
        $this->_emptyFix->addTestData($this->_testFix->getTestData('id',1));
        $this->setExpectedException('ErrorException');
        $this->_emptyFix->setup();
    }
    
    /**
     * Now we need to be able to actually build our fixture table, this
     * will be done by actually calling FixturesManagers method.
     * 
     */
    function testSetupFixtureTableReturnsTrueIfFixtureTableIsSuccessfullyBuilt() {
        $result = $this->_testFix->setup();
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
        $this->_testFix->populate();
    }
    
    /**
     * Now if our fixture table is present we need can insert out
     * test data.
     * 
     */
    function testpopulateReturnsTrueIfTestDataIsSuccessfullyInserted() {
        $this->_testFix->setup();
        $result = $this->_testFix->populate();
        $this->assertTrue($result);
    }
    
    /**
     * We need to make sure that our fixtures class can actually
     * truncate our table.
     */
    function testTruncateTableReturnsTrueOnSuccess() {
        $this->_testFix->setup();
        $result = $this->_testFix->truncate();
        $this->assertTrue($result);
    }
}