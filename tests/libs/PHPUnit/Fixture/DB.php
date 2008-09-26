<?php
/**
 * PHPUnit_Fixture_DB
 * 
 * Concerntrates on DB centric fixtures, will allow
 * us to populate, setup & drop our fixture DB tables &
 * test data.
 * 
 * Date: 23/09/08
 * Refactored DB centric functionality from PHPUnit_Fixture
 * to this class, which will solely deal with test data DB
 * interactions.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package PHPUnit_Fixture
 * @subpackage PHPUnit_Fixture_DB
 * 
 * @todo Implement functionality to allows users to specify an already
 *       setup table.
 *
 */

class PHPUnit_Fixture_DB extends PHPUnit_Fixture {
	 
    /**
     * Stores the fixtures table name
     *
     * @access protected
     * @var String
     * 
     */
    protected $_table = null;
    
    /**
     * Our fixture manager, used to handle 
     * the meat of fixture interactions.
     *
     * @access private
     * @var FixtureManager
     * 
     */
    private $_fixMan;
    
    public function __construct() {
        $this->_fixMan = new FixturesManager();
    }

    /**
     * Drops all test data DB's when object is destructed.
     * Used to keep our DB in its original format.
     * 
     */
    public function __destruct() {
    	try {
	        if($this->_fixMan->tablesPresent()) {
	              $this->drop();
	        }
	        $this->_fixMan = null;
    	}
    	catch(Exception $e) {
    		echo $e->getMessage();
    	}
    }
    
    /**
     * Used to call our CRUD methods
     * 
     * @access private
     * @param String    $call   The call we want to make.
     * @return bool
     * 
     */
    private function _callMethod($call) {
        try {
            $result = $this->_fixMan->fixtureMethodCheck($call,$this);
        }
        catch(Exception $e) {
        	$result = false;
            $e->getMessage();
        }
        return $result;
    }
    
    /**
     * Returns the fixtures table name.
     *
     * @return String
     * 
     */
    public function getName() {
        return $this->_table;
    }
    
    /**
     * Sets our test data DB table name
     * 
     * @access public
     * @param String    $tableName
     * @return bool
     * 
     */
    public function setName($tableName) {
        if(!is_string($tableName)) {
            throw new ErrorException('Table name must be a string');
        }
        $this->_table = $tableName;
        return true;
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
    public function setup() {
        if(0 === count($this->_fields)) {
            throw new ErrorException('No table fields present.');
        }
       return $this->_callMethod('setup');
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
        return $this->_callMethod('populate');
    }
    
    /**
     * Another wrapper function, this time used for deleting
     * our test tables.
     *
     * @access public
     * @return bool
     * 
     */
    public function drop() {
        return $this->_callMethod('drop');
    }
    
    /**
     * Wrapper function for truncating our test tables.
     * 
     * @access public
     * @return bool
     * 
     */
    public function truncate() {
        return $this->_callMethod('truncate');
    }
}