<?php
/**
 * PHPUnit_Fixture_Transactions
 * 
 * Deals with setting up an truncating our transaction
 * based fixture data. Here we can deal with the actual
 * setting up and cleaning of our test data and its tables.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id: DB.php 258 2008-10-31 13:37:10Z yomi $
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage PHPUnit_Fixture_Transactions
 * 
 */
class PHPUnit_Fixture_DynamicDB extends PHPUnit_Fixture {
	
	/**
     * Our fixture manager, used to handle 
     * the meat of fixture interactions.
     *
     * @access private
     * @var FixtureManager
     * 
     */
    protected $_fixMan;
    
	public function __construct() {
		$this->_fixMan = new FixturesManager();
	}
	
	/**
     * Drops all test data DB's when object is destructed.
     * Used to keep our DB in its original format.
     * 
     * @access public
     * 
     */
    public function __destruct() {
    	try {
	        if ($this->_fixMan->tablesPresent()) {
	              $this->truncate();
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
     * @access  private
     * @param   String    $call   The call we want to make.
     * @return  bool
     * 
     */
    protected function _callMethod($call) {
        try {
            $result = $this->_fixMan->fixtureMethodCheck($call, $this);
        }
        catch(Exception $e) {
        	$result = false;
            $e->getMessage();
        }
        return $result;
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
?>