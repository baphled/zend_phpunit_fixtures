<?php
/**
 * DevelopmentHandler
 * 
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
	 * @param $env String  our testing environment
	 * 
	 */
	public function __construct($env='development') {
        $this->_fixMan = new FixturesManager($env);
	}
	
	/**
	 * Builds our development database.
	 * 
	 * @access public
	 * @param $fixture PHPUnit_Fixture_DB
	 * @return  bool
	 * 
	 */
	public function buildDB($fixture) {
		if(!is_subclass_of($fixture,'PHPUnit_Fixture_DB')) {
			throw new ErrorException('Must be a decendant of PHPUnit_Fixtures');
		}
		if(null === $fixture->getTableName()) {
			throw new ErrorException('Fixture does not possess a table name.');
		}
		if(0 === count($fixture->getTableFields())) {
			throw new ErrorException('Fixture must have field data.');
		}
		return $this->_fixMan->setupTable($fixture->getTableFields(),$fixture->getTableName());
	}
	
	public function dropDB() {
	   if($this->_fixMan->tablesPresent()) {
              return $this->_fixMan->dropTable();
        }
        else {
        	return false;
        }
	}
}