<?php
/**
 *
 * DBTestFixturesTestCase
 * 
 * Used to work with the fixtures we'll need to run
 * our test cases.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: 20/08/2008
 * Finished 2nd session, we have now implemented convertDataType,
 * we'll refactor next session & then move on to storing, retrieving
 * fixtures.
 * 
 * Date: 19/08/2008
 * Started session, will implement basic functionality needed
 * to create the db and import the needed fixtures.
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR .realpath(dirname(__FILE__) .'/../libs/') . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR .dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../application/default/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

require_once 'FixturesManager.php';

class FixturesManagerTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_fixturesManager;
	
	public function __construct() {
		$this->setName ( 'FixturesManagerTest Case' );
	}
	
	public function setUp() {
		$this->_setUpConfig ();
		parent::setUp ();
		$this->_fixturesManager = new FixturesManager();
	}
	
	private function _getGenericQuery() {
		return 'CREATE TABLE blah (id INT(10) PRIMARY KEY AUTO_INCREMENT, parent_id INT(10) NULL, model VARCHAR(255) DEFAULT "", alias VARCHAR(255) DEFAULT "", lft INT(10) NULL, rght INT(10) NULL);';
	}
	private function _getPrimaryKeyDataType() {
		return array('id' => array('type' => 'integer', 'length' => 11, 'key' => 'primary'));
	}
	
	private function _getTestTableStructure() {
        $fields = array(
            'id' => array('type' => 'integer', 'length' => 10, 'key' => 'primary'),
            'parent_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'model' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'alias' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
        );
        return $fields;
	}
	
	private function _getTestTableStructureWithNoPrimKey() {
        $fields = array(
            'id' => array('type' => 'integer'),
            'parent_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'model' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'alias' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
        );
        return $fields;
    }
    
    private function _getInvalidTestTableStructure() {
            $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'parent_id' => array('typed' => 'integer', 'length' => 10, 'null' => true),
            'model' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'alias' => array('type' => 'string', 'default' => ''),
            'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
        );
        return $fields;
    }
    
    private function _getIntegerDataType() {
        $dataType = array('parent_id' => array('type' => 'integer', 'length' => 10, 'null' => true));
        return $dataType;
    }
    
    private function _getIntegerDataTypeWithNotNull() {
        $dataType = array('parent_id' => array('type' => 'integer', 'length' => 10, 'null' => false));
        return $dataType;
    }
    private function _getStringDataType() {
    	$dataType = array( 'model' => array('type' => 'string', 'length' => 255, 'default' => ''));
    	return $dataType;
    }
    
    private function _getStringDataTypeWithDefault() {
        $dataType = array( 'model' => array('type' => 'string', 'length' => 255, 'default' => 'sum text'));
        return $dataType;
    }
    private function _getIllegalDataTypeTestTableStructure() {
            $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'parent_id' => array('typed' => 'integer', 'length' => 10, 'null' => true),
            'model' => array('type' => 'strong', 'length' => 255, 'default' => ''),
            'alias' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
        );
        return $fields;
    }
	
	public function tearDown() {
		$this->_fixturesManager = null;
		parent::tearDown ();
	}
	
	function testConstructor() {
		$this->assertNotNull($this->_fixturesManager);
	}
	
	/**
	 * This test is purely used to help work out the
	 * implementation of the fixtures fields array,
	 * which I've borrowed from phpcake.
	 * 
	 */
	function testFixtureDataHasDataTypes() {
		$fields = $this->_getTestTableStructure();
		foreach ($fields as $field) {
			$this->assertArrayHasKey('type',$field);	
		}
	}
    
	/**
	 * How will we make sure that our array has
	 * a valid data type.
	 *  
	 */
	function testFieldsArrayStoresCorrectDataType() {
		$fields = $this->_getIllegalDataTypeTestTableStructure();
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->_convertDataType($fields);
	}
	
	/**
	 * We want to convert our string datatype into
	 * its correct sql syntax.
	 * varchar(255) default ='', these will be appended
	 * to an string
	 */
	function testDoesBuildQueryReturnAString() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->_convertDataType($fields,'blah');
		$this->assertType('string',$result); 
	}
	
	/**
	 * Make sure that our string start with 'CREATE TABLE'
	 * 
	 */
	function testDoesConstructQueryReturnStringContainingCreateTable() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->_convertDataType($fields,'blah');
		$this->assertContains('CREATE TABLE', $result);
	}
	
	/**
	 * Now does the query include the correct table name
	 * 
	 */
	function testDoesConstructQueryReturnContainTheCorrectTableName() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->_convertDataType($fields,'fakeTable');
		$this->assertContains('fakeTable',$result);
	}
	
	/**
	 * Test that we can that our string
	 * datatypes are converted properly.
	 * 
	 */
	function testStringDataTypesConvertedToVarchar() {
		$dataType = $this->_getStringDataType();
		$result = $this->_fixturesManager->_convertDataType($dataType);
		$this->assertContains('model',$result);
	}
	
	/**
	 * convert fails if datatype is empty
	 * 
	 */
	function testConvertDataTypeThrowsExceptionOnInvalidDataType() {
		$dataType ='not a datatype';
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->_convertDataType($dataType);	
	}
	
    /**
     * We need to make sure that our return data
     * has a field name.
     * 
     */
    function testConvertDataTypesReturnsValueWithFieldName() {
    	$name = 'model';
    	$fields = $this->_getStringDataType();
    	$result = $this->_fixturesManager->_convertDataType($fields);
        $this->assertContains($name,$result);
    }
    
    /**
     * If convert datatype sees type as string, we need to change
     * it to a varchar.
     * 
     */
    function testConvertDataTypeConvertsStringToVarChar() {
    	$dataType = $this->_getStringDataType();
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains('VARCHAR(255)',$result);
    }
    
    /**
     * Make sure that we are able to set a default for our tables,
     * this is depicted by the default value within our field array.
     * 
     */
    function testConvertDataTypeHandlesDefaultValues() {
    	$dataType = $this->_getStringDataType();
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains('DEFAULT ""',$result);
    }
    
    /**
     * Now lets make sure we can actually assign a default value
     * 
     */
    function testConvertDataTypeHandlesAbleToSetDefaultDataOnStrings() {
    	$dataType = $this->_getStringDataTypeWithDefault();
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains('DEFAULT "sum text"',$result);
    	//echo $result;
    }
    
    /**
     * Test we have query we expect
     * 
     */
    function testWeGetTheQuerySegmentWeExpect() {
    	$query = 'model VARCHAR(255) DEFAULT "sum text"';
    	$dataType = $this->_getStringDataTypeWithDefault();
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Now we need to test that we can convert our integers
     * into the proper format, we only need to check the start
     * of the result so we use assertContains.
     * 
     */
    function testConvertDataTypeConvertToIntegerToInt() {
    	$dataType = $this->_getIntegerDataType();
    	$query = 'parent_id INT(10)';
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Now we check that our can parse nulls to integer datatypes
     * 
     */
    function testConvertDataTypeParseNullsInIntegerDataTypes() {
    	$query = 'parent_id INT(10) NULL';
    	$dataType = $this->_getIntegerDataType();
    	$result = $this->_fixturesManager->_convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Ok now what about if we want to make our int data type not null>
     */
    function testConvertDataTypeParseNotNullInIntegerDataTypes() {
    	$query = 'parent_id INT(10) NOT NULL';
    	$dataType = $this->_getIntegerDataTypeWithNotNull();
        $result = $this->_fixturesManager->_convertDataType($dataType);
        $this->assertContains($query,$result);
    }
    
    /**
     * Just out of curiosity, we should really parse strings, so
     * that we can have custom lengths.
     * 
     */
    function testConvertDataTypeCanHandleCustomVarcharLengths() {
    	$query = 'model VARCHAR(23) DEFAULT "none"';
    	$dataType = array('model' => array('type' => 'string', 'length' =>23, 'default' => 'none'));
    	$result = $this->_fixturesManager->_convertDataType($dataType);
        $this->assertContains($query,$result);
    }
    
    /**
     * Now we need to check that we can parse primary keys
     * 
     */
    function testConvertDataTypeCanParsePrimaryKeys() {
    	$query = 'id INT(11) PRIMARY KEY AUTO_INCREMENT';
        $dataType = $this->_getPrimaryKeyDataType();
        $result = $this->_fixturesManager->_convertDataType($dataType);
        $this->assertContains($query,$result);
    }
    
    /**
     * Okay now we are pretty comfortable with with our
     * implementation so far, as far as converting out
     * fixture table arrays into legal query segments,
     * now we need to gather all theres into one, and
     * determine whether the query is what we expect.
     */
    function testCheckDataTypeCanBuildOurCreateTableQuery() {
    	$query = $this->_getGenericQuery();
    	$dataType = $this->_getTestTableStructure();
    	$result = $this->_fixturesManager->_convertDataType($dataType,'blah');
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Ok we have to refactor the table name into the method
     * 
     */
    function testConvertDataTypeNowTakesTableNameAsParam() {
    	$query = $this->_getGenericQuery();
    	$dataType = $this->_getTestTableStructure();
    	$result = $this->_fixturesManager->_convertDataType($dataType,'blah');
    	$this->assertEquals($query,$result);
    }
    /**
     * Now we need to check what happens with illegally built
     * table fixture array
     * 
     */
    function testConvertDataTypeThrowsExceptionIfParamIsInvalid() {
    	$dataType = $this->_getIllegalDataTypeTestTableStructure();
    	$this->setExpectedException('ErrorException');
    	$this->_fixturesManager->_convertDataType($dataType);
    }
    
	/**
	 * We'll work on turning our arrays into data types
	 * 
	 */
	function testTurnFixtureFieldsArrayIntoString() {
		$query = 'CREATE TABLE nufix (id INT(10) PRIMARY KEY AUTO_INCREMENT, parent_id INT(10) NULL, model VARCHAR(255) DEFAULT "", alias VARCHAR(255) DEFAULT "", lft INT(10) NULL, rght INT(10) NULL);';
		$result = $this->_fixturesManager->_convertDataType($this->_getTestTableStructure(),'nufix');		
	}
	
	/**
	 * Now we have gotten to our base point, we can now
	 * turn arrays into SQL queries.
	 * 
	 * We need to now be able to add fixtures to our newly
	 * created table.
	 * 
	 */
	function testConvertDatasQueryCanActuallyCreateTable() {
		$result = '';
		$this->assertTrue($result);
	}
}