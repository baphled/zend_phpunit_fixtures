<?php
/**
 * PHPUnit_Fixture
 * 
 * Abstract Fixture class, used to handle our actual fixtures,
 * allowing us to pull specific bits of data, auto-generate
 * new test data & create and insert pre-stated data.
 * 
 * @author Yomi (baphled) Akindayini 2008
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
     * @access protected
     * @var Array
     * 
     */
    protected $_fields = array();
    
	/**
	 * The fixtures test data.
	 *
	 * @access protected
	 * @var Array
	 * 
	 */
	protected $_testData = null;
	
	/**
	 * Stores our test data results
	 *
	 * @access private
	 * @var Array
	 * 
	 */
	private $_result = null;
	
	/**
	 * Sets the timezone ready for later.
	 * 
	 * @access public
	 * 
	 */
	public function __construct() {
		$tmz = TestConfigSettings::setupTimeZone();
		date_default_timezone_set($tmz);
	}

	/**
     * Verify that our test data is of a valid
     * structure and adds it to our our _testData
     * property.
     *
     * @access private
     * @param Array $testData
     * 
     */
    private function _verifyTestData($testData) {
       try {
            $this->validateTestData($testData, $this);
            $this->_testData[] = $testData;
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
     * @access private
     * @param String $field
     * @param Array $values
     * 
     */
    private function _parseSchema($field, $values) {
       foreach ($values as $value) {
            DataTypeIs::anInt($value, $field, $this);
            DataTypeIs::aString($value, $field, $this);
            DataTypeIs::aDate($value, $field, $this);
            DataTypeIs::aDateTime($value, $field, $this);
       }
    }

    /**
     * Retrieves all our test data.
     * 
     * @access private
     * @param String    $key
     * @param Array     $value
     * @return Array
     * 
     * @todo Should not really return false but instead
     *       throw an exception if no test data is found.
     * 
     */
    private function _retrieveTestData($key, $value) {
       if (0 !== $this->testDataCount()) {
            if (!empty($key) && !empty($value)) {
                foreach ($this->_testData as $data) {
                    if ($data[$key] === $value) {
                       return $data;
                    }
                }
            } else {
                return $this->_testData;                
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
     * @param   Int     $numOfTestData
     * @return  Array   $results
     * 
     */
    private function _generateTestData($numOfTestData) {
        if (0 === count($this->_fields)) {
            throw new ErrorException('Fields not defined, can not generate without it.');
        }
        if (!is_int($numOfTestData)) {
            throw new ErrorException('Must supply number of test data using an integer.');
        }
        $results = array();
        $this->_result = array();
        for ($i=0;$i<$numOfTestData;$i++) {
            foreach ($this->getFields() as $field=>$values) {
                DataTypeChecker::checkDataType($values);
                $this->_parseSchema($field, $values);
            }
            array_push($results, $this->_result);
        }
        return $results;
    }

    /**
     * Verify that field key and values are valid &
     * already set within the instance.
     * 
     * @access private
     * @param String $key
     * @param String $value
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
     * @param   String  $field
     * @return  bool    True on success, false on failure.
     * 
     */
    private function _dataTypeFieldExists($field) {
        try {
            if ($this->getSingleDataTypeField($field)) {
                return true;
            }
        }
        catch (Exception $e) {
            print $e->getMessage();
        }
        
        return false;
    }
    
	/**
	 * Validates that our test data is of the same structure
	 * as pre-existing data. We get the first data type from
	 * our test data & store it for comparison, if the validating
	 * datatype is not of the same structure we throw and exception.
	 *
	 * @access public
	 * @param Array $testData
	 * @return bool
	 * 
	 */
	public function validateTestData($testData) {
        if (false === $testData) {
            throw  new ErrorException('Invalid test data type.');
        }
        if (null === $this->_testData) {
			return true;
        } else {
			$existingTestData = $this->getTestData('id', 1);
			if (false === $existingTestData) {
				return true;
			}
			foreach ($testData as $key=>$value) {
				if (!array_key_exists($key, $existingTestData)) {
				    throw new ErrorException( $key .' using ' .$value.' is an invalid test data.');
				}
			}
		}
		return false;
	}
	
	/**
	 * Sets our results.
	 *
	 * @access public
	 * @param String $field
	 * @param Array $data
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
	 * @param  String $key
	 * @param  String $value
	 * @return Array
	 * 
	 */
	public function getTestData($key='', $value='') {
		$this->_verifyKeyAndValue($key, $value);
		return $this->_retrieveTestData($key, $value);
		
	}
	
	/**
	 * Removes a single piece of test data from our
	 * fixture.
	 *
	 * @access public
	 * @param  String  $key
	 * @param  Mixed   $value
	 * @return bool
	 * 
	 */
	public function removeTestData($key='', $value='') {
		$this->_verifyKeyAndValue($key, $value);
		if ($this->_dataTypeFieldExists($key)) {
			for ($i=0;$i<$this->testDataCount();$i++) { 
				if ($this->_testData[$i][$key] === $value) {
					unset($this->_testData[$i]);
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
     * @param   Array   $testData
     * @return  bool
     * 
     */
    public function addTestData($testData) {
        if (!is_array($testData)) {
            throw new ErrorException('Test data must be in an array format.');
        } 
        foreach ($testData as $data) {
            if (is_array($data)) {
                $this->_verifyTestData($data);
            } else {
                $this->_testData = $testData;
                break;
            }
        }
        return true;
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
     * @param   String  $field
     * @return  Array
     * 
     */
    function getSingleDataTypeField($field) {
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
     * @param   Array $fields
     * @return  bool
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
	 * Basic method, allowing us to determine
	 * the number of test data we have within
	 * the fixture.
	 *
	 * @access public
	 * @return Int
	 * 
	 */
	public function testDataCount() {
		$result = 0;
		if (isset($this->_testData)) {
			$result = count($this->_testData);
		}
		return $result;
	}
    
    /**
     * Determines whether our test data already exists
     *
     * @access  public
     * @param   Array $testData
     * @return  bool
     * 
     */
    public function testDataExists($testData) {
        if ($this->testDataCount() > 0 ) {
            for ($i=0;$i<$this->testDataCount();$i++) {
                if ($this->_testData[$i] == $testData[$i]) {
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
     * @return Array
     * 
     */
    public function retrieveTestDataResults() {
        $testData = $this->getTestData();
        if (!array_key_exists('id', $testData[0])) {
        	throw new ErrorException('Id does not exists, must have to use this method.');
        }
        for ($i=0;$i<$this->testDataCount();$i++) {
            $testData[$i]['id'] = $i+1;
        }
        return $testData;
    }
    
    /**
     * Automatically generates our test data.
     * 
     * Once it generates our data, it then passes it
     * to addTestData to append to the _testData
     * property.
     *
     * @access  public
     * @param   int     $numOfTestData
     * @return  bool
     * 
     */
    public function autoGenerateTestData($numOfTestData=10) {
        try {
            $result = $this->_generateTestData($numOfTestData);
            if (0 === count($result)) {
                throw new ErrorException('Unable to generate test data.');
            }
            $this->addTestData($result);
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
     * @param 	String 	$name	The name of the fixtures alias.
     * @return 	Array	$result
     */
    function find($name) {
    	foreach ($this->_testData as $data) {
    		if(array_key_exists('ALIAS',$data)) {
    			if($name === $data['ALIAS']) {
    				 unset($result['ALIAS']);
    				return $result;
    			}
    		}
    	}
    	return false;
    }
}