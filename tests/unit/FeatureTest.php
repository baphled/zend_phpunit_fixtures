<?php
/**
 *
 * FeatureTestCase
 * 
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: 21 Aug 2008
 * 
 */

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../libs/' ) . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../library/' . PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../application/features/models/' . PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureTest extends Module_PHPUnit_Framework_TestCase {
	
	private $_feature;
	
	public function __construct() {
		$this->setName ( 'FeatureTest Case' );
		
		$this->_fixtures = array(
			'completeFeature' => array(
				'title' 	  => 'new feature',
				'description' => 'To test a new feature'
			),
			'anotherFeature' => array(
				'title' 	  => 'sum feature',
				'description' => 'new feature'
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
		$this->_db = Zend_Registry::get('db');
		$this->_db->query('drop table if exists features');
		$this->_db->query(' CREATE TABLE features(
							id int AUTO_INCREMENT PRIMARY KEY ,
							title varchar( 255 ) NOT NULL ,
							description varchar( 255 ) NOT NULL
						 )');
		$this->_feature = new Features();
	}
	
	public function tearDown() {
		$this->_feature = null;
		$this->_db->query('drop table features');
		parent::tearDown ();
	}
	
	public function testConstructor(){
		$this->assertNotNull($this->_feature);
	}
	
	/**
	 * test for invalid data. 
	 * data must be an array, if not throw an exception
	 */
	public function testInvalidParam(){
		$data = 'crappy stuff';
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);		
	}
	
	/**
	 * return exception if the title is null
	 *
	 */
	function testParamFeatureKeyNotNull() {
		$data = array(
					'title' 	  => null,
					'description' => 'To test a new feature'
				);
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	/**
	 * Need to test that if we have duplicate data our function
	 * returns false.
	 * 
	 */
	function testAddNewFeatureDoesNotAllowDuplicateData() {
		$feature = $this->_fixtures['anotherFeature'];
		$result = $this->_feature->_featureExists($feature);
		$this->assertEquals(FALSE,$result);
	}
	
	/**
	 * list the features
	 * if empty, should throw an exception
	 */
	function testFeatureListEmpty(){
		$this->setExpectedException('ErrorException');
		$this->_feature->listFeatures();
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function testShowFeatureReturnsWhatWeExpect(){
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);	
		$result = $this->_feature->listFeatures();
		$this->assertEquals(1,count($result));
	}
	
	/**
	 * check if feature that has been entered
	 * is what we are getting back
	 */
	function testViewFeatureByIdReturnsExpected(){
		$data 	  = $this->_fixtures['completeFeature'];
		$expected = $this->_fixtures['viewableFeature'];
		$this->_feature->addNewFeature($data);	
		$result = $this->_feature->viewFeatureById(1);
		$this->assertEquals($result, $expected);
	}
}