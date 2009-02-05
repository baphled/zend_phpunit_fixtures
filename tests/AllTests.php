<?php
require_once dirname(__FILE__) .'/libs/TestHelper.php';
require_once 'unit/IntranetTestSuite.php';

/**
 * Static test suite.
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008-2009
 * @version $Id: IntranetTestSuite.php 193 2008-09-17 08:56:28Z yomi $
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage TestSuite_AllTests
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
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}
