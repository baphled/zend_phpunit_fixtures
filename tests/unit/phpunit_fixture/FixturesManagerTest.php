<?php
/**
 *
 * DBTestFixturesTestCase
 * 
 * Used to work with the fixtures we'll need to run
 * our test cases.
 * 
 * To get running you will need to configure settings.ini with corresponding
 * DB credentials.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package PHPUnit_FixturesManager
 * @subpackage TestSuite
 *
 * $LastChangedBy$
 * Date: 03/09/2008
 * Refactored test to use PHPUnit_Framework_TestCase, as we are not using any
 * Zend components directly.
 * Still working on refactoring class & test case.
 * 
 * Date: 02/09/2008
 * Added test cases to help implement our fixture table exists method, which
 * will tell us if a table already exists within our DB, will be critical as
 * we do not want to try to insert test data into a non-existent table. This will
 * be used by PHPUnit_Fixture to decide whether to insert test data into a table
 * or not.
 * Still refactoring out test on private methods, if done right we can still
 * keep our code coverage, which is 96% atm. Will concerntrate on _constructInsertQuery
 * 
 * Date: 31/08/2008
 * Put together tests to implement SQL insertion queries, via our test data
 * array. Can now build tables and insert data so will now work on cleaning
 * up and making more fluid.
 * 
 * Date: 29/08/2008
 * 5th session, working on refactoring and implementing fixtures insertion method.
 * Also cleaning up test case, removing uneeded tests, refactoring and also will try
 * to cover uncovered code.
 * 
 * Date: 28/08/2008
 * 4th session, introduced real interacting with _makeDBTable (now _runFixtureQuery)
 * to determine where our error was coming from, is now solved.
 * As mentioned have refactor _makeDBTable to a more meaningful name (_runFixtureQuery).
 * Stubbed out setupFixtureTable in testBuildFixtureTableReturnsTrue, so
 * that we don't access the DB directly, also did the same for
 * testMakeDBTableReturnsTrueOnSuccessUsingStubs for the same reason.
 * Have added tests to loop through a list of our DB tables & delete each
 * one. We will use this to automatically cleanup our fixture tables.
 * 
 * Date: 27/08/2008
 * 3rd session, refactoring tests to _makeDBTable, as it will be private
 * and is now linking to the DB.
 * Zend_DB_Statement does not throw exception if a query is illegal
 * making it hard to validate, will need to implement some validation
 * of our own in the meantime.
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
 * @todo Now we have completed the basics PHPUnit_Fixture we can now use it for our test data.
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../../fixtures/' )  
                       . PATH_SEPARATOR .realpath(dirname(__FILE__) .'/../../libs/') 
                       . PATH_SEPARATOR . get_include_path () );

require_once 'FixturesManager.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FixturesManWrapper extends FixturesManager {
	function runFixtureQuery($query) {
		$result = $this->_runFixtureQuery($query);
		if($result) {
			return true;
		}
		return false;
	}
}

class FixturesManagerTest extends PHPUnit_Framework_TestCase {
	
	private $_fixturesManager;
	
	private $_stub;
	
	public function __construct() {
		$this->setName ('FixturesManagerTest Case');
		
		/*
		 * For the moment we will only leave this here
		 * eventually we will move to its own private
		 * function.
		 * 
		 * @todo Is far from perfect, really want to
		 *       specify how many times the method is
		 *       run and what to return.
		 * 
		 */
		$this->_stub = $this->getMock('FixturesManager',array('setupFixtureTable'));
        $this->_stub->expects($this->any())
                    ->method('setupFixtureTable')
                    ->will($this->returnValue(TRUE));
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_fixturesManager = new FixturesManager();
		$this->_fixWrap = new FixturesManWrapper();
		$this->_testFixture = new TestFixture();
	}
	
    public function tearDown() {
        $this->_fixturesManager = null;
        $this->_fixMan = null;
        parent::tearDown ();
    }
    
	/*
	 * Test data starts here.
	 */
	private function _getGenericQuery($table) {
		return 'CREATE TABLE ' .$table .' (id INT(10) PRIMARY KEY AUTO_INCREMENT, apple_id INT(10) NULL, color VARCHAR(255) DEFAULT "", name VARCHAR(255) DEFAULT "", created DATETIME NOT NULL, date DATE NOT NULL, modified DATETIME NOT NULL);';
	}
	
	private function _getPrimaryKeyDataType() {
		return array('id' => array('type' => 'integer', 'length' => 11, 'key' => 'primary'));
	}
    
    private function _getDataTypeWithNoDefinedType() {
    	return array('date' => array('tipe' => 'date', 'null' => FALSE));
    }
    
    private function _getDateDataType() {
        return array('date' => array('type' => 'date', 'null' => FALSE));
    }
    
    private function _getDateTimeDataType() {
    	return array('created' => array('type' => 'datetime', 'null' => FALSE));
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
    
    /*
     * Test data finishes here.
     */
	
    /*
     * Helper functions start here.
     */
    /**
     * Seeing as we are being naughty and lazy we needed to pull this
     * out of our test units as its taking up too much space, not to
     * mention more than likely go at some stage.
     *
     * @param String $table
     */
    private function _setUpTestTableStructure($table) {
        $fixture = $this->_testFixture->getFixtureTableFields();
        $this->_fixturesManager->setupFixtureTable($fixture,$table);
    }
	
    /*
     * Helper functions finish here.
     */

    /*
     * Test units
     */
	function testConstructor() {
		$this->assertNotNull($this->_fixturesManager);
	}
	
	/**
	 * This test is purely used to help work out the
	 * implementation of the fixtures fields array,
	 * which I've stole from phpcake.
	 * 
	 */
	function testFixtureDataHasDataTypes() {
		$fields = $this->_testFixture->getFixtureTableFields();
		foreach ($fields as $field) {
			$this->assertArrayHasKey('type',$field);	
		}
	}
	
	/**
	 * Throw and exception is a date/datetime type is set with a length
	 * 
	 */
	function testCheckDataTypeThrowsExceptionIfDateOrDateTimeSpecifiedWithLength() {
		$fields = array( 'id' => array( 'type' => 'date', 'length' => '10'));
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->convertDataType($fields);
	}
	
	/**
	 * Really do not know how I missed this test. We need to make
	 * sure that each field has a type, if it doesn't we need to
	 * throw an error as the 
	 *
	 */
	function testConvertDataTypeThrowsExceptionIfNoTypeIfDefine() {
		$fields = $this->_getDataTypeWithNoDefinedType();
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->convertDataType($fields);
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
		$table = 'blah';
		$fields = $this->_testFixture->getFixtureTableFields();
		$result = $this->_fixturesManager->convertDataType($fields,$table);
		$this->assertType('string',$result); 
	}
	
	/**
	 * Make sure that our string start with 'CREATE TABLE'
	 * 
	 */
	function testDoesConstructQueryReturnStringContainingCreateTable() {
		$fields = $this->_testFixture->getFixtureTableFields();
		$result = $this->_fixturesManager->convertDataType($fields,'blah');
		$this->assertContains('CREATE TABLE', $result);
	}
	
	/**
	 * Now does the query include the correct table name
	 * 
	 */
	function testDoesConstructQueryReturnContainTheCorrectTableName() {
		$fields = $this->_testFixture->getFixtureTableFields();
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
    	$dataType = $this->_testFixture->getFixtureTableFields();
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
    	$table = 'apples';
    	$query = $this->_getGenericQuery($table);
    	$dataType = $this->_testFixture->getFixtureTableFields();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Though we can parse our date & datetime datatypes, we will check that
     * our query is built as expected.
     * 
     */
    function testConvertDataTypeParsesDateAndSetsDefaultToCurrentDate() {
    	$table = 'side';
    	$query = $this->_getGenericQuery($table);
    	$dataType = $this->_testFixture->getFixtureTableFields();
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
    	$table = 'blah';
    	$query = $this->_getGenericQuery($table);
    	$dataType = $this->_testFixture->getFixtureTableFields();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
    	$this->assertEquals($query,$result);
    }
    
    /**
     * Ok we have to refactor the table name into the method
     * 
     */
    function testConvertDataTypeNowTakesTableNameAsParam() {
    	$table = 'blah';
    	$query = $this->_getGenericQuery($table);
    	$dataType = $this->_testFixture->getFixtureTableFields();
    	$result = $this->_fixturesManager->convertDataType($dataType,$table);
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
		$table  = 'blah';
		$query = $this->_getGenericQuery($table);
		$result = $this->_fixturesManager->convertDataType($this->_testFixture->getFixtureTableFields(),$table);
		$this->assertEquals($query,$result);		
	}
	
	/**
	 * Now we have gotten to our base point, we can now
	 * turn arrays into SQL queries.
	 * 
	 * We need to now be able to add fixtures to our newly
	 * created table. We now stub out as we know the implementation
	 * works as expected.
	 * 
	 */
	function testSetupFixtureTableReturnsTrue() {
		$dataType = $this->_testFixture->getFixtureTableFields();
		$result = $this->_stub->setupFixtureTable($dataType,'info');
		$this->assertTrue($result);
	}
	
	/**
	 * If table name is empty we need to throw an exception.
	 *
	 */
	function testSetupFixtureTableThrowsExceptionOnEmptyTableName() {
		$tableName = '';
		$dataType = $this->_testFixture->getFixtureTableFields();
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->setupFixtureTable($dataType,$tableName);
	}
	
	/**
	 * What happens if we try to build a table using an invalid query.
	 * convertDataTypeShouldThrowAnError.
	 * 
	 * Replaced with our stub seeing as our implemention is now executing
	 * our query
	 * 
	 */
	function testSetupFixtureTableShouldReturnFalseIfDataTypeInvalidLength() {
		$tableName = 'illegalTable';
		$dataType = $this->_getIllegalDataTypeTestTableStructure();
		$result = $this->_fixturesManager->setupFixtureTable($dataType,$tableName);
		$this->assertFalse($result);
	}
	
	/**
	 * Ok, now we will need a DB creation method to actually make our
	 * table for us. This method will return false on failure & true
	 * on success.
	 * 
	 */
	function testRunFixtureQueryReturnsFalseOnFailure() {
		$query = 'CREATE TABLE SFSFSDsdsd;';
		$this->setExpectedException('ErrorException');
		$this->_fixWrap->runFixtureQuery($query);
	}
	
	/**
	 * Here we need to make sure that anything without CREATE TABLE
	 * is dismissed and thrown as an exception
	 */
	function testRunFixtureQueryThrowsExceptionIfPassedANonCreateTableQuery() {
		$query = 'sfdsfa';
		$this->setExpectedException('ErrorException');
		$this->_fixWrap->runFixtureQuery($query);
	}
	
	/**
	 * 
	 *
	 */
	function testRunFixtureQueryThrowsExceptionIfPassedAnUnexecutableQuery() {
		$query = 'CREATE TABLE (id serial);';
		$this->setExpectedException('PDOException');
		$this->_fixWrap->runFixtureQuery($query);
	}
	
	/**
	 * Now we want to loop through our array and check if each
	 * fixture table exists. If it doesn't throw error.
	 * 
	 * @todo Realistically this functionality would be down to come kind of DB interface.
	 */
	function testDropFixtureTableFixturesTableThrowExceptionIfFixturesTableDoesNotExist() {
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->dropFixtureTable();
	}
	
	/**
	 * Is a little bit naughty but the implementation of
	 * executing a query was giving us an error, now we
	 * are catching our errors. Once sorted we will extend
	 * so we can ascertain whether we can actually delete
	 * our table, can come in handy at some point & makes
	 * extended use of our test.
	 *
	 */
	function testDeleteDBTableCanDeleteAnActualTable() {
		$query = $this->_getGenericQuery('blah');
		$result = $this->_fixWrap->runFixtureQuery($query);
		$this->assertTrue($result);
		$wasDeleted = $this->_fixturesManager->dropFixtureTable();
		$this->assertTrue($wasDeleted);
	}

    /**
     * What happens when we try to insert an invalid insert query?
     * cool we get an error from Zend_Db_Statement.
     * Fatal error: Call to undefined method PDOStatement::result_metadata() in /usr/share/php/Zend/Db/Statement/Mysqli.php on line 221
     * This was because we were using the wrong type of of command.
     * 
     */
	
	/**
	 * BuildInsertQuery will need a help method, which will loop through
	 *  the params turning it into a standard insert code.
	 *
	 */
	function testConvertInsertQueryThrowsExceptionIfParamNotAnArray() {
		$insertData = '';
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->_constructInsertQuery($insertData,'coffee');
	}
	
	/**
	 * What if our table name is not a string?
	 *
	 */
    function testConvertInsertQueryThrowsExceptionIfTableNameIsAnArray() {
    	$table = array();
        $insertData = $this->_testFixture->getTestData('id',1);
        $this->setExpectedException('ErrorException');
        $this->_fixturesManager->_constructInsertQuery($insertData,$table);
    }
    
	/**
	 * Makes sure that if nothing is wrong, we return a string with 'INSERT INTO'.
	 *
	 */
	function testConstructInsertQueryReturnsTrue() {
		$data = $this->_testFixture->getTestData('id',1);
		$result = $this->_fixturesManager->_constructInsertQuery($data,'snooker');
		$this->assertContains('INSERT INTO', $result);
	}
	
	/**
	 * Looping through the test data is pretty simple so we
	 * are just going to test that we are returned a string
	 * containing 'VALUES ('. From there we can determine the
	 * rest of the functionality and make a big step.
	 *
	 */
	function testConstructInsertQueryContainsEnclosingBrackets() {
		$data = $this->_testFixture->getTestData('id',1);
		$result = $this->_fixturesManager->_constructInsertQuery($data,'pool');
		$this->assertContains('VALUES (',$result);
	}

	
	/**
	 * We missed this whilst refactoring, we need to make sure that the 
	 * tablename is a string & not empty.
	 * 
	 */
	function testConstructInsertQueryThrowsExceptionIfTableNameIsNotAString() {
		$testData = $this->_testFixture->getTestData('id',1);
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->setupFixtureTable($testData,array());
	}
	
	/**
	 * The same goes for empty string
	 * 
	 */
	function testConstructInsertQueryThrowsExceptionIfTableNameIsEmpty() {
		$testData = $this->_testFixture->getTestData('id',1);
        $this->setExpectedException('ErrorException');
        $this->_fixturesManager->setupFixtureTable($testData,'');
	}
	
	/**
	 * We want to make sure that we throw exceptions if our test data
	 * is not in an array format. From here we know we need to reimplement
     * functionality, so we'll refactor.
	 *
	 */
	function testInsertTestDataThrowsErrorIfTestDataIsNotAnArray() {
		$testData = '';
		$table = 'tea';
		$this->setExpectedException('ErrorException');
		$this->_fixturesManager->insertTestData($testData,$table);
	}
	
	/**
	 * We also want to make sure that we throw an exception if our
	 * table name is empty. This is already covered, but just to be
	 * on the safe side.
	 *
	 */
	function testInsertTestDataThrowsErrorIfTableNameIsEmpty() {
        $testData = '';
        $table = '';
        $this->setExpectedException('ErrorException');
        $this->_fixturesManager->insertTestData($testData,$table);
    }
     
    /**
     * We need to create a accessor method that allows us to insert
     * test data into our table.
     * 
     */
    function testInsertTestDataIsAbleToInsertASingleEntry() {
    	$table = 'apples';
        $this->_setUpTestTableStructure($table);
        $testData = $this->_testFixture->getTestData();
        $result = $this->_fixturesManager->insertTestData($testData,$table);
        $this->assertTrue($result);
        $this->_fixturesManager->dropFixtureTable();
    }
    
    /**
     * Now we want to make sure that we can insert multiple
     * entries of test data. This will help us to put together
     * test structures with as little effort as possible.
     *
     */
    function testInsertTestDataIsAbleToInsertMultipleEntries() {
    	$table = 'pears';
        $this->_setUpTestTableStructure($table);
        $testData = $this->_testFixture->getTestData();
        $result = $this->_fixturesManager->insertTestData($testData,$table);
        $this->assertTrue($result);
        $this->_fixturesManager->dropFixtureTable();
    }
    
    /**
     * We should get an exception if our SQL turns out invalid
     * i.e. the table names do not correspond.
     * 
     */
    function testInsertTestDataThrowsExceptionWhenTableDoesNotExist() {
    	$this->setExpectedException('PDOException');
        $testData = $this->_testFixture->getTestData();
        $this->_fixturesManager->insertTestData($testData,'plum');
    	$this->_fixturesManager->dropFixtureTable();
    }
    
    /**
     * So we are pretty much covered now, all we really need to do
     * now is some refactoring and a little more verification.
     * 
     */
    
    /**
     * Need to add a method to determine whether a table fixture has
     * been entered or not.
     * 
     */
    function testFixtureTableExistsReturnsFalseIfNoTableExists() {
    	$result = $this->_fixturesManager->fixtureTableExists('apples');
    	$this->assertFalse($result);
    }
    
    /**
     * If we have no tables present within the database we need to throw
     * an exception.
     * 
     */
    function testFixtureTableExistsThrowsExceptionIfNotTablesNameIsEmpty() {
    	$this->setExpectedException('ErrorException');
    	$this->_fixturesManager->fixtureTableExists('');
    }
    
    /**
     * What happens if the table name is not a string?
     * 
     */
    function testFixtureTableExistsThrowsExceptionIfTableNameIsNotAString() {
    	$this->setExpectedException('ErrorException');
    	$this->_fixturesManager->fixtureTableExists(array());
    }
    
    /**
     * Must return true if the table is found, we'll need to actually
     * create a table for this table to have some value.
     * 
     */
    function testFixtureTableExistsReturnsTrueIfTableDoesExist() {
    	$table = 'apples';
        $this->_setUpTestTableStructure($table);
    	$result = $this->_fixturesManager->fixtureTableExists($table);
    	$this->_fixturesManager->dropFixtureTable();
    	$this->assertTrue($result);
    }
}