<?php
/**
 *
 * FixtureTestCase
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package PHPUnit_Fixture
 * @subpackage TestSuite
 *
 * $LastChangedBy$
 * 
 * Date: 05/09/2008
 * Created test cases for getting & setting fixture table name & fields, 
 * we want to be able to get an individual data type field, we'll also
 * need to be able to get the actual table name. The goal of these is
 * to mak the interface smoother and hide our properties from general
 * code.
 * 
 * Date: 03/09/2008
 * Introduced tests for generating test data on the fly, we still need
 * to improve on but is more aless finished.
 * Really no need to extend Module_PHPUnit_Framework_TestCase, as
 * Fixture does not use the DB or Zend.
 * Improved code coverage and added tests for autoGenerateTestData
 * which were previously covered by _generateTestData
 * 
 * Date: 01/09/2008
 * Created tests cases to implement drop & build fixture table,
 * both of which will be need to create our db tables.
 * Initially we focussed on build, until we came across a stumbling
 * block which was the fact that old test db data was being left
 * behind by our tests, so we needed to factor in FixturesManagers
 * dropTable until we implemented Fixture's wrapper version.
 * Which is now being used. This situation is far from ideal, though
 * it should how to implement the system, it is cumbersome and sloppy.
 * We should really introduce stubs to handle this test functionality.
 * 
 * Date: 31/08/2008
 * Started basic test cases for implementing out fixture class,
 * which will allow us to subclass it giving us the ability to
 * preset specific details (table, fields & test data namely).
 * Complete test units to implement the get test data functionality
 * of the system.
 * 
 */
require_once dirname(__FILE__) .'/../../../tests/TestHelper.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Fixture.php';

class BasicFixture extends PHPUnit_Fixture {}

class AnotherFixture extends PHPUnit_Fixture {
	public function fixtureMethodCheck() {
		$this->_fixtureMethodCheck('blah');
	}
}
class FixtureTest extends PHPUnit_Framework_TestCase {
	
	private $_fixtures;
	
	private $_testFix;
	
	public function __construct() {
		$this->setName ( 'FixtureTest Case' );
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_fixture = new PHPUnit_Fixture();
		$this->_testFix = new TestFixture();
		$this->_invalidFieldFixture = new InvalidFieldTypeFixture();
		$this->_basicFix = new BasicFixture();
	}
	
	public function tearDown() {
		$this->_fixture = null;
		$this->_testFix = null;
		$this->_invalidFieldFixture = null;
		$this->_basicFix = null;
		parent::tearDown ();
	}
	
	function testConstructor() {
		$this->assertNotNull($this->_fixture);
	}
	
	/**
	 * Our Fixture will need a testDataProperty.
	 */
	function testFixtureHasTestDataProperty() {
		$this->assertClassHasAttribute('_testData','PHPUnit_Fixture');
	}
	
	/**
	 * We'll also need a fields property to store our table field property
	 * 
	 */
	function testPHPUnitFixtureHasFieldsProperty() {
		$this->assertClassHasAttribute('_fields','PHPUnit_Fixture');
	}
	
	/**
	 * Lastly we need a table property to store the table name
	 * 
	 */
	function testPHPUnitFixtureHasTableProperty() {
		$this->assertClassHasAttribute('_table','PHPUnit_Fixture');
	}
	
    function testPHPUnitFixtureHasFixtureManagerProperty() {
        $this->assertClassHasAttribute('_fixMan','PHPUnit_Fixture');
    }
	
   /**
     * If we have no fixtures we need to know. We'll create a method
     * that will return the number of fixtures within our class.
     * 
     */
    function testTestDataCountReturnsZeroIfNoFixturesArePresent() {
        $result = $this->_fixture->testDataCount();
        $this->assertEquals(0,$result);
    }
    
    /**
     * Now we'll actually check that we have a array & if we do
     * we want to count it, otherwise we have zero.
     * 
     */
    function testTestDataCountReturnsTheExpectedNumberOfResults() {
        $result = $this->_testFix->testDataCount();
        $this->assertEquals(7,$result);
    }
    
