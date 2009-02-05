<?php
/**
 *
 * FixtureTestCase
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage TestSuite_Fixture
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
 * Improved code coverage and added tests for autoGen
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
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Fixture.php';

/**
 * Created to test basic functionality of our
 * fixture class.
 *
 */
class BasicFixture extends PHPUnit_Fixture {}

/**
 * Used to test what happens when a invalid test data format is setup.
 *
 */
class FakeFixture extends PHPUnit_Fixture {
	
	protected $_fixtures = 
		array('ALIAS'=>'first', 'id' => 1, 'apple_id' => 2, 'color' => 'Red 1', 'name' => 'Red Apple 1', 'created' => '2006-11-22 10:38:58', 'date' => '1951-01-04', 'modified' => '2006-12-01 13:31:26');
}

/**
 * Our main test cases.
 *
 */
class FixtureTest extends PHPUnit_Framework_TestCase {

	/**
	 * Stores our TestFixture
	 *
	 * @var PHPUnit_Fixture
	 * 
	 */
	private $_testFix;
	
	public function __construct() {
		$this->setName ( 'PHPUnit_Fixture Testcase' );
	}
	
	public function setUp() {
		parent::setUp();
		$this->_testFix = new TestFixture();
		$this->_invalidFieldFixture = new InvalidFieldTypeFixture();
		$this->_basicFix = new BasicFixture();
	}
	
	public function tearDown() {
		$this->_testFix = null;
		$this->_invalidFieldFixture = null;
		$this->_basicFix = null;
		parent::tearDown();
	}
	
	function testConstructor() {
		$this->assertNotNull($this->_basicFix);
	}
	
	/*
	 * Test Helpers
	 */
	
	/**
	 * Used to test our generated patterns
	 *
	 * @param String $pattern	Pattern we are looking for.
	 * @param String $string	String we are going to check
	 */
	function assertPattern($pattern,$string) {
		if(!ereg($pattern,$string)) {
			$this->fail('Pattern not found in ' .$string);
		} else {
			echo 'Found pattern ' .$string;
		}
	}
	
	/*
	 * End of test helpers
	 */
	
	/**
	 * Our Fixture will need a testDataProperty.
	 */
	function testFixtureHasTestDataProperty() {
		$this->assertClassHasAttribute('_fixtures','PHPUnit_Fixture');
	}
	
	/**
	 * We'll also need a fields property to store our table field property
	 * 
	 */
	function testPHPUnitFixtureHasFieldsProperty() {
		$this->assertClassHasAttribute('_fields','PHPUnit_Fixture');
	}
	
   /**
     * If we have no fixtures we need to know. We'll create a method
     * that will return the number of fixtures within our class.
     * 
     */
    function testCountReturnsZeroIfNoFixturesArePresent() {
        $result = $this->_basicFix->count();
        $this->assertEquals(0,$result);
    }
    
    /**
     * Now we'll actually check that we have a array & if we do
     * we want to count it, otherwise we have zero.
     * 
     */
    function testCountReturnsTheExpectedNumberOfResults() {
        $result = $this->_testFix->count();
        $this->assertEquals(7,$result);
    }
    
