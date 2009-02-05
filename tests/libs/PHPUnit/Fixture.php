<?php
/**
 * PHPUnit_Fixture
 * 
 * Abstract Fixture class, used to handle our actual fixtures,
 * allowing us to pull specific bits of data, auto-generate
 * new test data & create and insert pre-stated data.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 * $LastChangedBy: yomi $
 * 
 * Date: 08/10/2008
 * Made abstract so that we can no long use the class directly.
 * Also added removeTestData functionality to allow us to remove a
 * single piece of test data.
 * 
 * Date: 06/09/2008
 * Improved validateDataType functionality, there was a hole in the
 * implementation where, if the fixture had no previous data, it
 * would fail, now rectified.
 * Also implemented retrieveTestDataResults, a nice little function
 * that allows us to retrieve our test data along with populated id's.
 * 
 * Date: 02/09/2008
 * Implemented functionality to generate, parse and determine each
 * properties data type.
 * Refactored the build and drop method to use _runFixtureMethodCall,
 * which now deals with most of the work build & drop previously shared.
 * Also did a little organising, improving on documentation & placing
 * methods in a better order (private, protected, public). This order
 * may need to be discussed as big files like this can be cumbersome
 * to search through.
 * 
 * Date: 01/09/2008
 * Added functionality to allow us to add test data to our fixture, we are 
 * also able to validate this data, to determine whether we are adding
 * test data with the same data structure.
 * Added functionality that allows us to determine whether a piece of test
 * data has already be inputted into our fixture. Will come in handy, when
 * a fixture class has predefined test data values & we want to create additional
 * ones on the fly.
 * Added functionality to build actual db tables using our fixtures object,
 * have also implemented a wrapper function to allow us to delete our test
 * tables.
 * Implemented functionality to generateFixtureTestData & _parseTestData, both
 * of which are used to generate our test data and store it in our _testData
 * property.
 *  
 * Date: 31/08/2008
 * Created basic implementation from test case, which allows us to count
 * the amount of test data present, aswell as retrieve, specific test data
 * & a whole of list of test data which can be used for testing.
 * 
 */
abstract class PHPUnit_Fixture {
	
    /**
     * The fixtures table structure
     *
     * @access 	protected
     * @var 	Array
     * 
     */
    protected $_fields = array();
    
	/**
	 * The fixtures test data.
	 *
	 * @access 	protected
	 * @var 	Array
	 * 
	 */
	protected $_fixtures = null;
	
	/**
	 * Stores our test data results
	 *
	 * @access 	private
	 * @var 	Array
	 * 
	 */
	private $_result = null;
	
	/**
	 * Checks that if test data is setup, that
	 * it is in the expected format, also sets 
	 * the timezone ready for later.
	 * 
	 * @access 	public
	 * 
	 */
	public function __construct() {
		if (null !== $this->_fixtures) {
			foreach ($this->_fixtures as $fixture) {
				if (!is_array($fixture)) {
					throw new Zend_Exception('Fixture data in unexpected format, should be an array of arrays');
				}
			}
		}
		$tmz = Zend_ConfigSettings::setupTimeZone();
		date_default_timezone_set($tmz);
	}

	/**
     * Verify that our test data is of a valid
     * structure and adds it to our our _testData
     * property.
     *
     * @access 	private
     * @param 	Array 	$fixture	TestData we want to verify.
     * 
     */
    private function _verify($fixture) {
       try {
            $this->validate($fixture, $this);
            $this->_fixtures[] = $fixture;
       }
       catch(ErrorException $e) {
            throw new ErrorException($e->getMessage());
       }
    }
    
    /**
     * 
     * Parses a fixture field array, building test
     * data as it goes. Is the main work horse behind
     * generate fixture test data, which is used to generate
     * our return our completed test data. 
     *
     * @access 	private
     * @param 	String 	$field		The fixture field
     * @param 	Array 	$fixtures	Our fixtures.
     * 
     */
    private function _parseSchema($field, $fixtures) {
       foreach ($fixtures as $fixture) {
            DataTypeIs::anInt($fixture, $field, $this);
            DataTypeIs::aString($fixture, $field, $this);
            DataTypeIs::aDate($fixture, $field, $this);
            DataTypeIs::aDateTime($fixture, $field, $this);
       }
    }

