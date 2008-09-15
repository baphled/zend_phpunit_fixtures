<?php
/**
 *
 * FeatureFunctionTestCase
 * 
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd 2008
 * @package FeaturesList
 * @subpackage TestSuite_FeaturesList
 *
 * $LastChangedBy$
 
 * Date: 08 Sept 2008
 * 
 */

require_once dirname(__FILE__) . '/../../TestHelper.php';

class FeatureFunctionTest extends PHPUnit_Framework_TestCase {
	
	private $_featureFunction;
	
	private function _initialiseCompleteFF(){
		$data = $this->_featureFunctionFixtures->getTestData('function_id',10);
		return $this->_featureFunction->add($data);	
	}
	
	public function __construct(){
		$this->setName('FeatureFunctionTest Case');
		$this->_featureFunctionFixtures = new FeatureFunctionFixture();
	}
	
	public function setUp(){
		parent::setUp();
		$this->_featureFunctionFixtures->setupTable();
		$this->_featureFunction = new FeatureFunction();
	}
	
	public function tearDown(){
		$this->_featureFunction = null;
		$this->_featureFunctionFixtures = null;
		parent::tearDown();
	}
	
	public function testConstructor(){
		$this->assertNotNull($this->_featureFunction);
	}
	
	public function testInvalidParam(){
		$data = 'not an integer or can be null or empty also';
		$this->setExpectedException('ErrorException');
		$this->_featureFunction->add($data);
	}
	
	public function testAddNewFeatureFunctionReturnsTrueOnSuccess(){
		$result = $this->_initialiseCompleteFF();
		$this->assertType('array',$result);
	}
	
	public function testViewFeatureFunctionById(){
		$data = $this->_featureFunctionFixtures->getTestData(array('feature_id', 'function_id'), array(1, 10));
		$this->_featureFunction->add($data);
		$result = $this->_featureFunction->show(array(1,10));
		$this->assertNotNull($result);
		$this->assertEquals(1, count($result));
	}

	public function testAddNewFeatureFunctionDoesNotAllowDuplication(){
		$this->_initialiseCompleteFF();
		$data = $this->_featureFunctionFixtures->getTestData('function_id', 10);
		$result = $this->_featureFunction->exists($data);
		$this->assertEquals(TRUE, $result);
	}
	/*	
	public function testUpdateFeatureFunctionReturnsTrueOnSuccess(){
		$data = $this->_featureFunctionFixtures->getTestData('function_id',10);
		$this->_featureFunction->add($data);
		$data['function_id'] = 40;
		$result = $this->_featureFunction->update(1, $data);
		$this->assertTrue($result);
	}
	*/
	
	/*
	public function testUpdateFeatureFunctionReturnsFalseOnFailure(){
		$data = $this->_featureFunctionFixtures->getTestData('function_id',10);
		$this->_featureFunction->add($data);
		$result = $this->_featureFunction->update(1, $data);
		$this->assertFalse($result);
	}
	
	public function testDeleteFeatureFunctionReturnsTrueOnSuccess(){
		$data = $this->_featureFunctionFixtures->getTestData('function_id',10);
		$this->_featureFunction->add($data);
		$result = $this->_featureFunction->delete(1);
		$this->assertTrue($result);
	}
	*/
	public function testDeleteFeatureFunctionReturnsFalseOnFailure(){
		$result = $this->_featureFunction->delete(50);
		$this->assertFalse($result);
	}

}