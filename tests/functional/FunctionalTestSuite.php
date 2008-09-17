<?php
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'api/ApiIndexControllerTest.php';
require_once 'features/FeaturesIndexControllerTest.php';

/**
 * Static test suite.
 *
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id: FeaturesListSuite.php 186 2008-09-16 09:13:47Z yomi $
 * @copyright 2008
 * @package FeaturesList
 * @subpackage TestSuite_FeaturesList
 *
 * $LastChangedBy: yomi $
 *
 */
class FunctionalTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'FunctionalTestSuite' );
		
		$this->addTestSuite ( 'ApiIndexControllerTest' );		
		$this->addTestSuite ( 'FeaturesIndexControllerTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}