    /**
     * Retrieves all our test data.
     * 
     * @access 	private
     * @param 	String  $property	The fixture property we are looking for.
     * @param 	String  $value		The value set in the property we are looking for		
     * @return 	Array	$results	Our resulting test data
     * 
     * @todo Should not really return false but instead
     *       throw an exception if no test data is found.
     * 
     */
    private function _retrieve($property, $value) {
    	$results = array();
    	if (0 !== $this->count()) {
            if (!empty($property) && !empty($value)) {
                foreach ($this->_fixtures as $fixture) {
                    if ($fixture[$property] === $value) {
                       return $this->_removeAlias($fixture);
                    }
                }
            } else {
            	foreach ($this->_fixtures as $fixture) {
	            		$results[] = $this->_removeAlias($fixture);
	            } 
                return $results;
            }
    	}
    	return false;
    }
    
    /**
     * Generates our fixture test data, we need this so we can
     * loop through our fields array, to ascertain the data type
     * of each piece of test data.
     *
     * @access  private
     * @param   Int     $numOfFixtures	Number of fixtures to generate.
     * @return  Array   $results		Our fixtures encapsulated in a array.
     * 
     */
    private function _generate($numOfFixtures) {
        if (0 === count($this->_fields)) {
            throw new ErrorException('Fields not defined, can not generate without it.');
        }
        if (!is_int($numOfFixtures)) {
            throw new ErrorException('Must supply number of test data using an integer.');
        }
        $results = array();
        $this->_result = array();
        for ($i=0;$i<$numOfFixtures;$i++) {
            foreach ($this->getFields() as $field=>$dataType) {
                DataTypeChecker::checkDataType($dataType);
                $this->_parseSchema($field, $dataType);
            }
            array_push($results, $this->_result);
        }
        return $results;
    }

    /**
     * Verify that field key and values are valid &
     * already set within the instance.
     * 
     * @access 	private
	 * @param  String  $key		The key to verify.
	 * @param  Mixed   $value	The value associated to the key, we want to verify.
     * 
     */
    private function _verifyKeyAndValue($key,$value) {
       if (!is_string($key)) {
            throw new ErrorException('Test data id must be a string.');
       }
       if (!empty($key) && empty($value)) {
       		throw new ErrorException('Must supply a value when submitting a key');
       }
    }
    
    /**
     * Determines whether a field actually exists
     * within a fixture or not.
     *
     * @access  private
     * @param   String  $field	The field we are looking for.
     * @return  bool    		True on success, false on failure.
     * 
     */
    private function _fieldExists($field) {
        try {
            if ($this->getField($field)) {
                return true;
            }
        }
        catch (Exception $e) {
            print $e->getMessage();
        }
        
        return false;
    }
	
	/**
	 * Gets the number we'll use to generate our test data.
	 *
	 * @access 	private
	 * @param 	Int 	$max	Max number.
	 * @param 	Int		$min	Minimum number.
	 * @return 	Int		$num	Actual number generated.
	 * 
	 */
	private function _getRandomNumber($max, $min) {
		if ( $min < $max) {
			$num = mt_rand($min, $max);			
		} elseif ($min > $max) {
			throw new Zend_Exception('Min cannot be greater than max.');
		} else {
			$num = $max;
		}
		return $num;
	}

    /**
     * Seeing as we don't want to actually store the
     * ALIAS key, we need to remove it from our fixture
     * before we actually use them.
     *
     * @access 	protected
     * @param 	Array 		$fixture	The Fixture we want to check for an alias.
     * @return 	Array		$fixture	Fixture without alias.
     */
    protected function _removeAlias($fixture) {
    	if (is_array($fixture)) {
    		if (array_key_exists('ALIAS', $fixture)) {
    			unset($fixture['ALIAS']);
    		}
	   	}
	   	return $fixture;
    }
    
