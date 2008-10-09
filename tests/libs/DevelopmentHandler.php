<?php
/**
 * DevelopmentHandler
 * 
 * Used to generate our test data DB for development.
 * Seeing as this process can be cumbersome, I have
 * created this class to deal with the basic creation
 * of our DB and manipulation of test data.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 */
class DevelopmentHandler {
	
	/**
	 * Stores our FixtureManager, which will be used
	 * to interact with our fixtures test tables.
	 * 
	 * @access private
	 * @var FixtureManager
	 * 
	 */
	private $_fixMan;
	
	/**
	 * Initialises our FixtureManager with the defined
	 * environment.
	 * 
	 * @access public
	 * @param String $env Our testing environment.
	 * 
	 */
	public function __construct($env='development') {
        $this->_fixMan = new FixturesManager($env);
	}
	
	/**
	 * Runs our method calls
	 * 
	 * @access private
	 * @param  String $call
	 * @param  PHPUnit_Fixture_DB $fixture
	 * @return bool $result
	 * 
	 * @todo Functionality is scarily simular to PHPUnit_Fixture_DB's callMethod
	 * 
	 */
	private function _runTableMethod($call,$fixture) {
	   if($fixture instanceof PHPUnit_Fixture_DB) {
            return $this->_fixMan->fixtureMethodCheck($call,$fixture);    
        }
        else {
        	throw new ErrorException('Must be a decendant of PHPUnit_Fixtures');
        }
	}
	
	/**
	 * Builds our development database.
	 * 
	 * @access public
	 * @param  PHPUnit_Fixture_DB $fixture
	 * @return bool
	 * 
	 */
	public function build($fixture) {
		return $this->_runTableMethod('setup',$fixture);
	}

    /**
     * Populates our test table with our test data.
     *
     * @access public
     * @param  PHPUnit_Fixture_DB $fixture
     * @return bool
     *  
     */
    public function populate($fixture) {
        return $this->_runTableMethod('populate',$fixture);
    }
    
	/**
	 * Drops our development DB tables
	 * 
	 * @access public
	 * @return bool
	 * 
	 */
	public function drop() {
	   if($this->_fixMan->tablesPresent()) {
              return $this->_fixMan->dropTables();
        }
        return false;
    }
}