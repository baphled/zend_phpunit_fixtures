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
 * Date: 28/08/2008
 * Refactored _makeDBTable to _runFixtureQuery, as the name is more
 * appropriate.
 * Improved execution of query, we can now catch error and exec
 * queries with no errors.
 * Implemented private function listTables, which is actually only
 * used by our unit test, we will remove once it has been put in SVN.
 * Added functionality to loop through our fixtures DB and remove each
 * table.
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
 *       false.
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
     * Just a simple wrapper to gather an array of current
     * tables within our DB.
     *
     * @access private
     * @return Array
     */
    function _listFixturesTables() {
    	return $this->_db->listTables();
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
	 * @todo Check the we only have a type, if we dont throw an exception.
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
    
    /*
     * @access private
     * @todo Finish implementation
     */
    function _buildInsertQuery($insertData) {
    	return 'INSERT INTO';
    }
    
    /**
     * Used to actually execute our dynamically
     * made SQL which creates an instance of our
     * db for us.
     *
     * @access private
     * @param String $query
     * @return bool
     * 
     * @todo Because Zend feel exceptions are pointless
     *       in sections, Statement.php doesn't throw error
     *       when we pass it an illegally form query, wudda???
     * 
     */
    function _runFixtureQuery($query) {
    	if(!eregi(' \(',$query)) {
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
     * Checks to see if a fixture table actually exists.
     *
     * @access private
     * @param String $tableName
     * @return Bool
     * 
     */
    function _tableExists($tableName) {
    	if(null === $tableName || $tableName === '') {
    		throw new ErrorException('Table name must be a string');
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
            if(!isset($dataType['length']) 
                && $dataType['type'] !== 'date'
                && $dataType['type'] !== 'datetime'
            ) {
            	throw new ErrorException('Datatype must have a length');
            }
            $data = '';
            foreach ($dataType as $key=>$value) {
            	$data .= $this->_checkDataTypeValues($key,$value);
            	$data .= $this->_checkDataTypeValuesLength($key,$value);
            	$data .= $this->_checkDataTypeValueNull($key,$value);
            	$data .= $this->_checkDataTypeDefault($key,$value);
                if($key === 'key') {
                    $data .= ' PRIMARY KEY AUTO_INCREMENT';
                }
            }
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
                $sql = 'DROP TABLE ' .$fixture;
                $this->_db->getConnection()->exec($sql);		
			}
			return true;
		}
		catch(Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
}