	/**
	 * We'll need to retrieve our fixtures at some point, as we already
	 * have a test fixture already defined we'll use that to determine
	 * a few things.
	 * 
	 */
    
    /**
     * Here we want to test that if we try to get test data from
     * an fixture with no actual data, we get false.
     *
     */
	function testGetTestDataReturnsFalseIfNoTestDataPresent() {
		$result = $this->_basicFix->getTestData();
		$this->assertFalse($result);
	}
	
	/**
	 * If the parameter passed to getTestData is no string we
	 * throw an exception.
	 * 
	 */
	function testGetTestDataThrowsExceptionIfParameterIsNotAString() {
		$id = array();
		$this->setExpectedException('ErrorException');
		$this->_basicFix->getTestData($id);
	}
	
	/**
	 * Ok, now we want to make sure that if we have test data we
	 * return an array. TestFixture has test data so we will use
	 * this instead of creating actual data.
	 * 
	 */
	function testGetTestDataReturnsAnArrayIfTestDataIsPresent() {
		$result = $this->_testFix->getTestData();
		$this->assertType('array',$result);
	}
	
	/**
	 * We forgot to test that a value is specified, if a key
	 * is, as both are need to retrieve a single test data.
	 * 
	 */
	function testGetTestDataThrowsExceptionIfKeyIsPassedButValueIsEmpty() {
		$key = 'id';
		$value = '';
		$this->setExpectedException('ErrorException');
		$this->_testFix->getTestData($key,$value);
	}
	
	/**
	 * Went on abit of a tangent there but retrieving test data
	 * seemed like functionality needed to implement first.
	 * 
	 */
	  
    /**
     * Now our class must have testData in array format
     * 
     */
    function testAddTestDataThrowsExceptionIfTestDataIsNotAnArray() {
        $testData = '';
        $this->setExpectedException('ErrorException');
        $this->_fixture->addTestData($testData);
    }
    
    /**
     * If we set our test data up successfully we should get a response
     * 
     */
    function testFixtureAddTestDataReturnsTrueOnSuccess() {
        $testData = $this->_testFix->getTestData('id',1);
        $result = $this->_basicFix->addTestData($testData);
        $this->assertTrue($result);
    }
	/**
	 * Now we need to see if we can actually add test data to our
	 * fixture, we'll use BasicFixture here so we can use a clean
	 * version, we'll work on this with the use of TestFixture later
	 * where we will check to see if the data being added is the same
	 * format as what we are inputting.
	 * 
	 */
	function testAddTestDataCanSubmitTestDataToFixture() {
		$testData = $this->_testFix->getTestData('id',1);
		$result = $this->_basicFix->addTestData($testData);
		$this->assertTrue($result);
		$expected = $this->_basicFix->getTestData(); 
		$this->assertSame($expected,$testData);
	}

	/**
	 * Now we want to be able to add multiples as well
	 * 
	 */
	function testAddTestDataIsAbleToAddMultiplesOfTestData() {
		$actual = array();
		$actual[] = $this->_testFix->getTestData('id',1);
		$actual[] = $this->_testFix->getTestData('id',3);
		$this->_basicFix->addTestData($actual);
		$expected = $this->_basicFix->getTestData();
		$this->assertSame($expected,$actual);
		
	}
	
	/**
	 * We want to make sure that we return false by default when
	 * using validateTestData
	 * 
	 */
	function testValidateTestDataReturnsTrueAsDefault() {
		$testData = array();
		$testData[] = $this->_testFix->getTestData('id',1);
		$result = $this->_basicFix->validateTestData($testData);
		$this->assertTrue($result);
	}
	
	/**
	 * 
	 * We should really test that the test data we are adding is
	 * of the same structure as the rest of predefined data.
	 * 
	 */
	function testAddTestDataThrowsExceptionsIfAddingTestDataOfVaryingStructure() {
		$testData[] = $this->_testFix->getTestData('id',1);
		$this->setExpectedException('ErrorException');
		$invalidData[] = array('id' => 7, 'appl_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21');
		$this->_basicFix->addTestData($testData);
		$this->_basicFix->validateTestData($invalidData);
	}
	
