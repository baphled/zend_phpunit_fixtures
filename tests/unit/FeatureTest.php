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
			'noUserIDFeature' => array(
				'title' 	  => 'second feature',
				'description' => 'second feature'
			),
			'completeFeature' => array(
				'userid'	  => 1,
				'title' 	  => 'new feature',
				'description' => 'To test a new feature'
			),
			'secondFeature' => array(
				'userid'	  => 23,
				'title' 	  => 'anuva feature',
				'description' => 'feature description'
			),
			'anotherFeature'  => array(
				'userid'	  => 1,
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
		$this->_db = Zend_Registry::get('db');
		$this->_db->query('drop table if exists features');
		$this->_db->query(' CREATE TABLE features(
							id int AUTO_INCREMENT PRIMARY KEY ,
							userid int(10) NOT NULL,
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
	 * check if param set is valid
	 */
	function testInvalidParam(){
		$data = 'crappy stuff';
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);		
	}
	
	/**
	 * return exception if the title is null
	 *
	*/
	function testParamFeatureTitleNotNull() {
		$data = array(
					'title' 	  => null,
					'description' => 'To test a new feature'
				);
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testParamFeatureTitleNotEmpty() {
		$data = array(
					'title' 	  => '',
					'description' => 'To test a new feature'
				);
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	/**
	 * test that addNew returns true on success
	 */
	function testAddNewFeatureReturnsTrueOnSuccess(){
		$data = $this->_fixtures['completeFeature'];
		$result = $this->_feature->addNewFeature($data);
		$this->assertEquals(1,$result);
	}
	
	function testAddNewFeatureThrowsExceptionOnNoUserId(){
		$data = $this->_fixtures['noUserIDFeature'];
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testUserIdThrowExceptionIfNotNull(){
		$id = null;
		$this->setExpectedException('ErrorException');
		$this->_feature->updateFeature($id,$this->_fixtures['completeFeature']);
		
	}
	
	function testAddNewFeatureReturnsIntegerOnSuccess(){
		$data1 = $this->_fixtures['completeFeature'];
		$data2 = $this->_fixtures['secondFeature'];
		$this->_feature->addNewFeature($data1);
		$this->assertEquals(2,$this->_feature->addNewFeature($data2));
	}

	/**
	 * Need to test that if we have duplicate data our function
	 * returns false.
	 * 
	*/
	function testAddNewFeatureDoesNotAllowDuplicateData() {
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);
		$feature = $this->_fixtures['anotherFeature'];
		$result = $this->_feature->_featureExists($feature);
		$this->assertEquals(FALSE,$result);
	}
	
	function testFeatureExistReturnsTrueOnFeatureDuplication(){
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->_featureExists($data);
		$this->assertEquals(True,$result);
	}
	
	function testUpdateFeaturesReturnTrueOnSuccess(){
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);
		$data1 = $this->_fixtures['secondFeature'];
		$result = $this->_feature->updateFeature(1,$data1);
		$this->assertTrue($result);
	}
	
	function testUpdateFeaturesReturnFalseOnFailure(){
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->updateFeature(1,$data);
		$this->assertFalse($result);		
	}
	
	function testViewFeatureById(){
		$data = $this->_fixtures['completeFeature'];
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->viewFeature(1);
		$this->assertType('array', $result);
	}

}