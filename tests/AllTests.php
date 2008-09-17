<?php
require_once dirname(__FILE__) .'/libs/TestHelper.php';
require_once 'unit/IntranetTestSuite.php';
require_once 'functional/FunctionalTestSuite.php';

/**
 * Static test suite.
 * @author Yomi (baphled) Akindayini
 * @version $Id: IntranetTestSuite.php 193 2008-09-17 08:56:28Z yomi $
 * 
 * $LastChangedBy: yomi $
 */
class AllTests extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'AllTests' );
		
		$this->addTestSuite ( 'IntranetTestSuite' );		
		$this->addTestSuite ( 'FunctionalTestSuite' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}