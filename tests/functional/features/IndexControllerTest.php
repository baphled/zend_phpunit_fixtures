<?php

require_once dirname(__FILE__) . '/../../TestHelper.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase { 
	
	public $bootstrap;
	private $_featureFixtures;
	
	public function setup() {
		$this->_featureFixtures = new FeatureFixture();
		$this->_featureFixtures->setupTable();
		$this->_featureFixtures->populate();
		$this->bootstrap= dirname(__FILE__) . '/../../bootstrap.php';		
		parent::setup();
	}
	
	function tearDown() {
		$this->_featureFixtures = null;
	}
	public function testDefaultFeaturesUrlCallShouldUseFeaturesModule() {
		$this->dispatch('/features');
        $this->assertModule('features');
	}
	
	public function testDefaultFeaturesUrlCallShouldUseIndexController() {
		$this->dispatch('/features');
		$this->assertController('index');
	}
	
	public function testDefaultFeaturesUrlHasFeaturesAsItsHeading() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('h1', 'Features');
	}
	
	public function testDefaultFeaturesUrlShowsErrorMessageIfNoDataExists() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('p', 'No features have been added yet.');
	}
	
	public function testDefaultFeaturesUrlShouldShowListOfFeaturesIfDataExists() {
		$this->dispatch('/features');
		var_dump($this->_response);
	}
	
}