<?php
/**
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package TestSuite
 *
 */

set_include_path ( '.' . PATH_SEPARATOR .realpath(dirname(__FILE__) .'/../libs/') . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR .dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR. dirname ( __FILE__ ) . '/../../application/default/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

/**
 * Static test suite.
 */
class PHPUnitFixturesUnitSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'PHPUnitFixturesUnitSuite' );
		
		$this->addTestSuite ( 'FixturesManagerTest' );
		$this->addTestSuite ( 'FixtureTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}