	/**
	 * Now we need to check that if our invalid test data is submitted via
	 * addTestData, we need to throw an exception. ValidateTestData, should
	 * ideally be callable from within addTestData, so that we can automatically
	 * handle validations within our addition routine.
	 * 
	 */
	function testAddTestThrowsExceptionsIfTestDataDoesNotMatchPreExistingTestDataStructure() {
		$testData[] = $this->_testFix->getTestData('id',1);
		$this->setExpectedException('ErrorException');
		$invalidData[] = array('id' => 7, 'appl_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21');
		$this->_basicFix->addTestData($testData);
		$this->_basicFix->addTestData($invalidData);
	}
	
	/**
	 * Now we can add test data to our fixture class, we want to throw
	 * exceptions if when an entry is already submitted.
	 * 
	 */
	function testAddTestReturnsTrueIfTestDataIsAlreadyPresent() {
		$testData[] = $this->_testFix->getTestData('id',1);
		$result = $this->_testFix->testDataExists($testData);
		$this->assertTrue($result);
	}
	
	/**
	 * If testDataExists finds no test data we want to return false.
	 * 
	 */
	function testTestDataExistsFalseIfNoTestDataIsPresent() {
		$testData[] = $this->_basicFix->getTestData('id',1);
		$result = $this->_basicFix->testDataExists($testData);
		$this->assertFalse($result);
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
	 * @todo Need to refactor and replace dropTable with Fixture's
	 *       implementation, once it is done.
	 * 
	 */
	function testSetupFixtureTableReturnsTrueIfSetupFixtureTableSucceeds() {
		$result = $this->_testFix->setupTable();
		$this->assertTrue($result);
		$this->_testFix->dropTable();
	}
	
	/**
	 * If our fixture does not have a table name set we need to handle it.
	 * 
	 */
	function testSetupFixtureTableThrowsExceptionIfTableNameIsNotSet() {
		$this->_basicFix->addTestData($this->_testFix->getTestData('id',1));
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setupTable();
	}
	
	/**
	 * If we are not able to build our fixtures table we need to return
	 * false.
	 * 
	 */
	function testSetupFixtureTableReturnsFalseIfUnableToCreateFixturesTable() {
		$this->_basicFix->setFields($this->_testFix->_fields);
		$result = $this->_basicFix->setupTable();
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
		$this->_basicFix->setTableName('blah');
		$this->_basicFix->addTestData($this->_testFix->getTestData('id',1));
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setupTable();
	}
	
	/**
	 * Now we need to be able to actually build our fixture table, this
	 * will be done by actually calling FixturesManagers method.
	 * 
	 */
	function testSetupFixtureTableReturnsTrueIfFixtureTableIsSuccessfullyBuilt() {
		$result = $this->_testFix->setupTable();
		$this->assertTrue($result);
        $this->_testFix->dropTable();
	}
	
	/**
	 * each test we need to create a drop method to remove all our fixture data.
	 * This'll be just a simple wrapper method that will use FixtureManager to remove
	 * the table.
	 * 
	 */
	function testDropFixtureTableReturnsFalseOnFailure() {
		$result = $this->_testFix->dropTable();
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
		$this->_testFix->setupTable();
		$result = $this->_testFix->dropTable();
        $this->assertTrue($result);
	}
	
	/**
	 * Ok, now we have implemented dropTable, we will use
	 * it to keep our tests clean, ideally this will be used within
	 * the tearDown() method.
	 * 
	 */
	
	/**
	 * First off if we haven't built the fixture table, we need to throw
	 * an error.
	 * 
	 */
	function testpopulateThrowsExceptionIfTableNameIsEmpty() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->populate();
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
		$this->_testFix->setupTable();
		$result = $this->_testFix->populate();
		$this->assertTrue($result);
		$this->_testFix->dropTable();
	}

