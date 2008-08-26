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
 * @package
 *
 * Date: 20/08/08
 * Improved implementations, can now create SQL queries via
 * fixture arrays, will add an example of how to create these
 * arrays later.
 * Have tested up to the point of being able to throw a whole
 * array into convertDataType, which now, checks for length &
 * parses our array & creates our schema. Need to implement
 * fixture parsing and insertiong into the dynamically created
 * table.
 *  
 * Date: 20/08/08
 * Created basic implementation of Fixturesmanager, will need
 * to improve tests for creating schema out of our array but
 * have a decent idea of how things should be.
 * 
 * @todo Look into creating fixtures on the fly.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FixturesManager {

    /**
     * Zend DB, used to connect to our DB
    */
    private $_db;
    
	public function __construct($env='development') {
		TestConfigSettings::setUpConfig($env);
		TestConfigSettings::setUpDBAdapter();
		$this->_db = Zend_Registry::get('db');
	}
    
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
    
	
    private function _checkDataTypeDefault($key,$value) {
        $data = '';
        if($key === 'default') {
            $data = ' DEFAULT ';
            if($value === '') {
               $data .= '""'; 
            }
            else {
               $data .= '"' .$value .'"';
            }
            return $data;
        }
    }
    
    function _buildInsertQuery($insertData) {
    	return 'INSERT INTO';
    }
    
    function _makeDBTable($query) {
    	if(!eregi(' \(',$query)) {
    		throw new ErrorException('Illegal query.');
    	}
    	else {
    		try {
    		echo $query;
	    		$stmt = new Zend_Db_Statement_Mysqli($this->_db,$query);
	    		$stmt->execute();
	    		return true;
    		}
    		catch(PDOException $e) {
    			throw new PDOException($e->getMessage());
    		}
    	}
    	return false;
    }
	/**
	 * Converts a Datatype array into SQL.
	 * We only are only creating these one at a time
	 * so we need to make sure we only have 1 array.
	 *
	 * @param Array    $dataType
	 * @return String Portion of SQL, which will be used to construct query.
	 * 
	 * @todo Function is way to long need to refactor
	 * 
	 */
     public function convertDataType($dataTypeInfo,$tablename='default') {
     	if(NULL === $tablename) {
     		throw new ErrorException('Needs a tablename to create a table.');
     	}
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
		}
		catch(ErrorException $e) {
			return false;
		}
		 return true;
	}
}