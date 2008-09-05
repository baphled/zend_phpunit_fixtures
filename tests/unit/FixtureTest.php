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
 * Date: 03/09/2008
 * Introduced tests for generating test data on the fly, we still need
 * to improve on but is more aless finished.
 * Really no need to extend Module_PHPUnit_Framework_TestCase, as
 * Fixture does not use the DB or Zend.
 * Improved code coverage and added tests for autoGenerateTestData
 * which were previously covered by _generateFixtureTestData
 * Date: 01/09/2008
 * Created tests cases to implement drop & build fixture table,
 * both of which will be need to create our db tables.
 * Initially we focussed on build, until we came across a stumbling
 * block which was the fact that old test db data was being left
 * behind by our tests, so we needed to factor in FixturesManagers
 * dropFixtureTable until we implemented Fixture's wrapper version.
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

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../fixtures/' ) 
                       .PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../libs/' ) 
                       .PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

require_once 'PHPUnit/Framework/TestCase.php';
require_once '../libs/PHPUnit/Fixture.php';

class BasicFixture extends PHPUnit_Fixture {}

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
	 * Now we want to make sure that that the test data we are getting
	 * is what we expected.
	 * 
	 */
	function testGetTestDataReturnsTheDataWeExpected() {
		$expected = $this->_testFix->_testData;
		$actual = $this->_testFix->getTestData();
		$this->assertSame($expected,$actual);
	}
	
	/**
	 * Now what about returning a specific test data
	 * 
	 */
	function testGetTestDataReturnsExpectedSingleResult() {
		$expected = $this->_testFix->getTestData('id',1);
		$actual = $this->_testFix->_testData[0];
		$this->assertSame($expected,$actual);
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
        $testData = $this->_testFix->_testData[0];
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
		$testData = $this->_testFix->_testData[0];
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
		$actual[] = $this->_testFix->_testData[0];
		$actual[] = $this->_testFix->_testData[2];
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
		$testData[] = $this->_testFix->_testData[0];
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
		$testData[] = $this->_testFix->_testData[0];
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
		$testData[] = $this->_testFix->_testData[0];
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
		$testData[] = $this->_testFix->_testData[0];
		$result = $this->_testFix->testDataExists($testData);
		$this->assertTrue($result);
	}
	
	/**
	 * If testDataExists finds no test data we want to return false.
	 * 
	 */
	function testTestDataExistsFalseIfNoTestDataIsPresent() {
		$testData[] = $this->_basicFix->_testData[0];
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
	 * @todo Need to refactor and replace dropFixtureTable with Fixture's
	 *       implementation, once it is done.
	 * 
	 */
	function testSetupFixtureTableReturnsTrueIfSetupFixtureTableSucceeds() {
		$result = $this->_testFix->setupFixtureTable();
		$this->assertTrue($result);
		$this->_testFix->dropFixtureTable();
	}
	
	/**
	 * If our fixture does not have a table name set we need to handle it.
	 * 
	 */
	function testSetupFixtureTableThrowsExceptionIfTableNameIsNotSet() {
		$this->_basicFix->_testData = $this->_testFix->_testData[0];
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setupFixtureTable();
	}
	
	/**
	 * If we are not able to build our fixtures table we need to return
	 * false.
	 * 
	 */
	function testSetupFixtureTableReturnsFalseIfUnableToCreateFixturesTable() {
		$this->_basicFix->_fields = $this->_testFix->_fields;
		$result = $this->_basicFix->setupFixtureTable();
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
		$this->_basicFix->_table = 'blah';
		$this->_basicFix->_testData = $this->_testFix->_testData[0];
		$this->setExpectedException('ErrorException');
		$this->_basicFix->setupFixtureTable();
	}
	
	/**
	 * Now we need to be able to actually build our fixture table, this
	 * will be done by actually calling FixturesManagers method.
	 * 
	 */
	function testSetupFixtureTableReturnsTrueIfFixtureTableIsSuccessfullyBuilt() {
		$result = $this->_testFix->setupFixtureTable();
		$this->assertTrue($result);
        $this->_testFix->dropFixtureTable();
	}
	
	/**
	 * Silly oversight, because we're using a real db & not cleaning our its results
	 * each test we need to create a drop method to remove all our fixture data.
	 * This'll be just a simple wrapper method that will use FixtureManager to remove
	 * the table.
	 * 
	 */
	function testDropFixtureTableReturnsFalseOnFailure() {
		$result = $this->_testFix->dropFixtureTable();
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
		$this->_testFix->setupFixtureTable();
		$result = $this->_testFix->dropFixtureTable();
        $this->assertTrue($result);
	}
	
	/**
	 * Ok, now we have implemented dropFixtureTable, we will use
	 * it to keep our tests clean, ideally this will be used within
	 * the tearDown() method.
	 * 
	 */
	
	/**
	 * First off if we haven't built the fixture table, we need to throw
	 * an error.
	 * 
	 */
	function testPopulateFixturesThrowsExceptionIfTableNameIsEmpty() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->populateFixtures();
	}
	
	/**
	 * If we dont have a fixtures table, we need to throw an
	 * exception.
	 */
	function testPopulateFixturesThrowsExceptionIfTableIsNotBuilt() {
		$this->setExpectedException('ErrorException');
		$this->_testFix->populateFixtures();
	}
	
	/**
	 * Now if our fixture table is present we need can insert out
	 * test data.
	 * 
	 */
	function testPopulateFixturesReturnsTrueIfTestDataIsSuccessfullyInserted() {
		$this->_testFix->setupFixtureTable();
		$result = $this->_testFix->populateFixtures();
		$this->assertTrue($result);
		$this->_testFix->dropFixtureTable();
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
		$this->assertEquals(8,count($this->_testFix->_testData));
	}
	
	/**
	 * Now we want to see if we can generate multiple pieces of test data.
	 * 
	 */
	function testGenerateFixtureTestDataReturnsExpectedNumberOfTestData() {
		$this->_testFix->autoGenerateTestData(23);
        $this->assertEquals(30,count($this->_testFix->_testData));
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
		$numTestData = count($this->_testFix->_testData);
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
		$this->_basicFix->getFixtureTableFields();
	}
	
	/**
	 * Now we need to test that we are able to return an array of our fixture fields
	 *
	 */
	function testGetFixtureTableFieldsReturnsAnArrayOnSuccess() {
		$result = $this->_testFix->getFixtureTableFields();
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
		$fieldData = $this->_testFix->_fields['id'];
		$result = $this->_testFix->getSingleDataTypeField('id');
		$this->assertSame($fieldData,$result);
	}
	
	/**
	 * Now if we need to be able to retrieve our testData with auto incremented id's, this will
	 * be used to retrieve test data without actually having to insert the data into our test DB.
	 */
	function testRetrieveTestDataResultsIncrementsTestDataIDWhenReturned() {
		$this->markTestIncomplete();
	}
	
}