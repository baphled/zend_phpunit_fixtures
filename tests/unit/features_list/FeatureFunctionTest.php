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
	
}