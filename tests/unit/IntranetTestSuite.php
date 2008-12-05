<?php
require_once dirname(__FILE__) .'/../libs/TestHelper.php';
require_once 'phpunit_fixture/PHPUnitFixturesUnitSuite.php';

/**
 * Static test suite.
 * @version $Id: IntranetTestSuite.php 230 2008-10-01 11:53:33Z yomi $
 * @package Zend_PHPUnit_Scaffolding
 * 
 * $LastChangedBy: yomi $
 */
class IntranetTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'IntranetTestSuite' );

		$this->addTestSuite ( 'PHPUnitFixturesUnitSuite' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

