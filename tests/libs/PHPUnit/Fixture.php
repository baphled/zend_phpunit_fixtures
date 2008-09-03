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
 * 
 * Date: 31/08/2008
 * Created basic implementation from test case, which allows us to count
 * the amount of test data present, aswell as retrieve, specific test data
 * & a whole of list of test data which can be used for testing.
 * 
 * @todo Turn all property with a '_' prefix into private properties.
 * @todo Add functionality to automatically generate test data, Which
 *       be a whole new test case.
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
	public $_table = null;
	
    /**
     * Stores the fixtures table structure
     *
     * @var Array
     */
    public $_fields = array();
    
	/**
	 * Stores the fixtures test data.
	 *
	 * @var Array
	 */
	public $_testData = null;
	
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
	private $_result;
	
	
	function __construct() {
		$this->_fixMan = new FixturesManager();
	}
	
	/**
	 * Determines whether our test data already exists
	 *
	 * @access public
	 * @param Array $testData
	 * @return bool
	 * 
	 */
	function testDataExists($testData) {
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
	 * Validates that our test data is of the same structure
	 * as pre-existing data.
	 *
	 * @access public
	 * @param Array $testData
	 * @return bool
	 * 
	 */
	function validateTestData($testData) {
		$data = $this->getTestData('id',1);
		if(null === $this->_testData) {
			return true;
		}
		else {
			foreach ($testData as $key=>$value) {
				if(!array_key_exists($key, $data)){
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
	function addTestData($testData) {
		if(!is_array($testData)) {
			throw new ErrorException('Test data must be in an array format.');
		} 
		foreach ($testData as $data) {
			if(is_array($data)) {
				try {
					$this->validateTestData($data);
    				$this->_testData[] = $data;
				}
				catch(ErrorException $e) {
					throw new ErrorException($e->getMessage());
				}
			}
			else {
				$this->_testData = $testData;
				break;
			}
		}
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
	 * @todo Refactor method, if clause on 165 needs lookin @.
	 */
	function getTestData($key='',$value='') {
		if(!is_string($key)) {
			throw new ErrorException('Test data id must be a string.');
		}
		if(!empty($key) && empty($value)) {
			throw new ErrorException('Must supply a value when submitting a key');
		}
		if($this->testDataCount() != 0) {                 // smells, cant this be put in a private function.
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
	 * Basic method, allowsing us to determine
	 * the number of test data we have within
	 * the fixture.
	 *
	 * @access public
	 * @return Int
	 * 
	 */
	function testDataCount() {
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
	function buildFixtureTable() {
		if(0 === count($this->_fields)) {
			throw new ErrorException('No table fields present.');
		}
		try {
			$result = $this->_fixMan->buildFixtureTable($this->_fields,$this->_table);
	        if(true === $result) {
	             return true;
	        }			
		}
		catch(ErrorException $e) {
			echo $e->getMessage();
		}
		return false;
	}
	
	/**
	 * Another wrapper function, this time used for deleting
	 * our test tables.
	 *
	 * @access public
	 * @return bool
	 * 
	 * @todo   The implementation of this and build look scarily simular,
     *         we need to refactor.
     * 
	 */
	function dropFixtureTable() {
		try {
			$result = $this->_fixMan->deleteFixturesTable();
			if(true === $result) {
				return true;
			}
		}
		catch (ErrorException $e) {
			echo $e->getMessage();
		}
		return false;
	}
	
	function populateFixtures() {
		if(!$this->_fixMan->fixtureTableExists($this->_table)) {
			throw new ErrorException('Fixtures table is not present.');
		}
		try {
			$result = $this->_fixMan->insertTestData($this->_testData,$this->_table);
			if(true === $result) {
				return true;
			}
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		return false;
	}
	
	function _parseFixtureSchema($field, $values) {
	   foreach ($values as $key=>$value) {
            if('integer' === $value && 'id' !== $key) {
            	$this->_result[$field] = rand();
            }
            elseif('string' === $value) {
                $this->_result[$field] = 'my string';
            }
            elseif('date' === $value) {
                $this->_result[$field] = date('d-m-Y');
            }
            elseif('datetime' === $value) {
                $this->_result[$field] = date('d-m-Y h:i:s');
            }
        }
	}
	
	function _generateFixtureTestData() {
		if(0 === count($this->_fields)) {
			throw new ErrorException('Fields not defined, can not generate without it.');
		}
		$results = array();
		$this->_result = array();
		
		foreach ($this->_fields as $field=>$values) {
			DataTypeChecker::checkDataType($values);
			$this->_parseFixtureSchema($field, $values);
		}
		array_push($results,$this->_result);
		return $results;
	}
	
	function autoGenerateTestData() {
		return false;
	}
}