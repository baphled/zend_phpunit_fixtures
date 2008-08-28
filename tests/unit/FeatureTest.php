<?php
/**
 *
 * FeatureTestCase
 * 
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd 2008
 * @package 
 * @subpackage FeaturesListTestSuite
 *
 * Date: 21 Aug 2008
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../libs/' ) . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../application/features/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_feature;
	
	public function __construct() {
		$this->setName ( 'FeatureTest Case' );
		
		$this->_fixtures = array(
			'userFixture'	  => array(
				'id'		  => 1,
				'fname'		  => 'nadjaha',
				'lname'		  => 'wohedally',
				'position'	  => 'developer'
			),
			'completeFeature' => array(
				'title' 	  => 'new feature',
				'description' => 'To test a new feature'
			),
			'anotherFeature'  => array(
				'title' 	  => 'second feature',
				'description' => 'second feature'
			),
			'viewableFeature' => array(
				'id'		  => 1,
				'title' 	  => 'new feature',
				'description' => 'To test a new feature'
			)
		);
	}
	
	public function setUp() {
		$this->_setUpConfig ();
		parent::setUp ();
		$this->_feature = new Features();
	}
	
	public function tearDown() {
		$this->_feature = null;
		parent::tearDown ();
	}
	
	public function testConstructor(){
		$this->assertNotNull($this->_feature);
	}
	
	/**
	 * 
	 */
}