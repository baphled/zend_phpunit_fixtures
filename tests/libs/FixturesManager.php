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
 * @package TestSuite
 * @subpackage FixturesManager
 *
 * Date: 31/08/2008
 * Finished constructInsertQuery & refactored so that it can handle
 * multiple entries.
 * Refactored constructInsertQuery and move validation out
 * to validateTestDataAndTableName, which will throw an error
 * if the datatype is not in an array or the name is not a valid
 * string.
 * Introduced buildFixtureTable, which is an accessor method for
 * constructInsertQuery, basically iterating over the test data
 * inserting it into our fixtures table.
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
 * @todo Look into creating fixtures on the fly.
 * @todo Refactor class so that '_' prefixed functions are actually
 *       private.
 * @todo Refactor convertDataTypes, is way too big.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FixturesManager {

    /**
     * Zend DB, used to connect to our DB
     * @access private
    */
    private $_db;
    
    /**
     * Initialises our DB connection for us.
     *
     * @access public
     * @param String $env
     */
	public function __construct($env='development') {
		TestConfigSettings::setUpConfig($env);
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
        foreach ($dataType as $key=>$value) {
            $data .= $this->_checkDataTypeValues($key,$value);
            $data .= $this->_checkDataTypeValuesLength($key,$value);
            $data .= $this->_checkDataTypeValueNull($key,$value);
            $data .= $this->_checkDataTypeDefault($key,$value);
            if($key === 'key') {
                $data .= ' PRIMARY KEY AUTO_INCREMENT';
            }
        }
        return $data;
    }
	
	/**
	 * Checks if our datatype is a length value
	 *
	 * @access private
	 * @param String $key
	 * @param String $value
	 * @return String
	 */
    private function _checkDataTypeValuesLength($key,$value) {
        $data = '';
        if($key === 'length') {
            $data .= '(' .$value .')';
        }
        return $data;
    }

    /**
     * Determines whether our data type have 
     * a is allowed a null value.
     *
     * @access  private
     * @param   String  $key
     * @param   String  $value
     * @return  String  $data
     * 
     */
    private function _checkDataTypeValueNull($key,$value) {
    	$data = '';
        if($key === 'null') {
            if(TRUE === $value) {
                $data .= ' NULL';
            }
            elseif(FALSE === $value) {
                $data .= ' NOT NULL';
            }
        }
        return $data;
    }
    
	/**
	 * Checks out data type and returns the correct data type.
	 *
	 * @access private
	 * @param  String  $key            
	 * @param  String  $value          the value of our type.
	 * @return String  $typeSegment    Returns a the SQL equalient to our type.
	 * 
	 * @todo Check that if we have a type & it doesnt match, throw an exception.
	 * @todo Needs looking at, way too many if clauses.
	 * 
	 */
    private function _checkDataTypeValues($key,$value) {
        $typeSegment = '';
        if($key === 'type') {
            if($value === 'string') {
                $typeSegment = ' VARCHAR';
            }
            if($value === 'integer') {
                $typeSegment = ' INT';
            }
            if($value === 'date') {
            	$typeSegment = ' DATE';
            }
            if($value === 'datetime') {
            	$typeSegment = ' DATETIME';
            }
        }
        return $typeSegment;
    }
	
    /**
     * Checks that our datatype has a default value,
     * if it does we need to set the appropriate SQL string.
     *
     * @access private
     * @param String $key
     * @param String $value
     * @return String
     */
    private function _checkDataTypeDefault($key,$value) {
        $data = null;
        if($key === 'default') {
            $data = ' DEFAULT ';
            if($value === '') {
               $data .= '""'; 
            }
            else {
               $data .= '"' .$value .'"';
            }
        }
        return $data;
    }


    /**
     * Checks that is we have a certain type, we must
     * also have a length, if we don't we throw an
     * exception
     *
     * @access private
     * @param Array $dataType
     * 
     */
    private function _validateDataType($dataType) {
        if(!isset($dataType['length']) 
            && $dataType['type'] !== 'date'
            && $dataType['type'] !== 'datetime') {
            throw new ErrorException('Invalid data type.');
        }
    }
    
    /**
     * Checks that our datatype is an array and that our table
     * is a valid string, if this is not the case we need to throw
     * an exception 
     *
     * @access private
     * @param Array $insertDataType
     * @param String $tableName
     * 
     */
    private function _validateTestDataAndTableName($insertDataType,$tableName) {
     if(!is_array($insertDataType)) {
            throw new ErrorException('Test data must be in array format.');
        }
        if(!is_string($tableName) || empty($tableName)) {
            throw new ErrorException('Table name must be a string.');
        }
    }
    
    /**
     * Constructs insertion query.
     *
     * @access  private
     * @param   Array $insertDataType
     * @param   String  $tableName
     * @return  String
     * 
     */    
    function _constructInsertQuery($insertTestData,$tableName) {
        $this->_validateTestDataAndTableName($insertTestData,$tableName);
        $stmt = 'INSERT INTO ' .$tableName;
        $insert = '(';
        $values = 'VALUES ( ';
        foreach($insertTestData as $key=>$value) {
        	$insert .= $key .', ';
        	if(is_string($value)) {
        		$value = '"' .$value .'"';
        	}
        	$values .=  $value .', ';
        }
        $stmt .= eregi_replace(', $',') ',$insert);
        $stmt .= eregi_replace(', $',');',$values);

        return $stmt;
    }

    /**
     * Used to actually execute our dynamically
     * made SQL which creates an instance of our
     * DB for us.
     *
     * @access private
     * @param String $query
     * @return bool
     * 
     */
    function _runFixtureQuery($query) {
    	if(!eregi(' \(',$query)) {             // @todo smells need better verification
    		throw new ErrorException('Illegal query.');
    	}
    	try {
    		$this->_db->getConnection()->exec($query);
    		return true;
    	}
    	catch(Exception $e) {
    		throw new PDOException($e->getMessage());
    	}
    	return false;
    }

	/**
	 * Converts a Datatype array into SQL.
	 * We only are only creating these one at a time
	 * so we need to make sure we only have 1 array.
	 *
	 * @access public
	 * @param Array    $dataType
	 * @return String Portion of SQL, which will be used to construct query.
	 * 
	 * @todo Function is way to long need to refactor
	 * 
	 */
     public function convertDataType($dataTypeInfo,$tablename='default') {
		if(!is_array($dataTypeInfo)) {
			throw new ErrorException('DataType is invalid.');
		}
        else {
          $stmt = 'CREATE TABLE ' .$tablename .' (';
        }
        $query = '';
	   foreach($dataTypeInfo as $field=>$dataType) {
            $this->_validateDataType($dataType);
            $data = '';
            $data = $this->_checkDataTypes($dataType);
            $query .= $field .$data .', ';
        }
        // remove the trailing ', ' and replace with ');'
        $stmt .= eregi_replace(', $',');',$query);
        return $stmt;
	}
	
	/**
	 * Builds our fixtures DB table
	 *
	 * @access public
	 * @param Array $dataType
	 * @param String $tableName
	 * @return Bool
	 */
	public function buildFixtureTable($dataType,$tableName) {
		$query = '';
		if(empty($tableName)) {
			throw new ErrorException('Table must have a name');
		}
		try {
		  $query = $this->convertDataType($dataType,$tableName);
		  $this->_runFixtureQuery($query);
		}
		catch(ErrorException $e) {
			echo $e->getMessage();
			return false;
		}
		 return true;
	}
	
	/**
	 * Insert test data into a fixtures table, ready for testing.
	 *
	 * @param Array $testData
	 * @param String $table
	 * @return Bool
	 */
	function insertTestData($testData,$table) {
		$this->_validateTestDataAndTableName($testData,$table);
		try {
			foreach($testData as $data) {
                $query = $this->_constructInsertQuery($data,$table);
                $this->_runFixtureQuery($query);				
			}
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
	 * Deletes all our fixtures tables.
	 *
	 * @access public
	 * @return bool
	 */
	function deleteFixturesTable() {
		$fixtures = $this->_db->listTables();
		if(count($fixtures) === 0) {
			throw new ErrorException('No fixture tables to drop.');
		}
		try {
			foreach ($fixtures as $fixture) {
                $sql = 'DROP TABLE ' .$fixture;         // smells
                $this->_db->getConnection()->exec($sql);		
			}
			return true;
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		return false;
	}
}