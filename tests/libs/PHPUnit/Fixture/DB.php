<?php
/**
 * PHPUnit_Fixture_DB
 * 
 * Concerntrates on DB centric fixtures, will allow
 * us to populate, setup & drop our fixture DB tables &
 * test data.
 * 
 * Date: 08/10/08
 * Made abstract as it should only be subclassed.
 * 
 * Date: 23/09/08
 * Refactored DB centric functionality from PHPUnit_Fixture
 * to this class, which will solely deal with test data DB
 * interactions.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage PHPUnit_Fixture_DB
 * 
 * @todo Implement functionality to allows users to specify an already
 *       setup table.
 *
 */

abstract class PHPUnit_Fixture_DB extends PHPUnit_Fixture_DynamicDB {
    
    /**
     * Initialises our fixture manager.
     * 
     * @access public
     * @see PHPUnit_Fixture_DynamicDB::__construct()
     * 
     */
    public function __construct() {
    	parent::__construct();
    }

    /**
     * Drops all test data DB's when object is destructed.
     * Used to keep our DB in its original format.
     * 
     * @access public
     * 
     */
    public function __destruct() {
    	parent::__destruct();
    }
    
    /**
     * Returns the fixtures table name.
     *
     * @access public
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
        if (!is_string($tableName)) {
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
        if (0 === count($this->_fields)) {
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
        if (!$this->_fixMan->tableExists($this->_table)) {
            throw new ErrorException('Fixtures table is not present.');
        }
        return $this->_callMethod('populate');
    }
}