	/**
	 * Validates that our test data is of the same structure
	 * as pre-existing data. We get the first data type from
	 * our test data & store it for comparison, if the validating
	 * datatype is not of the same structure we throw and exception.
	 *
	 * @access 	public
	 * @param 	Array 	$fixture	Fixture we want to validate
	 * @return 	bool				True if valid, false otherwise.
	 * 
	 */
	public function validate($fixture) {
        if (false === $fixture) {
            throw  new ErrorException('Invalid test data type.');
        }
        if (null === $this->_fixtures) {
			return true;
        } else {
			$existingTestData = $this->get('id', 1);
			if (false === $existingTestData) {
				return true;
			}
			foreach ($fixture as $key=>$value) {
				if (!array_key_exists($key, $existingTestData)) {
				    throw new ErrorException( $key .' using ' .$value.' is an invalid test data.');
				}
			}
        }
        return false;
	}
    
	/**
	 * Basic method, allowing us to determine
	 * the number of test data we have within
	 * the fixture.
	 *
	 * @access public
	 * @return Int		$result	Number of Fixtures we have stored.
	 * 
	 */
	public function count() {
		$result = 0;
		if (isset($this->_fixtures)) {
			$result = count($this->_fixtures);
		}
		return $result;
	}
	
	/**
	 * Sets our results.
	 *
	 * @access 	public
	 * @param 	String 	$field	The field we want to set the results to.
	 * @param 	Array 	$data	The data we want to set to our results.
	 * 
	 */
	public function setResult($field, $data) {
		$this->_result[$field] = $data;
	}
	
	/**
	 * Gets our test data for use, if parameters are not passed
	 * we will retrieve all test data stored in this object, otherwise
	 * we will return the specific test data in question.
	 *
	 * @access public
	 * @param  String  $key		The key to retrieve.
	 * @param  Mixed   $value	The value associated to the key, we want to retrieve.
	 * @return Array			The fixture we were looking for.
	 * 
	 */
	public function get($key='', $value='') {
		$this->_verifyKeyAndValue($key, $value);
		return $this->_retrieve($key, $value);
		
	}
	
	/**
	 * Removes a single piece of test data from our
	 * fixture.
	 *
	 * @access public
	 * @param  String  $key		The key to search for.
	 * @param  Mixed   $value	The value associated to the key, we want to remove.
	 * @return bool				True if able to remove, false otherwise.
	 * 
	 */
	public function remove($key='', $value='') {
		$this->_verifyKeyAndValue($key, $value);
		if ($this->_fieldExists($key)) {
			for ($i=0;$i<$this->count();$i++) { 
				if ($this->_fixtures[$i][$key] === $value) {
					unset($this->_fixtures[$i]);
					return true;
				}
			}
		} else {
			throw new ErrorException('Invalid field name.');
		}
		return false;
	}
    
    /**
     * Sets our test data to our fixture.
     *
     * @access  public
     * @param   Array   $fixture	The fixture we want to add.
     * @return  bool				True if able to add, false otherwise.
     * 
     */
    public function add($fixture) {
        if (!is_array($fixture)) {
            throw new ErrorException('Test data must be in an array format.');
        } 
        foreach ($fixture as $dataType) {
            if (is_array($dataType)) {
                $this->_verify($dataType);
            } else {
                $this->_fixtures[] = $fixture;
                break;
            }
        }
        return true;
    }
    
