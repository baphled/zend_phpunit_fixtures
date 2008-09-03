<?php

/**
 * DataTypeChecker
 *  
 * Helps to check our fixure DB table data types, this can help us
 * not only input data into a DB but also emulate an DB response.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @LastChangedBy $LastChangedBy$
 * @version $Id$
 * @copyright 2008
 * @package TestSuite
 *
 * Date: 02/09/2008
 * Built class on realisation that functionality for FixturesManager
 * did not need to be there as they are more data related.
 * Refactored _validateDataType into class & renamed to checkDataType.
 * Refactored _validateTestDataAndTableName into class & renamed to
 * checkTestDataAndTableName.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class DataTypeChecker {
	
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
    static function checkTestDataAndTableName($insertDataType,$tableName) {
        if(!is_array($insertDataType)) {
            throw new ErrorException('Test data must be in array format.');
        }
        if(!is_string($tableName) || empty($tableName)) {
            throw new ErrorException('Table name must be a string.');
        }
    }
    
    /**
     * Checks out data type and returns the correct data type.
     *
     * @access static
     * @param  String  $key            
     * @param  String  $value          the value of our type.
     * @return String  $typeSegment    Returns a the SQL equalient to our type.
     * 
     * @todo Check that if we have a type & it doesnt match, throw an exception.
     * @todo Needs looking at, way too many if clauses.
     * 
     */
    static function checkDataTypeValues($key,$value) {
        $typeSegment = '';
        if($key === 'type') {               // smells, need to refactor
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
     * Checks if our datatype is a length value
     *
     * @access static
     * @param String $key
     * @param String $value
     * @return String
     */
    static function checkDataTypeValuesLength($key,$value) {
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
     * @access  static
     * @param   String  $key
     * @param   String  $value
     * @return  String  $data
     * 
     */
    static function checkDataTypeValueNull($key,$value) {
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
     * Checks that our datatype has a default value,
     * if it does we need to set the appropriate SQL string.
     *
     * @access static
     * @param String $key
     * @param String $value
     * @return String
     */
    static function checkDataTypeDefault($key,$value) {
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
     * Makes sure that date & datetime properties do not come
     * with lengths
     *
     * @access static
     * @param Array $dataType
     * 
     */
    static function checkDataType($dataType) {
    	if(array_key_exists('type', $dataType)) {
	    	if($dataType['type'] === 'date' || $dataType['type'] === 'datetime') {         // throws notices, when type key not present 
	        	if(array_key_exists('length',$dataType)) {
	        		throw new ErrorException('Invalid data format.');
	        	}
	        }
    	}
        else {
            throw new ErrorException('Must supply a valid data type.');
        }
    }
    
    /**
     * Checks to see if we have a primary key set, if we do
     * we need to create the corresponding SQL.
     *
     * @access static
     * @param Array $key
     * @return bool
     * 
     */
    static function checkDataTypePrimaryKey($key) {
    	$data = '';
        if($key === 'key') {
            $data .= ' PRIMARY KEY AUTO_INCREMENT';    
        }
        return $data;
    }
}
