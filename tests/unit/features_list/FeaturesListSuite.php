<?php

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'tests/unit/features_list/FeatureTest.php';
require_once 'tests/unit/features_list/FunctionsTest.php';

/**
 * Static test suite.
 */
class FeaturesListSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'FeaturesListSuite' );
		
		$this->addTestSuite ( 'FeatureTest' );
		
		$this->addTestSuite ( 'FunctionsTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

