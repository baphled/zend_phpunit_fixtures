<?php
/**
 * PHPUnit_Fixture
 * 
 * Parent Fixture class, used to handle our actual fixtures,
 * allowing us to pull specific bits of data, auto-generate
 * new test data & create and insert pre-stated data.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package TestSuite
 *
 * $LastChangedBy$
 * Date: 06/09/2008
 * Improved validateDataType functionality, there as a hole in the
 * implementation where, if the fixture had no previous data, it
 * would fail, now rectified.
 * Also implemented retrieveTestDataResults, a nice little function
 * that allows us to retrieve our test data along with populated ids.
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
 * @todo Turn all property with a '_' prefix into private properties.
 * @todo Implement functionality to allows users to specify an already
 *       setup table.
 * 
 */
class PHPUnit_Fixture {
	
	/**
	 * Stores the fixtures table name
	 *
	 * @var String
	 */
	protected $_table = null;
	
    /**
     * Stores the fixtures table structure
     *
     * @var Array
     */
    protected $_fields = array();
    
	/**
	 * Stores the fixtures test data.
	 *
	 * @var Array
	 */
	protected $_testData = null;
	
	/**
	 * Stores our fixture manager, used
	 * to handle the meat of fixture interactions
	 *
	 * @access private
	 * @var FixtureManager
	 */
	private $_fixMan;
	
	/**
	 * Stores our test data results
	 *
	 * @access private
	 * @var Array
	 */
	private $_result = null;
	
	/**
	 * Sets an instance of FixturesManager &
	 * setup the timezone ready for later.
	 * 
	 * @todo Really should put the TZ in a config file.
	 *
	 */
	public function __construct() {
		$this->_fixMan = new FixturesManager();
		date_default_timezone_set('Europe/London');
	}

	/**
     * Verify that our test data is of a valid
     * structure and submit it to our our _testData
     * property.
     *
     * @access private
     * @param Array $testData
     */
    private function _verifyTestData($testData) {
       try {
            $this->validateTestData($testData);
            $this->_testData[] = $testData;
       }
       catch(ErrorException $e) {
            throw new ErrorException($e->getMessage());
       }
    }
	
    /**
     * Used to make sure that our data type fields are all valid.
     * 
     * @access private
     * @param $dataType
     * 
     */
    private function _validateDataTypeFields($dataType) {
       DataTypeChecker::checkFieldsType($dataType);
       DataTypeChecker::checkFieldsNullProperty($dataType);
    }
    
    /**
     * Checks that our data type is an integer
     *
     * @access private
     * @param Array $dataType
     * @param int $field
     */
    private function _dataTypeIsAnInt($dataType,$field) {
       if('integer' === $dataType) {
            if($field !== 'id') {
                $this->_result[$field] = rand();
            }
            else {
                $this->_result[$field] = NULL;
            }
        }
    }

    /**
     * Checks that our a string, if so we generate test data.
     *
     * @access private
     * @param Array $dataType
     * @param int $field
     */
    private function _dataTypeIsAString($dataType,$field) {
       if('string' === $dataType) {
           $this->_result[$field] = 'my string';
       }
    }
    
    /**
     * Checks to see if our data type is a date, if it is,
     * we generate the current date.
     *
     * @access private
     * @param Array $dataType
     * @param int $field
     */
    private function _dataTypeIsADate($dataType,$field) {
       if('date' === $dataType) {
            $this->_result[$field] = date('Ymd');
       }
    }
    
