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

require_once '../libs/FixturesManager.php';

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
	
	private function _getAppleFixtureDataStructure() {
	   $fixtures = array(
	   array('id' => 1, 'apple_id' => 2, 'color' => 'Red 1', 'name' => 'Red Apple 1', 'created' => '2006-11-22 10:38:58', 'date' => '1951-01-04', 'modified' => '2006-12-01 13:31:26'),
       array('id' => 2, 'apple_id' => 1, 'color' => 'Bright Red 1', 'name' => 'Bright Red Apple', 'created' => '2006-11-22 10:43:13', 'date' => '2014-01-01', 'modified' => '2006-11-30 18:38:10'),
       array('id' => 3, 'apple_id' => 2, 'color' => 'blue green', 'name' => 'green blue', 'created' => '2006-12-25 05:13:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:24'),
       array('id' => 6, 'apple_id' => 2, 'color' => 'Blue Green', 'name' => 'Test Name', 'created' => '2006-12-25 05:23:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:36'),
       array('id' => 7, 'apple_id' => 7, 'color' => 'Green', 'name' => 'Blue Green', 'created' => '2006-12-25 05:24:06', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:16'),
       array('id' => 8, 'apple_id' => 6, 'color' => 'My new appleOrange', 'name' => 'My new apple', 'created' => '2006-12-25 05:29:39', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:39'),
       array('id' => 9, 'apple_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21')
       );
       return $fixtures;
	}
	
	private function _getTestAppleTableStructure() {
        $fields = array(
            'id' => array('type' => 'integer', 'length' => 10, 'key' => 'primary'),
            'apple_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'color' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'name' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'created' => array('type' => 'datetime', 'null' => FALSE),
            'date' => array('type' => 'date', 'null' => FALSE),
            'modified' => array('type' => 'datetime', 'null' => FALSE)
        );
        return $fields;
    }
    private function _getDateDataType() {
        return array('date' => array('type' => 'date', 'null' => FALSE));
    }
    
    private function _getDateTimeDataType() {
    	return array('created' => array('type' => 'datetime', 'null' => FALSE));
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
		$this->_fixturesManager->convertDataType($fields);
	}
	
	/**
	 * We want to convert our string datatype into
	 * its correct sql syntax.
	 * varchar(255) default ='', these will be appended
	 * to an string
	 */
	function testDoesBuildQueryReturnAString() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->convertDataType($fields,'blah');
		$this->assertType('string',$result); 
	}
	
	/**
	 * Make sure that our string start with 'CREATE TABLE'
	 * 
	 */
	function testDoesConstructQueryReturnStringContainingCreateTable() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->convertDataType($fields,'blah');
		$this->assertContains('CREATE TABLE', $result);
	}
	
	/**
	 * Now does the query include the correct table name
	 * 
	 */
	function testDoesConstructQueryReturnContainTheCorrectTableName() {
		$fields = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->convertDataType($fields,'fakeTable');
		$this->assertContains('fakeTable',$result);
	}
	
	/**
	 * Test that we can that our string
	 * datatypes are converted properly.
	 * 
	 */
	function testStringDataTypesConvertedToVarchar() {
		$dataType = $this->_getStringDataType();
		$result = $this->_fixturesManager->convertDataType($dataType);
		$this->assertContains('model',$result);
	}
	
	/**
	 * convert fails if datatype is empty
	 * 
	 */
	function testConvertDataTypeThrowsExceptionOnInvalidDataType() {
		$dataType ='not a datatype';
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->convertDataType($dataType);	
	}
	
    /**
     * We need to make sure that our return data
     * has a field name.
     * 
     */
    function testConvertDataTypesReturnsValueWithFieldName() {
    	$name = 'model';
    	$fields = $this->_getStringDataType();
    	$result = $this->_fixturesManager->convertDataType($fields);
        $this->assertContains($name,$result);
    }
    
    /**
     * If convert datatype sees type as string, we need to change
     * it to a varchar.
     * 
     */
    function testConvertDataTypeConvertsStringToVarChar() {
    	$dataType = $this->_getStringDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType);
    	$this->assertContains('VARCHAR(255)',$result);
    }
    
    /**
     * Make sure that we are able to set a default for our tables,
     * this is depicted by the default value within our field array.
     * 
     */
    function testConvertDataTypeHandlesDefaultValues() {
    	$dataType = $this->_getStringDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType);
    	$this->assertContains('DEFAULT ""',$result);
    }
    
    /**
     * Now lets make sure we can actually assign a default value
     * 
     */
    function testConvertDataTypeHandlesAbleToSetDefaultDataOnStrings() {
    	$dataType = $this->_getStringDataTypeWithDefault();
    	$result = $this->_fixturesManager->convertDataType($dataType);
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
    	$result = $this->_fixturesManager->convertDataType($dataType);
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
    	$result = $this->_fixturesManager->convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Now we check that our can parse nulls to integer datatypes
     * 
     */
    function testConvertDataTypeParseNullsInIntegerDataTypes() {
    	$query = 'parent_id INT(10) NULL';
    	$dataType = $this->_getIntegerDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Ok now what about if we want to make our int data type not null>
     */
    function testConvertDataTypeParseNotNullInIntegerDataTypes() {
    	$query = 'parent_id INT(10) NOT NULL';
    	$dataType = $this->_getIntegerDataTypeWithNotNull();
        $result = $this->_fixturesManager->convertDataType($dataType);
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
    	$result = $this->_fixturesManager->convertDataType($dataType);
        $this->assertContains($query,$result);
    }
    
    /**
     * Now we need to check that we can parse primary keys
     * 
     */
    function testConvertDataTypeCanParsePrimaryKeys() {
    	$query = 'id INT(11) PRIMARY KEY AUTO_INCREMENT';
        $dataType = $this->_getPrimaryKeyDataType();
        $result = $this->_fixturesManager->convertDataType($dataType);
        $this->assertContains($query,$result);
    }
    
    /**
     * Can we parse a date type?
     * 
     */
    function testConvertDataTypeCanParseDate() {
    	$query = 'DATE';
    	$dataType = $this->_getDateDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType);
    	$this->assertContains($query,$result);
    }
    
    /**
     * Make sure that our date datatype is created as a proper
     * SQL query.
     */
    function testConvertDataTypeReturnsExpectedQueryFromParsedDate() {
    	$query = 'CREATE TABLE pool (date DATE NOT NULL);';
    	$table = 'pool';
    	$dataType = $this->_getDateDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Next we need to make sure we can parse datetime datatypes
     * 
     */
    function testConvertDataTypeCanParseDateTimeDataTypes() {
    	$query = 'CREATE TABLE snooker (created DATETIME NOT NULL);';
    	$table = 'snooker';
    	$dataType = $this->_getDateTimeDataType();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Now we have a method of parsing date & datetime,
     * we can now check that we can parse our mixed datatype array,
     * which will be used to further test our later units.
     * 
     */
    function testConvertDataTypeReturnsExpectedQueryString() {
    	$query = 'CREATE TABLE apples (id INT(10) PRIMARY KEY AUTO_INCREMENT, apple_id INT(10) NULL, color VARCHAR(255) DEFAULT "", name VARCHAR(255) DEFAULT "", created DATETIME NOT NULL, date DATE NOT NULL, modified DATETIME NOT NULL);';
    	$table = 'apples';
    	$dataType = $this->_getTestAppleTableStructure();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Though we can parse our date & datetime datatypes, we will check that
     * our query is built as expected.
     * 
     */
    function testConvertDataTypeParsesDateAndSetsDefaultToCurrentDate() {
    	$query = 'CREATE TABLE side (id INT(10) PRIMARY KEY AUTO_INCREMENT, apple_id INT(10) NULL, color VARCHAR(255) DEFAULT "", name VARCHAR(255) DEFAULT "", created DATETIME NOT NULL, date DATE NOT NULL, modified DATETIME NOT NULL);';
    	$table = 'side';
    	$dataType = $this->_getTestAppleTableStructure();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
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
    	$result = $this->_fixturesManager->convertDataType($dataType,'blah');
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Ok we have to refactor the table name into the method
     * 
     */
    function testConvertDataTypeNowTakesTableNameAsParam() {
    	$query = $this->_getGenericQuery();
    	$dataType = $this->_getTestTableStructure();
    	$result = $this->_fixturesManager->convertDataType($dataType,'blah');
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
    	$this->_fixturesManager->convertDataType($dataType);
    }
    
	/**
	 * We'll work on turning our arrays into data types
	 * 
	 */
	function testTurnFixtureFieldsArrayIntoString() {
		$query = 'CREATE TABLE nufix (id INT(10) PRIMARY KEY AUTO_INCREMENT, parent_id INT(10) NULL, model VARCHAR(255) DEFAULT "", alias VARCHAR(255) DEFAULT "", lft INT(10) NULL, rght INT(10) NULL);';
		$result = $this->_fixturesManager->convertDataType($this->_getTestTableStructure(),'nufix');
		$this->assertEquals($query,$result);		
	}
	
	/**
	 * Now we have gotten to our base point, we can now
	 * turn arrays into SQL queries.
	 * 
	 * We need to now be able to add fixtures to our newly
	 * created table.
	 * 
	 */
	function testBuildFixtureTableReturnsTrue() {
		$dataType = $this->_getTestTableStructure();
		$result = $this->_fixturesManager->buildFixtureTable($dataType,'info');
		$this->assertTrue($result);
	}
	
	/**
	 * If table name is empty we need to throw an exception.
	 *
	 */
	function testBuildFixtureTableThrowsExceptionOnEmptyTableName() {
		$tableName = '';
		$dataType = $this->_getTestTableStructure();
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->buildFixtureTable($dataType,$tableName);
	}
	
	/**
	 * What happens if we try to build a table using an invalid query.
	 * convertDataTypeShouldThrowAnError.
	 * 
	 */
	function testBuildFixtureTableShouldReturnFalseIfDataTypeInvalidLength() {
		$tableName = 'blah';
		$dataType = $this->_getIllegalDataTypeTestTableStructure();
		$result = $this->_fixturesManager->buildFixtureTable($dataType,$tableName);
		$this->assertFalse($result);
	}
	
	/**
	 * If we successfully convert the datatype into an query
	 * we return true, else we return false.
	 * 
	 */
	function testBuildFixtureTableShouldReturnFalseOnFailure() {
		$tableName = 'tennis';
		$dataType = $this->_getAppleFixtureDataStructure();   // invalid fixture structure.
		$result = $this->_fixturesManager->buildFixtureTable($dataType,$tableName);
		$this->assertFalse($result);
	}
	
	/**
	 * What does Zend_Db_Table::query return
	 * 
	 */
	function testZendDbTableQueryReturnsWhatOnFailure() {
		
	}
	
	/**
	 * Ok, now we will need a DB creation method to actually make our
	 * table for us. This method will return false on failure & true
	 * on success.
	 * 
	 */
	function testMakeDBTableReturnsFalseOnFailure() {
		$query = 'CREATE TABLE SFSFSDsdsd;';
		$this->setExpectedException('ErrorException');
		$result = $this->_fixturesManager->_makeDBTable($query);
	}
	
	/**
	 * Here we need to make sure that anything without CREATE TABLE
	 * is dismissed and thrown as an exception
	 */
	function testMakeDBTableThrowsExceptionIfPassedANonCreateTableQuery() {
		$query = 'sfdsfa';
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->_makeDBTable($query);
		
	}
	
	/**
	 * We now need to see what happens when we pass a legal query
	 * 
	 */
	function testMakeDBTableReturnsTrueOnSuccess() {
		$query = $this->_getGenericQuery();
		$result = $this->_fixturesManager->_makeDBTable($query);
		$this->assertTrue($result);
	}
	
	/**
	 * Now we need to be able to insert data into our dynamic tables.
	 *  
	 */
	
	/**
	 * Our buildInsertQuery method needs to contain INSERT INTO
	 * 
	 */
	function testBuildInsertQueryReturnsInsertInto() {
		$insertData = $this->_getAppleFixtureDataStructure();
		$result = $this->_fixturesManager->_buildInsertQuery($insertData);
		$this->assertContains('INSERT INTO',$result);
	}
	
	/**
	 * What happens when we try to insert an invalid insert query
	 * 
	 */
	function testQueryStatement() {
	   $db = Zend_Registry::get('db');
	   $query = 'sfsadf';
	   $this->setExpectedException('PDOException');
	   new Zend_Db_Statement_Mysqli($db,$query);
	}
}