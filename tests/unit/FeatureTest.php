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
		
		$this->_stub = $this->getMock('FeatureModel', array('insert'));
		$this->_stub->expects($this->any())
					->method('insert')
					->will($this->returnValue(1));
		
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
				'userId'	  => 1,
				'title' 	  => 'new feature',
				'description' => 'To test a new feature'
			),
			'anotherFeature'  => array(
				'userId'	  => 1,
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
	

}