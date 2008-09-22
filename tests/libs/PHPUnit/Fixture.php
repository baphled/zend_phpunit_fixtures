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
 * @todo Implement functionality to allows users to specify an already
 *       setup table.
 * 
 */
class PHPUnit_Fixture {
	
	/**
	 * Stores the fixtures table name
	 *
	 * @access protected
	 * @var String
	 * 
	 */
	protected $_table = null;
	
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
	 * Our fixture manager, used to handle 
	 * the meat of fixture interactions.
	 *
	 * @access private
	 * @var FixtureManager
	 * 
	 */
	private $_fixMan;
	
	/**
	 * Stores our test data results
	 *
	 * @access private
	 * @var Array
	 * 
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
	
	public function __destruct() {
		if($this->_fixMan->tablesPresent()) {
		      $this->dropTable();
		}
		$this->_fixMan = null;
	}

	/**
     * Verify that our test data is of a valid
     * structure and submit it to our our _testData
     * property.
     *
     * @access private
     * @param Array $testData
     * 
     */
    private function _verifyTestData($testData) {
       try {
            $this->validateTestData($testData,$this);
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
            DataTypeChecker::dataTypeIsAnInt($value,$field,$this);
            DataTypeChecker::dataTypeIsAString($value,$field,$this);
            DataTypeChecker::dataTypeIsADate($value,$field,$this);
            DataTypeChecker::dataTypeIsDateTime($value,$field,$this);
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
     * @todo This would be so much more effient as an iterator.
     * 
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
     * @param string $called  The method that was called.
     * @return bool
     * 
     * @todo Write test to ascertain whether the string
     *       build/drop & that it is actually a string.
     * 
     */
    private function _runFixtureMethod($called) {
        try {
            $result = $this->_fixtureMethodCheck($called);
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
     * Generates our fixture test data, we need this so we can
     * loop through our fields array, to ascertain the data type
     * of each piece of test data.
     *
     * @access public
     * @param int $numOfTestData
     * @return Array
     * 
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
            foreach ($this->getTableFields() as $field=>$values) {
                DataTypeChecker::checkDataType($values);
                $this->_parseSchema($field, $values);
            }
            array_push($results,$this->_result);
        }
        return $results;
    }
	
    /**
     * Does the checking for our method call, at the moment
     * we can only use setup & drop calls.
     *
     * @access private
     * @param String $call      The called method.
     * @return bool
     * 
     * @todo could be done better but this seems fine for the moment.
     * 
     */
    protected function _fixtureMethodCheck($call) {
    	switch($call) {
    		case 'drop':
    		    $result = $this->_fixMan->dropTable();
    		    break;
    		case 'setup':
    		    $result = $this->_fixMan->setupTable($this->_fields,$this->_table);
    		    break;
    		case 'truncate':
    		    $result = $this->_fixMan->truncateTable($this->_table);
    		    break;
    		default:
    		    throw new ErrorException('Invalid fixture method call.');    		  
    	}
        return $result;
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
				if(!array_key_exists($key, $existingTestData)) {
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
	public function getTableName() {
		return $this->_table;
	}
	
	public function setTableName($tableName) {
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
     * Wrapper function for truncating our test tables.
     * 
     * @access public
     * @return bool
     * 
     */
    public function truncateTable() {
        return $this->_runFixtureMethod('truncate');
    }
    
	/**
	 * Sets PHPUnit_Fixture's field property.
	 *
	 * @param Array $fields
	 * @return bool
	 */
	public function setFields(array $fields) {
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
				DataTypeChecker::validateDataTypeFields($data);
		}
		$this->_fields = $fields;
		return true;
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