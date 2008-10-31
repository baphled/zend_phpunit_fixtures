<?php
/**
 * FixturesManager
 * Handles our fixtures during testing. This feature allows us
 * to create new tables via an array, create input & read/create
 * new fixtures.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 * $LastChangedBy: yomi $
 * 
 * Date: 04/09/2008
 * A large amount of refactoring has been done here, namely to improve
 * performance and design, we introduced dropTable int our buildFixtureTable
 * function earlier, which didn't help things much, though out DB based tests
 * work, this leaves our other tests failing, so we have reintroduced the same
 * method at the tearDown method of our class.
 * 
 * Date: 02/09/2008
 * Refactored _validateDataType, we were getting errors when checking
 * that a data type had lengths on arrays with none. We are now able
 * to check that we actually do & suppress the error by using array_search.
 * Refactored checkDataType functions into DataTypeChecker, which will now
 * deal with all our datatype checking.
 * Improved validateDataType to throw an exception if a type is not defined,
 * this functionality seems more at home in DataTypeChecker, so we'll refactor
 * it into there shortly.
 * Removed _validateDataType and refactored into DataTypeChecker.
 * 
 * Date: 31/08/2008
 * Finished constructInsertQuery & refactored so that it can handle
 * multiple entries.
 * Refactored constructInsertQuery and move validation out
 * to validateTestDataAndTableName, which will throw an error
 * if the datatype is not in an array or the name is not a valid
 * string.
 * Introduced setupTable, which is an accessor method for
 * constructInsertQuery, basically iterating over the test data
 * inserting it into our fixtures table.
 * Refactored validateTestDataAndTableName and placed within
 * DataTypeChecker, also renamed to checkTestDataAndTableName.
 * Refactored insertTestData, now uses parseTestData, which parses
 * our test data and inserts each on into our selected test table.
 * 
 * Date: 28/08/2008
 * Refactored _makeDBTable to _runFixtureQuery, as the name is more
 * appropriate.
 * Improved execution of query, we can now catch error and exec
 * queries with no errors.
 * Implemented private function listTables, which is actually only
 * used by our unit test, we will remove once it has been put in SVN.
 * Added functionality to loop through our fixtures DB and remove each
 * table.
 * Removed listTable method.
 * 
 * Date: 27/08/2008
 * Added implementation to execute our dynamically built query.
 * We get an error when trying to exec our query, need to look into.
 * 
 * Date: 20/08/2008
 * Improved implementations, can now create SQL queries via
 * fixture arrays, will add an example of how to create these
 * arrays later.
 * Have tested up to the point of being able to throw a whole
 * array into convertDataType, which now, checks for length &
 * parses our array & creates our schema. Need to implement
 * fixture parsing and insertiong into the dynamically created
 * table.
 * Remove convertDataType tablename exception seeing as we are
 * using a default within the parameter.
 * 
 * Date: 20/08/08
 * Created basic implementation of Fixturesmanager, will need
 * to improve tests for creating schema out of our array but
 * have a decent idea of how things should be.
 * 
 * @todo Don't like the fact that the DB related functionality
 *       is mingled in here, it should really be refactored.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();


class FixturesManager {
	
    /**
     * Zend DB, used to connect to our DB
     * @access private
     * @var Zend_DB
     * 
    */
    private $_db;
    
    /**
     * Initialises our DB connection for us.
     *
     * @access public
     * @param String $env
     */
	public function __construct($env=null) {
		if(null === $env) {
		  TestConfigSettings::setUpConfig();
		}
		else {
			TestConfigSettings::setUpConfigEnv($env);
		}
		TestConfigSettings::setUpDBAdapter();
		$this->_db = Zend_Registry::get('db');
	}
	
	/**
	 * Loops through our datatypes and generate our SQL as well
	 * go along.
	 *
	 * @access private
	 * @param  Array $dataType
	 * @return String
	 * 
	 */
    private function _checkDataTypes($dataType) {
    	$data = '';
        foreach ($dataType as $key=>$value) {
            $data .= DataTypeChecker::checkDataTypeValues($key, $value);
            $data .= DataTypeChecker::checkDataTypeValuesLength($key, $value);
            $data .= DataTypeChecker::checkDataTypeValueNull($key, $value);
            $data .= DataTypeChecker::checkDataTypeDefault($key, $value);
            $data .= DataTypeChecker::checkDataTypePrimaryKey($key);
        }
        return $data;
    }
    
    /**
     * Parses through our test data constructing the nessary SQL
     * which is run, giving us our populated test DB.
     *
     * @access private
     * @param Array $testData
     * @param String $table
     * 
     */
    private function _parseTestData($testData, $table) {
       DataTypeChecker::checkTestDataAndTableName($testData, $table);
       foreach ($testData as $data) {
            $query = $this->_constructInsertQuery($data, $table);
            $this->_runFixtureQuery($query);                
       }
    }

    /**
     * Used to actually execute our dynamically
     * made SQL which creates an instance of our
     * DB for us.
     *
     * @access protected
     * @param String $query
     * @return bool
     * 
     */
    protected function _runFixtureQuery($query) {
        if (!eregi(' \(',$query)) {             // @todo smells need better verification
            throw new ErrorException('Illegal query.');
        }
        try {
            $this->_db->getConnection()->exec($query);
        }
        catch(Exception $e) {
            throw new PDOException($e->getMessage());
        }
        return true;
    }
    
    /**
     * Constructs insertion query.
     *
     * @access  private
     * @param   Array   $insertDataType
     * @param   String  $tableName
     * @return  String
     * 
     */    
    protected function _constructInsertQuery($insertTestData, $tableName) {
        DataTypeChecker::checkTestDataAndTableName($insertTestData, $tableName);
        $stmt = 'INSERT INTO ' .$tableName;
        $insert = '(';
        $values = 'VALUES ( ';
        foreach ($insertTestData as $key=>$value) {
        	$insert .= $key .', ';
        	if (is_string($value)) {
        		$value = $this->_db->quote($value);
        	}
        	$values .=  $value .', ';
        }
        $stmt .= eregi_replace(', $',') ', $insert);
        $stmt .= eregi_replace(', $',');', $values);

        return $stmt;
    }

	/**
	 * Converts a Datatype array into SQL.
	 * We only are only creating these one at a time
	 * so we need to make sure we only have 1 array.
	 *
	 * @access public
	 * @param  Array   $dataTypeInfo   Our data type array, which will be used to create our SQL.
	 * @param  String  $tablename      The name of our DB table.
	 * @return String  $stmt           Portion of SQL, which will be used to construct query.
	 * 
	 */
     public function convertDataType($dataTypeInfo, $tablename='default') {
		if (!is_array($dataTypeInfo)) {
			throw new ErrorException('DataType is invalid.');
		}
        $stmt = 'CREATE TABLE ' .$tablename .' (';
        $query = '';
	    foreach ($dataTypeInfo as $field=>$dataType) {
            DataTypeChecker::checkDataType($dataType);
            $data = '';
            $data = $this->_checkDataTypes($dataType);
            $query .= $field .$data .', ';
		}
        // remove the trailing ', ' and replace with ');'
        $stmt .= eregi_replace(', $', ');', $query);
        return $stmt;
	}
	
	/**
	 * Builds our fixtures DB table
	 *
	 * @access public
	 * @param  Array   $dataType       Our data type array, which will be used to create our SQL.
	 * @param  String  $tableName      The name of our DB table.
	 * @return Bool                    True on success, false on failure.
	 * 
	 */
	public function setupTable($dataType, $tableName) {
		$query = '';
		if (empty($tableName)) {
			throw new ErrorException('Table must have a name');
		}
		try {
		  $query = $this->convertDataType($dataType, $tableName);
		  $this->_runFixtureQuery($query);
		  return true;
		}
		catch(ErrorException $e) {
			echo $e->getMessage();
		}
		 return false;
	}
	
	/**
	 * Accessor method for inserting test data into a test DB table.
	 * Will parse through each of our test data inserting them into
	 * the selected test table.
	 *
	 * @access public
	 * @param  Array   $testData   Our data type array, which will be used to create our SQL.
	 * @param  String  $table      The name of the DB table.
	 * @return Bool                True on success, false on failure.
	 * 
	 */
	public function insertTestData($testData, $table) {
		try {
			$this->_parseTestData($testData, $table);
		}
		catch(PDOException $e) {
			throw new PDOException($e->getMessage());
		}
		catch(Exception $e) {
			throw new ErrorException($e->getMessage());
		}
		return true;
	}
	
	/**
	 * Determines whether the fixture 
	 * actually exists with our test db.
	 *
	 * @access public
	 * @param  String $tableName  The DB table name.
	 * @return Bool               True on success, false on failure.
	 * 
	 */
	public function tableExists($tableName) {
		if (empty($tableName)) {
			throw new ErrorException('Table name can not be empty');
		}
		if (in_array($tableName, $this->_db->listTables())) {
			return true;
		}
		return false;
	}
    
    /**
     * Checks to see if any tables are present in our test db.
     *
     * @access public
     * @return bool
     * 
     */
    public function tablesPresent() {
        if ($this->_db->listTables()) {
            return true;
        }
        return false;
    }

	/**
	 * Truncates our fixtures table.
	 * 
	 * @access public
	 * @param  String $name    Our fixture table name
	 * @return bool
	 * 
	 */
    public function truncateTable($name) {
        if (!is_string($name)) {
            throw new ErrorException('Tablename must be a string.');
        }
        try {
	        if ($this->tableExists($name)) {
	        	$sql = 'TRUNCATE TABLE ' .$name;
	        	$this->_db->getConnection()->exec($sql);
	        } else {
	        	throw new ErrorException($name .' does not exist.');
	        }
        }
        catch (ErrorException $e) {
        	echo $e->getMessage();
        	return false;
        }
        return true;
    }
	
	/**
	 * Deletes all our fixtures tables.
	 *
	 * @access public
	 * @return bool
	 * 
	 */
	public function dropTables() {
		$fixtures = $this->_db->listTables();
		if (count($fixtures) === 0) {
			throw new ErrorException('No fixture tables to drop.');
		}
		try {
			foreach ($fixtures as $fixture) {
                $this->dropTable($fixture);
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		return true;
	}
	
	/**
	 * Deletes a specific table.
	 * 
	 * @access public
	 * @param  $name    Tablename
	 * 
	 */
	public function dropTable($name) {
		$sql = 'DROP TABLE ' .$name;         // smells
        $this->_db->getConnection()->exec($sql);        
	}
	
    /**
     * Does the checking for our method call.
     *
     * @access  public
     * @param   String                $call      The called method.
     * @param   PHPUnit_Fixture_DB    $fixture
     * @return  bool
     * 
     */
    public function fixtureMethodCheck($call,$fixture) {
    	if ($fixture instanceof PHPUnit_Fixture_DB) {
	    	switch ($call) {
	            case 'drop':
	                $result = $this->dropTables();
	                break;
	            case 'setup':
	                $result = $this->setupTable($fixture->getFields(), $fixture->getName());
	                break;
	            case 'truncate':
	                $result = $this->truncateTable($fixture->getName());
	                break;
	            case 'populate':
	                $result = $this->insertTestData($fixture->getTestData(), $fixture->getName());
	                break;
	            default:
	                throw new ErrorException('Invalid fixture method call.');             
	    	}
    	} else {
    		throw new ErrorException('Fixture must extend PHPUnit_Fixture_DB.');
    	}
        return $result;
    }
}