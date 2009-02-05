<?php

/**
 * DataTypeChecker
 *  
 * Helps to check our fixture data types, this can help us
 * not only input data into a DB but also emulate a DB response.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage DataTypeChecker
 *
 * $LastChangedBy: yomi $
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
Zend_Loader::registerAutoload();

class DataTypeChecker {
    /**
     * Checks that our datatype is an array and that our table
     * is a valid string, if this is not the case we need to throw
     * an exception.
     *
     * @access 	public
     * @param 	Array 	$fixture	Fixture we want to insert.
     * @param 	String 	$tableName	The name of the table we want to add the test data to.
     * 
     */
    static function checkTestDataAndTableName($fixture, $tableName) {
        if (!is_array($fixture)) {
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
     * @param  String  $key            The key we want to check.
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
     * Checks if our datatype is a length value.
     * 
     * Used to dynamically determine and setup the test datas length.
     *
     * @access	public
     * @param 	String 	$key	Key we want to check.
     * @param 	String 	$value	The value we want to check.
     * @return 	String	$data	Returns the length in a SQL format.
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
     * Determines whether our data type is allowed a null value.
     *
     * @access  public
     * @param 	String 	$key	Key we want to check.
     * @param 	String 	$value	The value we want to check.
     * @return 	String	$data	Returns the length in a SQL format.
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
     * @access 	public
     * @param 	String 	$key	Key we want to check.
     * @param 	String 	$value	The value we want to check.
     * @return 	String	$data	Returns the default value in a SQL format.
     * 
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
     * @access 	public
     * @param 	String 	$key	Key we want to check.
     * @return 	String	$data	Returns the primary key in a SQL format.
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
     * @access 	public
     * @param 	Array 	$fixture	The fixture we want to check.
     * 
     */
    static function checkDataType($fixture) {
    	if (!is_array($fixture)) {
    		throw new ErrorException('Data type must be an array.');
    	}
    	if (array_key_exists('type', $fixture)) {
	    	if ('date' === $fixture['type'] || 'datetime' === $fixture['type']) {         // throws exception, when type key not present
	        	if (array_key_exists('length', $fixture)) {
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
     * @access 	public
     * @param 	Array 	$fixture	The fixture we want to check.
     * 
     */
    static function checkFieldsType($fixture) {
       DataTypeChecker::checkDataType($fixture);
       if ( $fixture['type'] === 'integer' || $fixture['type'] ===  'string' ) {
            if (!array_key_exists('length', $fixture)) {
                throw new ErrorException('String & Integer must have a length specified.');
            }
       } elseif ('date' === $fixture['type'] || 'datetime' === $fixture['type']) {
       } else {
            throw new ErrorException('Invalid data type.');
       }
    }

    /**
     * Checks that our null propery is a boolean type.
     * 
     * @access 	public
     * @param 	Array 	$fixture	Fixture we want to check.
     * 
     */
    static function checkFieldsNullProperty($fixture) {
       if (array_key_exists('null', $fixture)) {
            if (!is_bool($fixture['null'])) {
                throw new ErrorException('Null must be set to a boolean value.');
            }
            if (array_key_exists('default', $fixture)) {
                throw new ErrorException('Can not use keyword default along with null.');
            }
       }
    }

    /**
	 * Helper method for finding our generate type
	 * & returning the correct string pool. 
	 *
	 * @access 	private
	 * @param 	String 	$type		The type of string we want to generate.
	 * @return 	String 	$pool		The string pool we want to use to generate our string.
	 * 
	 */
    static function getDataTypeGeneratePool($type) {
    	$pool = 'abcdefghijklmnopqrstuvwxyz';
		$upper = strtoupper($pool);
		$nums = '';
		for ($i=0; $i<=9; $i++) {
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
     * @access 	public
     * @param 	Array 	$fixture	The fixture we want to validate.
     * 
     */
    static function validateDataTypeFields($fixture) {
       self::checkFieldsType($fixture);
       self::checkFieldsNullProperty($fixture);
    }
}