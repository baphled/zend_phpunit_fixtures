<?php

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'FeatureTest.php';
require_once 'FunctionsTest.php';

/**
 * Static test suite.
 *
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package FeaturesList
 * @subpackage TestSuite_FeaturesList
 *
 * $LastChangedBy$
 *
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

