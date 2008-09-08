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

set_include_path ( '.' . PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../../libs/' ) 
					   .PATH_SEPARATOR . realpath ( dirname ( __FILE__ ) . '/../../fixtures/' ) 
					   .PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../../library/' 
					   .PATH_SEPARATOR . dirname ( __FILE__ ) . '/../../../application/features/models/' 
					   .PATH_SEPARATOR . get_include_path () );

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureTest extends PHPUnit_Framework_TestCase {
	
	private $_feature;
	
	private function _initialiseCompleteFeature(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		return $this->_feature->addNewFeature($data);	
	}
	
	public function __construct() {			
		$this->setName ( 'FeatureTest Case' );
		$this->_featureFixtures = new FeatureFixture();
		
		$this->_fixtures = array(
			'userFixture'	  => array('id'	=> 1,'fname' => 'nadjaha',	'lname' => 'wohedally','position' => 'developer'),
			'noUserIDFeature' => array(	'title' => 'second feature','description' => 'second feature'));
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_featureFixtures->dropFixtureTable();
		$this->_featureFixtures->setupFixtureTable();
		$this->_feature = new Features();
	}
	
	public function tearDown() {
		$this->_feature = null;
		//$this->_featureFixtures->dropFixtureTable();
		$this->_featureFixtures = null;
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
		$result = $this->_initialiseCompleteFeature();
		$this->assertEquals(1,$result);
	}
	
	function testAddNewFeatureThrowsExceptionOnNoUserId(){
		$data = $this->_fixtures['noUserIDFeature'];
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testAddNewFeatureReturnsIntegerOnSuccess(){
		$data1 = $this->_featureFixtures->getTestData('userid',1);
		$data2 = $this->_featureFixtures->getTestData('userid',13);
		$this->_feature->addNewFeature($data1);
		$this->assertEquals(3,$this->_feature->addNewFeature($data2));
	}
	
	function testViewFeatureById(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->show(1);
		$this->assertType('array', $result);
	}
	
	/**
	 * Need to test that if we have duplicate data our function
	 * returns false.
	 * 
	*/
	function testAddNewFeatureDoesNotAllowDuplicateData() {
		$this->_initialiseCompleteFeature();
		$feature = $this->_featureFixtures->getTestData('userid',23);
		$result = $this->_feature->_featureExists($feature);
		$this->assertEquals(FALSE,$result);
	}
	
	function testFeatureExistReturnsTrueOnFeatureDuplication(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->_featureExists($data);
		$this->assertEquals(True,$result);
	}
	
	function testUserIdThrowExceptionIfNotNull(){
		$id = null;
		$this->setExpectedException('ErrorException');
		$this->_feature->updateFeature($id,$this->_featureFixtures->getTestData('userid',1));
		
	}
	
	function testUpdateFeaturesReturnTrueOnSuccess(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		$this->_feature->addNewFeature($data);
		$data['title'] = 'shitty ting';
		$result = $this->_feature->updateFeature(1,$data);
		$this->assertTrue($result);
	}
	
	function testUpdateFeaturesReturnFalseOnFailure(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->updateFeature(1,$data);
		$this->assertFalse($result);		
	}
	
	function testDeleteFeatureReturnTrueOnSuccess(){
		$data = $this->_featureFixtures->getTestData('userid',1);
		$this->_feature->addNewFeature($data);
		$result = $this->_feature->deleteFeature(1);
		$this->assertTrue($result);
	}
	
	function testDeleteFeatureReturnFalseOnFailure(){
		$result = $this->_feature->deleteFeature(2);
		$this->assertFalse($result);		
	}


}