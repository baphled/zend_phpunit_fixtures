<?php
/**
 *
 * FixtureTestCase
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: Aug 31, 2008
 * Started basic test cases for implementing out fixture class,
 * which will allow us to subclass it giving us the ability to
 * preset specific details (table, fields & test data namely).
 * Complete test units to implement the get test data functionality
 * of the system.
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../libs/' ) . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../application/default/models/' . PATH_SEPARATOR . get_include_path () );

require_once '../fixtures/TestFixture.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class BasicFixture extends PHPUnit_Fixture {}

class FixtureTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_fixtures;
	
	private $_testFix;
	
	public function __construct() {
		$this->setName ( 'FixtureTest Case' );
	}
	
	public function setUp() {
		$this->_setUpConfig ();
		parent::setUp ();
		$this->_fixture = new PHPUnit_Fixture();
		$this->_testFix = new TestFixture();
		$this->_basicFix = new BasicFixture();
	}
	
	public function tearDown() {
		$this->_fixture = null;
		$this->_testFix = null;
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
	 * Now what about return a specific test data
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
		$testData[] = $this->_testix->_testData[0];
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
}