	/**
	 * We'll need this at some point to help us generate our 
	 * actul test data & add it to the fixtures test data.
	 *
	 */
	function testAutoGenerateTestDataReturnsFalseByDefault() {
		$result = $this->_testFix->autoGenerateTestData();
		$this->assertTrue($result);
	}
	
	/**
	 * Now we want to be able to parse our fields data, 
	 * creating an array that corresponds with the fixtures schema.
	 * 
	 */
	function testAutoGenerateTestDataReturnsResultsAsArrayAndIdIsNotAutoSet() {
		$this->_testFix->autoGenerateTestData(1);
		$this->assertEquals(8,count($this->_testFix->getTestData()));
	}
	
	/**
	 * Now we want to see if we can generate multiple pieces of test data.
	 * 
	 */
	function testGenerateFixtureTestDataReturnsExpectedNumberOfTestData() {
		$this->_testFix->autoGenerateTestData(23);
        $this->assertEquals(30,count($this->_testFix->getTestData()));
	}
	
	/**
	 * We should get back to implementing autoGenerateTestData, which will be 
	 * the wrapper method for handling all our test data generating.
	 * 
	 */
	
	/**
	 * We know _basicFix has no set fields, so we will try to generate test data
	 * for that and see what happens, we expect to see a return of false, but lets see.
	 *
	 */
	function testAutoGenerateTestDataReturnsFalseIfFixtureHasNotDefinedFieldsProperty() {
		$result = $this->_basicFix->autoGenerateTestData();
		$this->assertFalse($result);
	}
	
	/**
	 * Sweet, so all goes well so far, lets see what happens if we add test data to _testFix.
	 * It already has 7 pieces of data, lets add another 10.
	 * 
	 */
	function testAutoGenerateTestDataCanAddTenPiecesOfTestDataToTestFixture() {
		$result = $this->_testFix->autoGenerateTestData(10);
		$this->assertTrue($result);
		$numTestData = count($this->_testFix->getTestData());
		$this->assertEquals(17,$numTestData);
	}
	
	/**
	 * When we try to auto gen test data with no field data, what happens?
	 * 
	 */
	function testAutoGenerateTestDataReturnsFalseIfFixtureHasNoFieldData() {
		$result = $this->_basicFix->autoGenerateTestData();
		$this->assertFalse($result);
	}
	