	/**
	 * Generates a random string.
	 * 
	 * @access 	public
	 * @param  	int		$length Number of characters (defaults to 8).
	 * @param 	Int		$max	The maximum number of chars to generate.
	 * @return	string	$str	Our generated string.
	 * 
	 */
	public function generate($type = '', $max = 8, $min = 8) {
		$str = '';
		$pool = DataTypeChecker::getDataTypeGeneratePool($type);
		$num = $this->_getRandomNumber($max, $min);
		for ($i=0; $i < $num; $i++) {
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		return $str;
	}
	
	/**
	 * Gets the fixture fields data in an array format.
	 *
	 * @access public
	 * @return Array
	 * 
	 */
	public function getFields() {
		if (0 === count($this->_fields)) {
			throw new ErrorException('No fixture fields present.');
		} else {
			return $this->_fields;
		}
	}
    
    /**
     * Gets a single data type field from our fixture.
     *
     * @access  public
     * @param   String  $field	Name of the field we want to get.
     * @return  Array			Field's data type.
     * 
     */
    public function getField($field) {
        if (!is_string($field)) {
            throw new ErrorException('Field name must be a string.');
        }
        if (!array_key_exists($field, $this->_fields)) {
            throw new ErrorException('Field id does not exist.');
        }
        return array($field=>$this->_fields[$field]);
    }
    
	/**
     * Sets PHPUnit_Fixture's field property.
     *
     * @access  public
     * @param   Array 	$fields	The Fields we want to set for our fixture.
     * @return  bool			True if set, false otherwise.
     * 
     */
    public function setFields(array $fields) {
        if (0 === count($fields)) {
            throw new ErrorException('Illegal field format.');
        }
        foreach ($fields as $name=>$data) {
                if (!is_string($name)) {
                    throw new ErrorException('Field name must be a string.');
                }
                if (!is_array($data)) {
                    throw new ErrorException('Data must be in an associative array.');
                }
                DataTypeChecker::validateDataTypeFields($data);
        }
        $this->_fields = $fields;
        return true;
    }
    
    /**
     * Determines whether our test data already exists
     *
     * @access  public
     * @param   Array 	$fixture	Fixture we are looking for.
     * @return  bool				True if it exists, false otherwise.
     * 
     */
    public function exists($fixture) {
        if ($this->count() > 0 ) {
            for ($i=0;$i<$this->count();$i++) {
            	$data = $this->_removeAlias($this->_fixtures[$i]);
                if ($data == $fixture[$i]) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Returns our results with a id auto incremented.
     *
     * @access public
     * @return Array	$fixtures	Our fixtures with their ID's populated.
     * 
     */
    public function retrieveResults() {
        $fixtures = $this->get();
        if (!array_key_exists('id', $fixtures[0])) {
        	throw new ErrorException('Id does not exists, must have to use this method.');
        }
        for ($i=0;$i<$this->count();$i++) {
            $fixtures[$i]['id'] = $i+1;
            $fixtures[$i] = $this->_removeAlias($fixtures[$i]);
        }
        return $fixtures;
    }
    
    /**
     * Automatically generates our test data.
     * 
     * Once it generates our data, it then passes it
     * to addTestData to append to the _testData
     * property.
     *
     * @access  public
     * @param   int     $numOfFixtures	Number of fixtures we want to generate.
     * @return  bool					True if successful, false otherwise.
     * 
     */
    public function autoGen($numOfFixtures=10) {
        try {
            $result = $this->_generate($numOfFixtures);
            if (0 === count($result)) {
                throw new ErrorException('Unable to generate test data.');
            }
            $this->add($result);
            return true;
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }
    
    /**
     * Finds a fixture via an alias, the fixture must
     * already have an alias key defined.
     *
     * @access 	public
     * @param 	String 	$name		The name of the fixtures alias.
     * @return 	Array	$result 	Fixture if found, otherwise false.
     * 
     */
    public function find($name) {
    	foreach ($this->_fixtures as $result) {
    		if (array_key_exists('ALIAS', $result)) {
    			if ($name === $result['ALIAS']) {
    				return $this->_removeAlias($result);
    			}
    		}
    	}
    	return false;
    }
    
    /**
     * Adds an alias to our Fixture, is useful for when we 
     * want to specify a user-friendly name for each of our
     * pieces of test data.
     *
     * @access public
     * @param Int 		$index	The index of the fixture we want to give an alias.
     * @param String 	$alias	The alias we want to assign
     * @return bool				True if successfully added, false otherwise.
     * 
     */
    public function addAlias($index, $alias) {
    	if (!array_key_exists('ALIAS', $this->_fixtures[$index])) {
    		$this->_fixtures[$index]['ALIAS'] = $alias;
    		return true;
    	}
    	return false;
    }
 
    /**
     * Modifies a Fixtures alias from one name to another.
     *
     * @param 	String	$oldAlias	The old alias
     * @param 	String	$newAlias	The new alias
     * @return 	bool				True if alias was modified.
     */
    function modAlias($oldAlias, $newAlias) {
    	$fixture = $this->find($oldAlias);
    	if (false !== $fixture) {
    		for ($index=0;$index<$this->count();$index++) {
    			if (array_key_exists('ALIAS', $this->_fixtures[$index])) {
    				if ($oldAlias === $this->_fixtures[$index]['ALIAS']) {
    					$this->_fixtures[$index]['ALIAS'] = $newAlias;
    					return true;
    				}
    			}
    		}
    	}
    	return false;
    }
}