    /**
     * If our test data is not in an expected format, a single
     * array encapsulating each piece of test data, we throw
     * an exception.
     *
     */
    function testTestDataIsntInExpectedFormatThrowException() {
    	$this->setExpectedException('Zend_Exception');
		$fake = new FakeFixture();
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
	function testgetReturnsFalseIfNoTestDataPresent() {
		$result = $this->_basicFix->get();
		$this->assertFalse($result);
	}
	
	/**
	 * If the parameter passed to get is no string we
	 * throw an exception.
	 * 
	 */
	function testgetThrowsExceptionIfParameterIsNotAString() {
		$id = array();
		$this->setExpectedException('ErrorException');
		$this->_basicFix->get($id);
	}
	
	/**
	 * Ok, now we want to make sure that if we have test data we
	 * return an array. TestFixture has test data so we will use
	 * this instead of creating actual data.
	 * 
	 */
	function testgetReturnsAnArrayIfTestDataIsPresent() {
		$result = $this->_testFix->get();
		$this->assertType('array',$result);
	}
	
	/**
	 * We forgot to test that a value is specified, if a key
	 * is, as both are need to retrieve a single test data.
	 * 
	 */
	function testgetThrowsExceptionIfKeyIsPassedButValueIsEmpty() {
		$key = 'id';
		$value = '';
		$this->setExpectedException('ErrorException');
		$this->_testFix->get($key,$value);
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
    function testaddThrowsExceptionIfTestDataIsNotAnArray() {
        $testData = '';
        $this->setExpectedException('ErrorException');
        $this->_basicFix->add($testData);
    }
    
    /**
     * If we set our test data up successfully we should get a response
     * 
     */
    function testFixtureaddReturnsTrueOnSuccess() {
        $testData = $this->_testFix->get('id',1);
        $result = $this->_basicFix->add($testData);
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
	function testaddCanSubmitTestDataToFixture() {
		$testData = array();
		$testData[] = $this->_testFix->get('id',1);
		$result = $this->_basicFix->add($testData);
		$this->assertTrue($result);
		$expected = $this->_basicFix->get();
		$this->assertSame($expected,$testData);
	}

	/**
	 * Now we want to be able to add multiples as well
	 * 
	 */
	function testaddIsAbleToAddMultiplesOfTestData() {
		$actual = array();
		$actual[] = $this->_testFix->get('id',1);
		$actual[] = $this->_testFix->get('id',3);
		$this->_basicFix->add($actual);
		$expected = $this->_basicFix->get();
		$this->assertSame($expected,$actual);
		
	}
	
	/**
	 * We want to make sure that we return false by default when
	 * using validateTestData
	 * 
	 */
	function testValidateTestDataReturnsTrueAsDefault() {
		$testData = array();
		$testData[] = $this->_testFix->get('id',1);
		$result = $this->_basicFix->validate($testData);
		$this->assertTrue($result);
	}
	
	/**
	 * 
	 * We should really test that the test data we are adding is
	 * of the same structure as the rest of predefined data.
	 * 
	 */
	function testaddThrowsExceptionsIfAddingTestDataOfVaryingStructure() {
		$testData[] = $this->_testFix->get('id',1);
		$this->setExpectedException('ErrorException');
		$invalidData[] = array('id' => 7, 'appl_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21');
		$this->_basicFix->add($testData);
		$this->_basicFix->validate($invalidData);
	}
	
	/**
	 * Now we need to check that if our invalid test data is submitted via
	 * add, we need to throw an exception. validate, should
	 * ideally be callable from within add, so that we can automatically
	 * handle validations within our addition routine.
	 * 
	 */
	function testAddTestThrowsExceptionsIfTestDataDoesNotMatchPreExistingTestDataStructure() {
		$testData[] = $this->_testFix->get('id',1);
		$this->setExpectedException('ErrorException');
		$invalidData[] = array('id' => 7, 'appl_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21');
		$this->_basicFix->add($testData);
		$this->_basicFix->add($invalidData);
	}
	
	/**
	 * Now we can add test data to our fixture class, we want to throw
	 * exceptions if when an entry is already submitted.
	 * 
	 */
	function testAddTestReturnsTrueIfTestDataIsAlreadyPresent() {
		$testData[] = $this->_testFix->get('id',1);
		$result = $this->_testFix->exists($testData);
		$this->assertTrue($result);
	}
	
	/**
	 * If exists finds no test data we want to return false.
	 * 
	 */
	function testExistsFalseIfNoTestDataIsPresent() {
		$testData[] = $this->_basicFix->get('id',1);
		$result = $this->_basicFix->exists($testData);
		$this->assertFalse($result);
	}
	
	/**
	 * We'll need this at some point to help us generate our 
	 * actul test data & add it to the fixtures test data.
	 *
	 */
	function testAutoGenReturnsFalseByDefault() {
		$result = $this->_testFix->autoGen();
		$this->assertTrue($result);
	}
	
	/**
	 * Now we want to be able to parse our fields data, 
	 * creating an array that corresponds with the fixtures schema.
	 * 
	 */
	function testAutoGenReturnsResultsAsArrayAndIdIsNotAutoSet() {
		$this->_testFix->autoGen(1);
		$this->assertEquals(8,count($this->_testFix->get()));
	}
	
	/**
	 * Now we want to see if we can generate multiple pieces of test data.
	 * 
	 */
	function testGenerateFixtureTestDataReturnsExpectedNumberOfTestData() {
		$this->_testFix->autoGen(23);
        $this->assertEquals(30,count($this->_testFix->get()));
	}
	
	/**
	 * We should get back to implementing autoGen, which will be 
	 * the wrapper method for handling all our test data generating.
	 * 
	 */
	
	/**
	 * We know _basicFix has no set fields, so we will try to generate test data
	 * for that and see what happens, we expect to see a return of false, but lets see.
	 *
	 */
	function testAutoGenReturnsFalseIfFixtureHasNotDefinedFieldsProperty() {
		$result = $this->_basicFix->autoGen();
		$this->assertFalse($result);
	}
	
	/**
	 * Sweet, so all goes well so far, lets see what happens if we add test data to _testFix.
	 * It already has 7 pieces of data, lets add another 10.
	 * 
	 */
	function testAutoGenCanAddTenPiecesOfTestDataToTestFixture() {
		$result = $this->_testFix->autoGen(10);
		$this->assertTrue($result);
		$numTestData = count($this->_testFix->get());
		$this->assertEquals(17,$numTestData);
	}
	
	/**
	 * When we try to auto gen test data with no field data, what happens?
	 * 
	 */
	function testAutoGenReturnsFalseIfFixtureHasNoFieldData() {
		$result = $this->_basicFix->autoGen();
		$this->assertFalse($result);
	}
	
	/**
	 * Now we need to create an implementation that allows us to retrieve
	 * an array of the fixture table fields.
	 * 
	 */
	function testGetFixtureTableFieldsThrowsExceptionsIfNoFieldsAreDefined() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->getFields();
	}
	
	/**
	 * Now we need to test that we are able to return an array of our fixture fields
	 *
	 */
	function testGetFixtureTableFieldsReturnsAnArrayOnSuccess() {
		$result = $this->_testFix->getFields();
		$this->assertType('array',$result);
	}
	
	/**
	 * We refactored Fixture so that _generateTestData is now private, we need to cover
	 * the tests that were previously used on _generateTestData
	 *
	 * The following will cause autoGen to return false. 
	 */
	function testAutoGenReturnsFalseAndCatchesErrorExceptionIfTestDataIsNotSet() {
		$result = $this->_basicFix->autoGen(1);
		$this->assertFalse($result);
	}
	
	function testAutoGenReturnsFalseAndCatchesErrorExceptionIfFieldsHasAnEntryWithNoType() {
		$result = $this->_invalidFieldFixture->autoGen(1);
		$this->assertFalse($result);
	}

    function testAutoGenReturnsFalseAndCatchesErrorExceptionIfParamIsZero() {
        $result = $this->_invalidFieldFixture->autoGen(0);
        $this->assertFalse($result);
    }
    
	function testAutoGenReturnsFalseAndCatchesErrorExceptionIfParamIsNotInt() {
        $result = $this->_invalidFieldFixture->autoGen('23');
        $this->assertFalse($result);
    }
	
	function testAutoGenReturnsFalseAndCatchesErrorExceptionIfDataTypeLengthSpecifiedWithDateAndDateTime() {
		$this->_invalidFieldFixture->_fields = array('id' => array('type' => 'date', 'length' => '10', 'null' => FALSE));
		$result = $this->_invalidFieldFixture->autoGen(1); 
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
		$this->_testFix->getField(array());
	}
	
	/**
	 * Now we need to check that our field id is valid
	 * 
	 */
	function testRetrieveSingleTestDataFieldThrowsExceptionIfFieldIsInvalid() {
		$this->setExpectedException('ErrorException');
		$this->_testFix->getField('bid');
	}
	
	/**
	 * Now we need to make sure that we return an array
	 * @todo create tests to implement data type verification.
	 * 
	 */
	function testRetrieveSingleTestDataFieldReturnAnArrayOnSucces() {
		$result = $this->_testFix->getField('id');
		$this->assertType('array',$result);
	}
	
	function testRetrieveSingleTestDataFieldReturnExpected() {
		$fieldData = $this->_testFix->getField('id');
		$result = $this->_testFix->getField('id');
		$this->assertSame($fieldData,$result);
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
        $result = $this->_basicFix->setFields($this->_testFix->getFields());
        $this->assertTrue($result);
    }
    
    /**
     * Now we'll make sure that our new field data is actually set.
     *  
     */
    function testSetDataFieldsActuallySetsOurFieldsProperty() {
    	$expected = $this->_testFix->getFields();
    	$result = $this->_basicFix->setFields($expected);
    	$actual = $this->_basicFix->getFields();
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
     * Using the retrieveTestDataResult without an id should produce an exception,
     * seeing as we would only use this functionality if/when test data has a id field
     * if this is not the case we throw an exception.
     */
    function testRetrieveResultsThrowsExceptionIfIDFieldDoesNotExists() {
    	$fields = $this->_testFix->getFields();
    	unset($fields['id']);
    	$testData = array(
    	       array('userid' => 1,'title' => 'new feature','description' => 'To test a new feature','addeddate'=>'2008-10-07')
    	);
    	$this->_basicFix->setFields($fields);
    	$this->_basicFix->add($testData);
    	$this->setExpectedException('ErrorException');
    	$this->_basicFix->retrieveResults();
    }
    
	/**
	 * Now if we need to be able to retrieve our testData with auto incremented id's, this will
	 * be used to retrieve test data without actually having to insert the data into our test DB.
	 */
	function testRetrieveResultsReturnsArrayOnSuccess() {
		$result = $this->_testFix->retrieveResults();
		$this->assertType('array',$result);
	}
	
	/**
	 * We need to make sure that if we have and null id's, that we
	 * incrementally change them to an integer.
	 * 
	 */
	function testRetrieveResultsReturnsArrayAndIdsAreNotNull() {
		$this->_basicFix->setFields($this->_testFix->getFields());
		$this->assertTrue($this->_basicFix->autoGen(20));
		$data = $this->_basicFix->retrieveResults();
		for($i=0;$i<$this->_basicFix->count();$i++) {
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
		$this->_testFix->autoGen(20);
        $data = $this->_testFix->retrieveResults();
		for($i=0;$i<$this->_testFix->count();$i++) {
			$this->assertEquals($i+1,$data[$i]['id']);
		}
	}
	
	function testAutoGenThrowsException() {
		$result = $this->_basicFix->autoGen(20);
		$this->assertFalse($result);
	}
	
	function testvalidateThrowsExceptionIfTestDataHasNoData() {
		$this->setExpectedException('ErrorException');
		$this->_basicFix->validate(false);
	}
	
	/**
	 * The TMZ should be set in configs, if it is not present we should
	 * throw an exception.
	 */
	
	/**
	 * First we need to make sure that the TMZ being passed is actually valid
	 * we'll need to use DateTimeZone::listIdentifiers() for this.
	 *
	 */	
	function testConstructorThrowsExceptionIfErrorWithSettingTimeZone() {
		$this->markTestSkipped('Need a way of actually testing.');
		$this->setExpectedException('ErrorException');
		new BasicFixture('blah/blah');
	}
	
	/**
	 * We want a new feature that will allow us to remove a single piece
	 * of test data from our list, this will be handy in the times when
	 * invalid test data is placed inside a PHPUnit_Fixture child & we
	 * want to populate a db with our test data, whilst making sure that our
	 * insertation only deals with valid data. 
	 */
	
	/**
	 * First off we want true of false to be returned on success or failure
	 * 
	 */
	function testremoveReturnsFalseOnFailure() {
		$result = $this->_testFix->remove('id',40);
		$this->assertFalse($result);
	}
	
	function testremoveThrowsExceptionIfKeyDoesNotExist() {
		$this->setExpectedException('ErrorException');
		$this->_testFix->remove('nid',1);
	}
	
	/**
	 * The test data only goes up to 7 so we will use 10 as the value,
	 * doing so will make sure that we can not actually remove invalid test
	 * data & return false.
	 * 
	 */
	function testremoveReturnsFalseIfValueIsInvalid() {
        $result = $this->_testFix->remove('id',10);
        $this->assertFalse($result);
	}
	
	function testremoveReturnsTrueIfTestDataIsFound() {
		$result = $this->_testFix->remove('id',1);
		$this->assertTrue($result);
	}
	
	/**
	 * we expect our test data to come up to 1 less than what we 
	 * initially had., so here we want to make sure that the pieces
     * of test data equal 
	 *
	 */
	function testremoveActuallyRemovesExpectedTestData() {
		$expected = $this->_testFix->count() -1;
		$this->_testFix->remove('id',1);
		$actual = $this->_testFix->count();
		$this->assertEquals($expected,$actual);
	}
	
	/**
	 * We want to be able to get a fixture by using an alias,
	 * this alias is optional and will only return a fixture if
	 * the corresponding alias is found.
	 */
	function testFindReturnsFalseIfAliasNotFound() {
		$this->assertFalse($this->_testFix->find('blah'));
	}
	
	function testFindReturnsArrayIfAliasIsFound() {
		$this->assertType('array', $this->_testFix->find('first'));
	}
	
	function testFindReturnsWhatWeExpect() {
		$expected = array('id' => 1, 'apple_id' => 2, 'color' => 'Red 1', 'name' => 'Red Apple 1', 'created' => '2006-11-22 10:38:58', 'date' => '1951-01-04', 'modified' => '2006-12-01 13:31:26');
		$this->assertSame($expected,$this->_testFix->find('first'));
	}
	
	function testSetReturnsBool() {
		$this->assertType('bool',$this->_testFix->addAlias(0,''));
	}
	
	function testSetAliasReturnsFalseIfUnableToAddAlias() {
		$this->assertFalse($this->_testFix->addAlias(0,'first'));
	}
	
	function testSetAliasReturnsTrueIfAliasKeyNotFound() {
		$this->assertTrue($this->_testFix->addAlias(1,'second'));		
	}
	
	function testSetAliasActuallySetsAliasIfNoneAreAlreadySet() {
		$this->assertTrue($this->_testFix->addAlias(1,'second'));
		$actual = $this->_testFix->find('second');
		$expected = array('id' => 2, 'apple_id' => 1, 'color' => 'Bright Red 1', 'name' => 'Bright Red Apple', 'created' => '2006-11-22 10:43:13', 'date' => '2014-01-01', 'modified' => '2006-11-30 18:38:10');
		$this->assertSame($expected,$actual);
	}
	
	/**
	 * Now we are able to add aliases, we should really implement functionality
	 * to remove this key when retrieving our test data as it will not be needed.
	 * 
	 */
	function testRetrieveResultsDoesNotReturnAnyFixturesWithALIASes() {
		$result = $this->_testFix->retrieveResults();
		foreach($result as $data) {
			$this->assertArrayNotHasKey('ALIAS', $data);
		}
	}
	
	/**
	 * We now want to check that get also does not include our ALIAS key
	 */
	function testgetDoesNotReturnTheALIASKeyAlongWithTheResults() {
		$result = $this->_testFix->get('id',1);
		$this->assertArrayNotHasKey('ALIAS', $result);
	}
	
	function testgetWithNoParamsDoesNotReturnTheALIASKeyAlongWithTheResults() {
		$result = $this->_testFix->get();
		foreach ($result as $data) {
			$this->assertArrayNotHasKey('ALIAS', $data);
		}
	}
	
	/**
	 * We now want to make sure that we can modify pre-existing aliases. By default
	 * if a alias doesn't already have an alias we throw an exception.
	 */
	function testModAliasThrowsExceptionIfExistingAliasIsNotFound() {
		$this->assertFalse($this->_testFix->modAlias('blah','newAlias'));
	}
	
	function testModAliasReturnsTrueIfAliasModified() {
		$this->_testFix->find('first');
		$result = $this->_testFix->modAlias('first','second');
		$this->assertTrue($result);
	}
	
	function testModAliasActuallyModifiesExistingAlias() {
		$expected = $this->_testFix->find('first');
		$this->_testFix->modAlias('first','second');
		$actual = $this->_testFix->find('second');
		$this->assertSame($expected,$actual);
	}

	/**
	 * Refactored from SecurityTest, as we will be using
	 * this functionality in our fixtures.
	 */
	public function testGenerateMethodMakesRandomNumber() {
		$number1 = $this->_testFix->generate();
		$this->assertNotNull($number1);
		$number2 = $this->_testFix->generate();
		$this->assertNotNull($number2);
		$this->assertEquals(8, strlen($number2));
		$this->assertNotEquals($number1, $number2);
	}

	/**
	 * We want to be able to generate different types of data
	 * depending on the type passed to generate, we want to be
	 * able to use this to help us get specific test data when
	 * our input systems require a certain type.
	 *
	 * 
	 * Below we make sure that we can match these patterns, with the 
	 * default max value, once this is done, we test for differing lengths.
	 */
	function testGenerateGeneratesAlphaLowerByDefault() {
		$pattern = '[a-z]{8}';
		$result = $this->_basicFix->generate();
		$this->assertPattern($pattern,$result);
	}
	
	function testGenerateGeneratesAlphanumericWhenALPHNUMIsPassedAsType() {
		$pattern = '[a-zA-Z0-9]{8}';
		$result = $this->_basicFix->generate('ALPHNUM');
		$this->assertPattern($pattern,$result);
	}
	
	function testGenerateGeneratesAlphaWhenALPHUpIsPassedAsType() {
		$pattern = '[A-Z]{8}';
		$result = $this->_basicFix->generate('ALPHUP');
		$this->assertPattern($pattern,$result);
	}
	
	function testGenerateGeneratesAlphaWhenALPHIsPassedAsType() {
		$pattern = '[a-zA-Z]{8}';
		$result = $this->_basicFix->generate('ALPH');
		$this->assertPattern($pattern,$result);
	}
	
	function testGenerateGeneratesNumbersWhenNUMIsPassedAsType() {
		$pattern = '[0-9]{8}';
		$result = $this->_basicFix->generate('NUM');
		$this->assertPattern($pattern,$result);
	}
	
	/**
	 * Now we have tested the types we can focus on generated larger string,
	 * we'll loop through 10 times, creating a min number which we'll pass to
	 * our generate function, this will then be checked against the number of 
	 * characters per result. 
	 * 
	 */
	function testGenerateReturnsExpectedAmountOfCharactersWhenMinIsPassedAsParam() {
		for($i=0;$i<100;$i++) {
			$max = mt_rand(5,10);
			$result = $this->_basicFix->generate('',$max, $max-4);
			//echo $result .' - ' .$min .PHP_EOL;
			$this->assertLessThanOrEqual($max,strlen($result));		
		}
	}
	
	/*
	 * Now we want to be able to pass generate a min & max parameter, which will
	 * allow the method to create a more random string.
	 */
	function testGenerateReturnsExpectedRangeAmountsWhenMinAndMaxParamsPassed() {
		$expected = 10;
		$actual = $this->_basicFix->generate('',$expected,3);
		$this->assertLessThanOrEqual($expected,strlen($actual));
	}
	
	function testGenerateThrowsExceptionIfMinIsGreaterThanMax() {
		$this->setExpectedException('Zend_Exception');
		$this->_basicFix->generate('',3,6);
	}
	
	/**
	 * Want to make sure the don't get any duplicate data, which will
	 * cause problems when we need unique data for our fixtures.
	 *
	 */
	function testGenerateMethodMakesRandomStringDoesNotDuplicate() {
		$newData = array();
		$duplicateCount = 0;
		$loopNum = 500;
		for($i=0;$i<$loopNum;$i++) {
			$newData[] = $this->_basicFix->generate();
		}
		foreach ($newData as $data) {
			$duplicateCount = 0;
			for($i=0;$i<$loopNum;$i++) {
				//echo $data . ' - ' .$newData[$i].PHP_EOL;
				if( $newData[$i] === $data) {
					$duplicateCount++;
				}
				if($duplicateCount >=2) {
					$this->fail($duplicateCount .' duplicate entries ' .$data .' - ' .$newData[$i]);
				}
			}
		}
	}
}