	/**
	 * Now we need to create an implementation that allows us to retrieve
	 * an array of the fixture table fields.
	 * 
	 */
	function testGetFixtureTableFieldsThrowsExceptionsIfNoFieldsAreDefined() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->getTableFields();
	}
	
	/**
	 * Now we need to test that we are able to return an array of our fixture fields
	 *
	 */
	function testGetFixtureTableFieldsReturnsAnArrayOnSuccess() {
		$result = $this->_testFix->getTableFields();
		$this->assertType('array',$result);
	}
	
	/**
	 * We refactored Fixture so that _generateTestData is now private, we need to cover
	 * the tests that were previously used on _generateTestData
	 *
	 * The following will cause autoGenerateTestData to return false. 
	 */
	function testAutoGenerateTestDataReturnsFalseAndCatchesErrorExceptionIfTestDataIsNotSet() {
		$result = $this->_basicFix->autoGenerateTestData(1);
		$this->assertFalse($result);
	}
	
	function testAutoGenerateTestDataReturnsFalseAndCatchesErrorExceptionIfFieldsHasAnEntryWithNoType() {
		$result = $this->_invalidFieldFixture->autoGenerateTestData(1);
		$this->assertFalse($result);
	}

    function testAutoGenerateTestDataReturnsFalseAndCatchesErrorExceptionIfParamIsZero() {
        $result = $this->_invalidFieldFixture->autoGenerateTestData(0);
        $this->assertFalse($result);
    }
    
	function testAutoGenerateTestDataReturnsFalseAndCatchesErrorExceptionIfParamIsNotInt() {
        $result = $this->_invalidFieldFixture->autoGenerateTestData('23');
        $this->assertFalse($result);
    }
	
	function testAutoGenerateTestDataReturnsFalseAndCatchesErrorExceptionIfDataTypeLengthSpecifiedWithDateAndDateTime() {
		$this->_invalidFieldFixture->_fields = array('id' => array('type' => 'date', 'length' => '10', 'null' => FALSE));
		$result = $this->_invalidFieldFixture->autoGenerateTestData(1); 
		$this->assertFalse($result);
	}
	
	/**
	 * We want to be able to retrieve a single data type field, this will usually be used
	 * to validate a piece of test data is of the correct type.
	 */
	
	/**
	 * Our field name parameter is a string so we need to make sure we make
	 * sure it is.
	 *
	 */
	function testRetrieveSingleTestDataFieldThrowsExceptionIfFieldNameIsNotAString() {
		$this->setExpectedException('ErrorException');
		$this->_testFix->getSingleDataTypeField(array());
	}
	
	/**
	 * Now we need to check that our field id is valid
	 * 
	 */
	function testRetrieveSingleTestDataFieldThrowsExceptionIfFieldIsInvalid() {
		$this->setExpectedException('ErrorException');
		$this->_testFix->getSingleDataTypeField('bid');
	}
	
	/**
	 * Now we need to make sure that we return an array
	 * @todo create tests to implement data type verification.
	 * 
	 */
	function testRetrieveSingleTestDataFieldReturnAnArrayOnSucces() {
		$result = $this->_testFix->getSingleDataTypeField('id');
		$this->assertType('array',$result);
	}
	
	function testRetrieveSingleTestDataFieldReturnExpected() {
		$fieldData = array('id'=>$this->_testFix->_fields['id']);
		$result = $this->_testFix->getSingleDataTypeField('id');
		$this->assertSame($fieldData,$result);
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
		$this->_basicFix->setTableName(array());
	}
	
	/**
	 * Now we need to be able to actual set the name.
	 * 
	 */
	function testSetTableNameReturnsTrueOnSucces() {
		$result = $this->_basicFix->setTableName('coffee');
		$this->assertTrue($result);
	}
	
	/**
	 * Now we want to make sure that the name has actually
	 * been set.
	 * 
	 */
	function testSetTableNameActuallySetsNameOnSucces() {
		$table = 'tea';
		$this->_basicFix->setTableName($table);
		$this->assertEquals($this->_basicFix->getTableName(),$table);
	}
	
	/**
	 * Okay so after the last pass we refactored our testcase and
	 * our class. _table is now private as it should be.
	 */
	
	/**
	 * Now we'll need to be able to set field data, to do this, we'll
	 * need to make sure that the submitted array is of the correct format.
	 * 
	 */
	function testSetTestDataFieldsThrowsExceptionIfParamIsNotAnArrayOfAnArray() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setFields(array());
	}
	
	/**
	 * Each field must start with a string
	 * 
	 */
	function testSetFieldsThrowExceptionIfFieldHasNoDataTypeName() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setFields(array(array()));
	}

	function testSetFieldsThrowExceptionIfFieldsDataIsNotInArrayFormat() {
		$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => ''));
	}

	function testSetFieldsThrowExceptionIfDataHasNotType() {
		$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('tip' => 'top')));
	}
	
	function testSetFieldsThrowExceptionIfDataHasTypeSetInTheWrongOrder() {
		$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('tip' => 'type')));
	}
	
	function testSetFieldsReturnsTrueIfTypeIsOfIntegerType() {
		$result = $this->_basicFix->setFields(array('id' => array('type' => 'integer', 'length' => 10)));
		$this->assertTrue($result);
	}
	
    function testSetFieldsReturnsTrueIfTypeIsOfStringType() {
        $result = $this->_basicFix->setFields(array('id' => array('type' => 'string', 'length' => 10)));
        $this->assertTrue($result);
    }
    
    function testSetFieldsThrowsExceptionIfTypeIsStringWithNoLength() {
    	$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('type' => 'string')));
    }
    
    function testSetFieldsReturnsTrueIfTypeIsOfDateType() {
        $result = $this->_basicFix->setFields(array('id' => array('type' => 'date')));
        $this->assertTrue($result);
    }
    
    function testSetFieldsReturnsTrueIfTypeIsOfDateTimeType() {
        $result = $this->_basicFix->setFields(array('id' => array('type' => 'datetime')));
        $this->assertTrue($result);
    }
    
    function testSetFieldsThrowExceptionIfLengthIsNotAnInt() {
    	$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('type' => 'datetime', 'length' => '10')));
    }
    
    function testSetFieldsThrowExceptionIfLengthSpecifiedWithDate() {
    	$this->setExpectedException('ErrorException');
    	$this->_basicFix->setFields(array('id' => array('type' => 'datetime', 'length' => 10)));
    }
    
    function testSetFieldsThrowExceptionIfNullIsNotABool() {
    	$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('type' => 'datetime', 'null' => 10)));
    }
    
    /**
     * We really shouldnt be able to set default & null on the same data type.
     *
     */
    function testSetFieldsThrowExceptionIfNullIsAlongWithDefault() {
    	$this->setExpectedException('ErrorException');
        $this->_basicFix->setFields(array('id' => array('type' => 'string', 'length' => 10, 'null' => true, 'default' => '')));
    }
    
    /*
     * Be nice to be able to be able to verify if we have more than one primary key
     * as it shouldnt be possible.
     */
    
	function testSetFieldsThrowExceptionIfDataTypeIsNotOfTheCorrectType() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setFields(array('id' => array('type' => 'type')));
	}
	
    function testSetDataFieldsReturnsTrueOnSuccess() {
        $result = $this->_basicFix->setFields($this->_testFix->getTableFields());
        $this->assertTrue($result);
    }
    
    /**
     * Now we'll make sure that our new field data is actually set.
     *  
     */
    function testSetDataFieldsActuallySetsOurFieldsProperty() {
    	$expected = $this->_testFix->getTableFields();
    	$result = $this->_basicFix->setFields($expected);
    	$actual = $this->_basicFix->getTableFields();
    	$this->assertTrue($result);
    	$this->assertEquals($expected,$actual);
    }
	/**
	 * There will be times when we want to clear our predefined
	 * test data, possibly when we want to use the same structure
	 * but don't wont any data present, for this we will need to 
	 * unset the _testData property.
	 * 
	 */
	
	/**
	 * Now if we need to be able to retrieve our testData with auto incremented id's, this will
	 * be used to retrieve test data without actually having to insert the data into our test DB.
	 */
	function testRetrieveTestDataResultsReturnsArrayOnSuccess() {
		//$this->markTestIncomplete();
		$result = $this->_testFix->retrieveTestDataResults();
		$this->assertType('array',$result);
	}
	
	/**
	 * We need to make sure that if we have and null id's, that we
	 * incrementally change them to an integer.
	 * 
	 */
	function testRetrieveTestDataResultsReturnsArrayAndIdsAreNotNull() {
		$this->_basicFix->setFields($this->_testFix->getTableFields());
		$this->_basicFix->autoGenerateTestData(20);
		$data = $this->_basicFix->retrieveTestDataResults();
		for($i=0;$i<$this->_basicFix->testDataCount();$i++) {
		  $this->assertNotNull($data[$i]['id']);
		  $this->assertNotEquals(0,$data[$i]['id']);
		  $this->assertEquals($i+1,$data[$i]['id']);
		}
	}
	
	/**
	 * Now we want to make sure that we only increment id's that are null
	 * 
	 */
	function testRetrieveTestDataIncrementsFromTheLastInputtedID() {
		$this->_testFix->autoGenerateTestData(20);
        $data = $this->_testFix->retrieveTestDataResults();
		for($i=0;$i<$this->_testFix->testDataCount();$i++) {
			$this->assertEquals($i+1,$data[$i]['id']);
		}
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
}