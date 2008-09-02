<?php

/**
 * DataTypeChecker
 *  
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package
 *
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class DataTypeChecker {
	
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
    static function checkDataTypeValues($key,$value) {
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
     * Checks if our datatype is a length value
     *
     * @access private
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
     * @access  private
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
     * @access private
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
     * Checks to see if we have a primary key set, if we do
     * we need to create the corresponding SQL.
     *
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
