<?php
/**
 * FixturesManager
 * Handles our fixtures during testing. This feature allows us
 * to create new tables via an array, create input & read/create
 * new fixtures.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
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
Zend_Loader::registerAutoload();


class FixturesManager {
	
    /**
     * Zend DB, used to connect to our DB
     * 
     * @access 	private
     * @var 	Zend_DB
     * 
     */
    private $_db;
    
    /**
     * Stores the list of allowed SQL commands
     * allowed to be executed.
     *
     * @access 	private
     * @var 	Array
     * 
     */
    private $_allowedSQLCmds = null;
    
    /**
     * Initialises our DB connection for us.
     * 
     * Takes an environment as the parameter which is used to 
     * load up the correct configuration information.
     *
     * @access 	public
     * @param 	String 	$env		The environment we want to set our FixturesManager up in
     * 
     */
	public function __construct($env=null) {
		if (null === $env) {
		  Zend_ConfigSettings::setUpConfig();
		} else {
			Zend_ConfigSettings::setUpConfigEnv($env);
		}
		Zend_ConfigSettings::setUpDBAdapter();
		$this->_db = Zend_Registry::get('db');
		$this->_allowedSQLCmds = array('CREATE','INSERT INTO');
	}
	
	/**
	 * Loops through our datatypes and generate our SQL as well
	 * go along.
	 *
	 * @access private
	 * @param  Array 	$fixture	The fixture we want to check.
	 * @return String	$stmt		The returned SQL statement.
	 * 
	 */
    private function _checkDataTypes($fixture) {
    	$stmt = '';
        foreach ($fixture as $property=>$dataType) {
            $stmt .= DataTypeChecker::checkDataTypeValues($property, $dataType);
            $stmt .= DataTypeChecker::checkDataTypeValuesLength($property, $dataType);
            $stmt .= DataTypeChecker::checkDataTypeValueNull($property, $dataType);
            $stmt .= DataTypeChecker::checkDataTypeDefault($property, $dataType);
            $stmt .= DataTypeChecker::checkDataTypePrimaryKey($property);
        }
        return $stmt;
    }
    
    /**
     * Parses through our test data constructing the nessary SQL
     * which is run, giving us our populated test DB.
     *
     * @access 	private
     * @param 	Array 	$fixture	The fixture we want to parse
     * @param 	String 	$table		The database we want to run the query on.
     * 
     */
    private function _parseTestData($fixture, $table) {
       DataTypeChecker::checkTestDataAndTableName($fixture, $table);
       foreach ($fixture as $testData) {
            $query = $this->_constructInsertQuery($testData, $table);
            $this->_runFixtureQuery($query);                
       }
    }
	
    /**
     * Truncates a single table
     *
     * @access 	private
     * @param 	String 		$name	The name of the table we want to truncate.
     * 
     */
    private function _truncate($name) {
    	$sql = 'TRUNCATE TABLE ' .$name;
       	$this->_db->getConnection()->exec($sql);
    }
    
    /**
     * Used to actually execute our dynamically
     * made SQL which creates an instance of our
     * DB for us.
     *
     * @access 	protected
     * @param 	String 		$query
     * @return 	bool
     * 
     */
    protected function _runFixtureQuery($query) {
    	$this->validateQuery($query);
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
     * @param   Array   	$insertDataType
     * @param   String  	$tableName
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
        $stmt .= eregi_replace(', $', ') ', $insert);
        $stmt .= eregi_replace(', $', ');', $values);

        return $stmt;
    }
    
    /**
     * Checks to see if any tables are present in our test db.
     *
     * @access 	public
     * @return 	bool
     * 
     */
    public function tablesPresent() {
        if ($this->_db->listTables()) {
            return true;
        }
        return false;
    }
	
    /**
     * Does the checking for our method call.
     *
     * @access  public
     * @param   String                		$call      The called method.
     * @param   PHPUnit_Fixture_DynamicDB   $fixture
     * @return  bool
     * 
     */
    public function fixtureMethodCheck($call,$fixture) {
    	if ($fixture instanceof PHPUnit_Fixture_DynamicDB) {
			switch ($call) {
				case 'generate':
					$result = $this->buildSchema($fixture);
					break;
          		case 'drop':
              		$result = $this->dropTables();
              		break;
				case 'setup':
				    $result = $this->setupTable($fixture->getFields(), $fixture->getName());
				    break;
				case 'truncate':
				    $result = $this->truncateTable($fixture->getName());
				    break;
			    case 'clean':
				    $result = $this->truncateTable();
				    break;
				case 'populate':
				    $result = $this->insertTestData($fixture->get(), $fixture->getName());
				    break;
				default:
    				throw new ErrorException('Invalid fixture method call.');
			}
    	} else {
    		throw new ErrorException('Fixture must extend PHPUnit_Fixture_DynamicDB.');
    	}
        return $result;
    }

    /**
     * Validates our query, checking it against our allowed SQL commands.
     *
     * @access 	public
     * @param 	String 		$query		The query we want to validate.
     * 
     */
    function validateQuery($query) {
    	$found = count($this->_allowedSQLCmds);
    	foreach ($this->_allowedSQLCmds as $cmd) {
	        if (!ereg($cmd, $query)) {
	            $found--;
	        }
	    	if (0 >= $found) {
	    		throw new ErrorException('Illegal format: ' .$found .'='.$query .' = ' .$cmd);
	    	}
    	}
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
            $data = $this->_checkDataTypes($dataType);
            $query .= $field .$data .', ';
	    }
        // remove the trailing ', ' and replace with ');'
        $stmt .= eregi_replace(', $', ');', $query);
        return $stmt;
     }
    
    /**
     * Runs each of the schemas stored by PHPUnit_Fixture_DynamicDB
     *
     * Primarily used to generate our staging DB structure, ready for
     * integration, functionality & acceptance testing.
     * 
     * @access public
     * @param  PHPUnit_Fixture_DynamicDB	$fixture
     * @return bool										True if sucessful, false otherwise.
     * 
     */
    public function buildSchema($fixture) {
    	if (!$fixture instanceof PHPUnit_Fixture_DynamicDB) {
    		throw new ErrorException('Fixture must extend PHPUnit_Fixture_DynamicDB.');
    	}
    	$schemas = $fixture->getSchemas();
		if (0 === count($schemas)) {
			throw new Zend_Exception('No schema found.');
		}
		try {
			foreach ($schemas as $sql) {
				echo $sql;
				$this->_runFixtureQuery($sql);
			}
			return true;
		}
		catch (Exception $e) {
			$e->getMessage();
			$fixture->drop();
		}
		return false;
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
	 * @param  String 	$tableName  The DB table name.
	 * @return Bool     	        True on success, false on failure.
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
	 * Truncates our fixtures table.
	 * 
	 * @access 	public
	 * @param  	String 	$name    Our fixture table name
	 * @return 	bool
	 * 
	 */
    public function truncateTable($name='') {
        if (!is_string($name)) {
            throw new ErrorException('Tablename must be a string.');
        }
        try {
	        if ('' === $name) {
		        $tables = $this->_db->listTables();
				foreach ($tables as $table) {
					$this->_truncate($table);
				}
	        } elseif ($this->tableExists($name)) {
	        	$this->_truncate($name);
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
	 * Deletes a specific table.
	 * 
	 * @access 	public
	 * @param  	$name    Name of table we want to drop.
	 * 
	 */
	public function dropTable($name) {
		$sql = 'DROP TABLE ' .$name;         // smells
        $this->_db->getConnection()->exec($sql);        
	}
    
	/**
	 * Deletes all our fixtures tables.
	 *
	 * @access 	public
	 * @return 	bool		True on success, false otherwise.
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
	 * Copies table data from development to test
	 * 
	 * Copies table data from a table in the development databse to one in the test database
	 * Connects to the dev database, fetches all records 
	 * then connects to the test db and does th inserts
	 * 
	 * @param	string	$table	The name of the table to copy
	 * @access 	public
	 */
	public function loadTable($table) {
		// get the development database
		Zend_ConfigSettings::setUpConfigEnv('development');
		Zend_ConfigSettings::setUpDBAdapter();		
		$devDb = Zend_Registry::get('db');
		
		// get the test database
		Zend_ConfigSettings::setUpConfigEnv('local');
		Zend_ConfigSettings::setUpDBAdapter();		
		$testDb = Zend_Registry::get('db');		
				
		$stmt = $devDb->query("SELECT * FROM {$table}");
		$tableData = $stmt->fetchAll();
		if ($tableData) {
			$testDb->query("TRUNCATE TABLE {$table}");
			foreach ($tableData as $tableRow) {
				$data = array();
				foreach ($tableRow as $key => $value) {
					$data[$key] = $value;
				}
				
				$testDb->insert($table, $data);
			}
		}
	}
}