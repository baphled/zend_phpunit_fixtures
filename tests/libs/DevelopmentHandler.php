<?php
/**
 * DevelopmentHandler
 * 
 * Used to generate our test data DB for development.
 * Seeing as this process can be combersome, we have
 * created this class to deal with the basic creation
 * of our DB.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package
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
	 * @param $env String  our testing environment.
	 * 
	 */
	public function __construct($env='development') {
        $this->_fixMan = new FixturesManager($env);
	}
	
	/**
	 * Runs our method calls
	 * 
	 * @access private
	 * @param String $call
	 * @param PHPUnit_Fixture_DB
	 * @return bool $result
	 * 
	 * @todo Functionality is scarily simular to PHPUnit_Fixture_DB's callMethod
	 * 
	 */
	private function _runTableMethod($call,$fixture) {
	   if(!is_subclass_of($fixture,'PHPUnit_Fixture_DB')) {
            throw new ErrorException('Must be a decendant of PHPUnit_Fixtures');
        }
        $result = $this->_fixMan->fixtureMethodCheck($call,$fixture);
        if(false !== $result) {
        	return $result;
        }
		else {
			return false;
		}
	}
	
	/**
	 * Builds our development database.
	 * 
	 * @access public
	 * @param $fixture PHPUnit_Fixture_DB
	 * @return  bool
	 * 
	 */
	public function build($fixture) {
		return $this->_runTableMethod('setup',$fixture);
	}

    /**
     * Populates our test table with our test data.
     *
     * @access public
     * @param PHPUnitFixture_DB $fixture
     * @return bool
     * 
     * @todo refactor, is a copy of PHPUnit_Fixture_DB's functionality
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
        else {
        	return false;
        }
	}
}