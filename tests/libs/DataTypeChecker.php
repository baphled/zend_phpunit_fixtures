<?php

/**
 * DataTypeChecker
 *  
 * Helps to check our fixture data types, this can help us
 * not only input data into a DB but also emulate a DB response.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage DataTypeChecker
 *
 * $LastChangedBy: yomi $
 * Date: 02/09/2008
 * Built class on realisation that functionality for FixturesManager
 * did not need to be there as they are more data related.
 * Refactored _validateDataType into class & renamed to checkDataType.
 * Refactored _validateTestDataAndTableName into class & renamed to
 * checkTestDataAndTableName.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

class DataTypeChecker {
    /**
     * Checks that our datatype is an array and that our table
     * is a valid string, if this is not the case we need to throw
     * an exception.
     *
     * @access public
     * @param Array $insertDataType
     * @param String $tableName
     * 
     */
    static function checkTestDataAndTableName($insertDataType, $tableName) {
        if (!is_array($insertDataType)) {
            throw new ErrorException('Test data must be in array format.');
        }
        if (!is_string($tableName) || empty($tableName)) {
            throw new ErrorException('Table name must be a string.');
        }
    }

    /**
     * Checks out data type and returns the correct data type.
     *
     * @access public
     * @param  String  $key            
     * @param  String  $value          the value of our type.
     * @return String  $typeSegment    Returns a the SQL equalient to our type.
     * 
     */
    static function checkDataTypeValues($key, $value) {
        $typeSegment = '';
        if ('type' === $key) {
        	switch ($value) {
        		case 'string':
        			$typeSegment = ' VARCHAR';
        			break;
        		case 'integer':
        			$typeSegment = ' INT';
        			break;
        		case 'date':
        			$typeSegment = ' DATE';
        			break;
        		case 'datetime':
        			$typeSegment = ' DATETIME';
        			break;
        	}
        }
        return $typeSegment;
    }

    /**
     * Checks if our datatype is a length value
     *
     * @access public
     * @param String $key
     * @param String $value
     * @return String
     * 
     */
    static function checkDataTypeValuesLength($key, $value) {
        $data = '';
        if ('length' === $key) {
            $data .= '(' .$value .')';
        }
        return $data;
    }

    /**
     * Determines whether our data type 
     * is allowed a null value.
     *
     * @access  public
     * @param   String  $key
     * @param   String  $value
     * @return  String  $data
     * 
     */
    static function checkDataTypeValueNull($key,$value) {
        $data = '';
        if ('null' === $key) {
            if (TRUE === $value) {
                $data .= ' NULL';
            } elseif (FALSE === $value) {
                $data .= ' NOT NULL';
            }
        }
        return $data;
    }

    /**
     * Checks that our datatype has a default value,
     * if it does we need to set the appropriate SQL string.
     *
     * @access public
     * @param String $key
     * @param String $value
     * @return String
     */
    static function checkDataTypeDefault($key, $value) {
        $data = null;
        if ('default' === $key) {
            $data = ' DEFAULT ';
            if ('' === $value) {
               $data .= '""'; 
            } else {
               $data .= '"' .$value .'"';
            }
        }
        return $data;
    }

    /**
     * Checks to see if we have a primary key set, if we do
     * we need to create the corresponding SQL.
     *
     * @access public
     * @param Array $key
     * @return bool
     * 
     */
    static function checkDataTypePrimaryKey($key) {
        $data = '';
        if ('key' === $key) {
            $data .= ' PRIMARY KEY AUTO_INCREMENT';    
        }
        return $data;
    }

    /**
     * Makes sure that date & datetime properties do not come
     * with lengths
     *
     * @access public
     * @param Array $dataType
     * 
     */
    static function checkDataType($dataType) {
    	if (!is_array($dataType)) {
    		throw new ErrorException('Data type must be an array.');
    	}
    	if (array_key_exists('type', $dataType)) {
	    	if ('date' === $dataType['type'] || 'datetime' === $dataType['type']) {         // throws exception, when type key not present
	        	if (array_key_exists('length', $dataType)) {
	        		throw new ErrorException('Invalid data format.');
	        	}
	    	}
    	} else {
            throw new ErrorException('Must supply a valid data type.');
        }
    }

    /**
     * Checks our field types for us, strings & integer must have a length.
     * 
     * @access public
     * @param Array $dataType 
     * 
     */
    static function checkFieldsType($dataType) {
       DataTypeChecker::checkDataType($dataType);
       if ( $dataType['type'] === 'integer' || $dataType['type'] ===  'string' ) {
            if (!array_key_exists('length', $dataType)) {
                throw new ErrorException('String & Integer must have a length specified.');
            }
       } elseif ('date' === $dataType['type'] || 'datetime' === $dataType['type']) {
       } else {
            throw new ErrorException('Invalid data type.');
       }
    }

    /**
     * Checks that our null propery is a boolean type.
     * 
     * @access public
     * @param Array $dataType
     * 
     */
    static function checkFieldsNullProperty($dataType) {
       if (array_key_exists('null', $dataType)) {
            if (!is_bool($dataType['null'])) {
                throw new ErrorException('Null must be set to a boolean value.');
            }
            if (array_key_exists('default', $dataType)) {
                throw new ErrorException('Can not use keyword default along with null.');
            }
       }
    }

    /**
	 * Helper method for finding our generate type
	 * & returning the correct string pool. 
	 *
	 * @access 	private
	 * @param 	String 	$type
	 * @return 	String 	$pool		The string pool we want to use to generate our string.
	 * 
	 */
    static function getDataTypeGeneratePool($type) {
    	$pool = 'abcdefghijklmnopqrstuvwxyz';
		$upper = strtoupper($pool);
		$nums = '';
		for($i=0;$i<=9;$i++) {
			$nums .= $i;
		}
		switch($type) {
			case 'ALPH':
				$pool .= $upper;
				break;
			case 'ALPHUP':
				$pool = $upper;
				break;
			case 'NUM':
				$pool = $nums;
				break;
			case 'ALPHNUM':
				$pool .= $upper .= $nums;
				break;
			default:
				break;
		}
		return $pool;
    }
    
    /**
     * Used to make sure that our data type fields are all valid.
     * 
     * @access public
     * @param Array $dataType
     * 
     */
    static function validateDataTypeFields($dataType) {
       self::checkFieldsType($dataType);
       self::checkFieldsNullProperty($dataType);
    }
}