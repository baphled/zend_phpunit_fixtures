<?php
require_once dirname(__FILE__) .'/../libs/TestHelper.php';
require_once 'features_list/FeaturesListSuite.php';
require_once 'phpunit_fixture/PHPUnitFixturesUnitSuite.php';

/**
 * Static test suite.
 * @author Yomi (baphled) Akindayini
 * @version $Id$
 * @package Zend_PHPUnit_Scaffolding
 * 
 * $LastChangedBy$
 */
class IntranetTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'IntranetTestSuite' );
		
		$this->addTestSuite ( 'FeaturesListSuite' );
		
		$this->addTestSuite ( 'PHPUnitFixturesUnitSuite' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

