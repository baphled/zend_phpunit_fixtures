<?php
/**
 * Fixture
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
	public $_table;
	
    /**
     * Stores the fixtures table structure
     *
     * @var Array
     */
    public $_fields;
    
	/**
	 * Stores the fixtures test data.
	 *
	 * @var Array
	 */
	public $_testData;
	
	/**
	 * Sets our test data to our fixture.
	 *
	 * @param unknown_type $testData
	 * @return unknown
	 */
	function addTestData($testData) {
		if(!is_array($testData)) {
			throw new ErrorException('Test data must be in an array format.');
		}
		return true;
	}
	
	function getTestData($key='',$value='') {
		if(!is_string($key)) {
			throw new ErrorException('Test data id must be a string.');
		}
		if(!empty($key) && empty($value)) {
			throw new ErrorException('Must supply a value when submitting a key');
		}
		if($this->testDataCount() != 0) {
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
	 * @return Int
	 */
	function testDataCount() {
		$result = 0;
		if(isset($this->_testData)) {
			$result = count($this->_testData);
		}
		return $result;
	}
}

?>