    /**
     * Checks to see if we have a datetype type, if we do
     * we generate the current date & time.
     *
     * @access private
     * @param Array $dateType
     * @param int $field
     */
    private function _dataTypeIsDateTime($dateType,$field) {
       if('datetime' === $dateType) {
            $this->_result[$field] = date(DATE_RFC822);
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
     */
    private function _parseSchema($field, $values) {
       foreach ($values as $value) {
            $this->_dataTypeIsAnInt($value,$field);
            $this->_dataTypeIsAString($value,$field);
            $this->_dataTypeIsADate($value,$field);
            $this->_dataTypeIsDateTime($value,$field);
        }
    }

    /**
     * Retrieves all our test data.
     * 
     * @access private
     * @param String    $key
     * @param Array     $value
     * @return Array
     */
    private function _retrieveTestData($key,$value) {
       if(0 !== $this->testDataCount()) {
            if(!empty($key) && !empty($value)) {
                foreach($this->_testData as $data) {
                    if($data[$key] === $value) {
                       return $data;
                    }
                }
            }
            else {
                return $this->_testData;                
            }
        }
        return false;
    }
    
    /**
     * Is used to run build & drop, seeing as both methods
     * have practically the same functionality, it seems
     * silly not to refactor them into this function.
     *
     * @access private
     * @param string $calledBy
     * @return bool
     * 
     * @todo Write test to ascertain whether the string
     *       build/drop & that it is actually a string.
     * 
     */
    private function _runFixtureMethod($calledBy) {
        try {
            $result = $this->_fixtureMethodCheck($calledBy);
            if(true === $result) {
                return true;
            }
        }
        catch (ErrorException $e) {
            echo $e->getMessage();
        }
        return false;
    }
    
    /**
     * Does the checking for our method call
     *
     * @access private
     * @param String $call
     * @return bool
     * 
     * @todo could be done better but this seems fine for the moment.
     * 
     */
    private function _fixtureMethodCheck($call) {
        if('drop' === $call) {
            $result = $this->_fixMan->dropTable();
        }
        if('setup' === $call) {
            $result = $this->_fixMan->setupTable($this->_fields,$this->_table);
        }
        return $result;
    }
    
    /**
     * Generates our fixture test data, we need this so we can
     * loop through our fields array, to ascertain the data type
     * of each piece of test data.
     *
     * @access public
     * @param int $numOfTestData
     * @return Array
     */
    private function _generateTestData($numOfTestData) {
        if(0 === count($this->_fields)) {
            throw new ErrorException('Fields not defined, can not generate without it.');
        }
        if(!is_int($numOfTestData)) {
            throw new ErrorException('Must supply number of test data using an integer.');
        }
        $results = array();
        $this->_result = array();
        for($i=0;$i<$numOfTestData;$i++) {
        	$fields = $this->getTableFields();
            foreach ($fields as $field=>$values) {
                DataTypeChecker::checkDataType($values);
                $this->_parseSchema($field, $values);
            }
            array_push($results,$this->_result);
        }
        return $results;
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
		if(null === $this->_testData) {
			return true;
		}
		else {
			$existingTestData = $this->getTestData('id',1);
			if(false === $existingTestData) {
				return true;
			}
			foreach ($testData as $key=>$value) {
				if(!array_key_exists($key, $existingTestData)){
				    throw new ErrorException( $key .' using ' .$value.' is an invalid test data.');
				}
			}
		}
		return false;
	}
	
	/**
	 * Sets our test data to our fixture.
	 *
	 * @access public
	 * @param Array $testData
	 * @return bool
	 * 
	 */
	public function addTestData($testData) {
		if(!is_array($testData)) {
			throw new ErrorException('Test data must be in an array format.');
		} 
		foreach ($testData as $data) {
			if(is_array($data)) {
				$this->_verifyTestData($data);
			}
			else {
				$this->_testData = $testData;
				break;
			}
		}
		return true;
	}
	
	/**
	 * Returns the fixtures table name.
	 *
	 * @return String
	 * 
	 */
	function getTableName() {
		return $this->_table;
	}
	
	function setTableName($tableName) {
		if(!is_string($tableName)) {
			throw new ErrorException('Table name must be a string');
		}
		$this->_table = $tableName;
		return true;
	}
	/**
	 * Gets our test data for use, if parameters are not passed
	 * we will retrieve all test data stored in this object, otherwise
	 * we will return the specific test data in question.
	 *
	 * @access public
	 * @param String $key
	 * @param String $value
	 * @return Array
	 * 
	 */
	public function getTestData($key='',$value='') {
		if(!is_string($key)) {
			throw new ErrorException('Test data id must be a string.');
		}
		if(!empty($key) && empty($value)) {
			throw new ErrorException('Must supply a value when submitting a key');
		}
		return $this->_retrieveTestData($key,$value);
		
	}
	
	/**
	 * Gets the fixture fields data in an array format.
	 *
	 * @access public
	 * @return Array
	 * 
	 */
	public function getTableFields() {
		if(0 === count($this->_fields)) {
			throw new ErrorException('No fixture fields present.');
		}
		else {
			return $this->_fields;
		}
	}
	
	/**
	 * Basic method, allowsing us to determine
	 * the number of test data we have within
	 * the fixture.
	 *
	 * @access public
	 * @return Int
	 * 
	 */
	public function testDataCount() {
		$result = 0;
		if(isset($this->_testData)) {
			$result = count($this->_testData);
		}
		return $result;
	}
	
	/*
	 * Wrapper functions
	 */
	
	/**
	 * Wrapper function used to build our fixture tables.
	 *
	 * @access public
	 * @return bool
	 * 
	 */
	public function setupTable() {		
		if(0 === count($this->_fields)) {
			throw new ErrorException('No table fields present.');
		}
		return $this->_runFixtureMethod('setup');
	}
	
	/**
	 * Sets PHPUnit_Fixture's field property.
	 *
	 * @param Array $fields
	 * @return bool
	 */
	function setFields(array $fields) {
		if(0 === count($fields)) {
			throw new ErrorException('Illegal field format.');
		}
		foreach ($fields as $name=>$data) {
				if(!is_string($name)) {
					throw new ErrorException('Field name must be a string.');
				}
				if(!is_array($data)) {
					throw new ErrorException('Data must be in an associative array.');
				}
				$this->_validateDataTypeFields($data);
		}
		$this->_fields = $fields;
		return true;
	}

	/**
	 * Another wrapper function, this time used for deleting
	 * our test tables.
	 *
	 * @access public
	 * @return bool
     * 
	 */
	public function dropTable() {
		return $this->_runFixtureMethod('drop');
	}
	
	/**
	 * Automatically generates our test data.
	 * 
	 * Once it generates our data, it then passes it
	 * to _addTestData to append to the _testData
	 * property.
	 *
	 * @access private
	 * @param int $numOfTestData
	 * @return bool
	 * 
	 */
	public function autoGenerateTestData($numOfTestData=10) {
		try {
			$result = $this->_generateTestData($numOfTestData);
			if(0 === count($result)) {
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
     * Populates our fixtures test table with our test data.
     *
     * @access public
     * @return bool
     * 
     */
    public function populate() {
        if(!$this->_fixMan->tableExists($this->_table)) {
            throw new ErrorException('Fixtures table is not present.');
        }
        return $this->_fixMan->insertTestData($this->_testData,$this->_table);
    }
    
    /**
     * Determines whether our test data already exists
     *
     * @access public
     * @param Array $testData
     * @return bool
     * 
     */
    public function testDataExists($testData) {
        if($this->testDataCount() > 0 ) {
            for($i=0;$i<$this->testDataCount();$i++) {
                if($this->_testData[$i] == $testData[$i]) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Gets a single data type field from our fixture.
     *
     * @param String $field
     * @return Array
     */
    function getSingleDataTypeField($field) {
    	if(!is_string($field)) {
    		throw new ErrorException('Field name must be a string.');
    	}
    	if(!array_key_exists($field,$this->_fields)) {
    		throw new ErrorException('Field id does not exist.');
    	}
    	return array($field => $this->_fields[$field]);
    }
    
    /**
     * Returns our results with a id auto incremented.
     *
     * @return Array
     */
    function retrieveTestDataResults() {
    	$testData = $this->getTestData();
    	for($i=0;$i<$this->testDataCount();$i++) {
    		$testData[$i]['id'] = $i+1;
    	}
    	return $testData;
    }
}