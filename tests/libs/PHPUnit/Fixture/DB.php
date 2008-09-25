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
     * Does the checking for our method call, at the moment
     * we can only use setup & drop calls.
     *
     * @access protected
     * @param String $call      The called method.
     * @return bool
     * 
     * @todo could be done better but this seems fine for the moment.
     * 
     */
    protected function _fixtureMethodCheck($call) {
        switch($call) {
            case 'drop':
                $result = $this->_fixMan->dropTables();
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
        return $this->_runFixtureMethod('setup');
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
     * Another wrapper function, this time used for deleting
     * our test tables.
     *
     * @access public
     * @return bool
     * 
     */
    public function drop() {
        return $this->_runFixtureMethod('drop');
    }
    
    /**
     * Wrapper function for truncating our test tables.
     * 
     * @access public
     * @return bool
     * 
     */
    public function truncate() {
        return $this->_runFixtureMethod('truncate');
    }
}
?>