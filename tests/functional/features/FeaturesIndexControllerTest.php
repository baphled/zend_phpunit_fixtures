<?php

require_once dirname(__FILE__) . '/../../libs/TestHelper.php';

class FeaturesIndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase { 
	
	public $bootstrap;
	private $_featureFixtures;
	
	public function setup() {
		$this->_featureFixtures = new FeatureFixture();
		$this->_featureFixtures->setupTable();
		$this->_featureFixtures->populate();
		$this->bootstrap = dirname(__FILE__) . '/../../libs/bootstrap.php';		
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
		$this->assertQueryContentContains('h2', 'Features');
	}
	
	public function testDefaultFeaturesUrlShowsErrorMessageIfNoDataExists() {
		$this->_featureFixtures->truncateTable();
		$this->dispatch('/features');	
		$this->assertQueryContentContains('p', 'No features have been added yet.');
	}
	
	public function testDefaultFeaturesUrlShouldShowListOfFeaturesIfDataExists() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('td', 'To test a new feature');
	}
	
	public function testLinksToRelatedFunctionsPageAreAdded() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('a', 'View Functions');
	}
	
	public function testLinksToRelatedFunctionsAreCorrect() {
		$this->dispatch('/features');
		$this->assertQuery('td a[href*="functions/index/id/1"]');
	}
	
	public function testUrlForId() {
		$this->dispatch('/features/index/edit');
		$this->assertRedirectTo('/features');
	}
	
	public function testUrlForEditFeatureLink() {
		$this->dispatch('/features/index/edit/id/1');
		$this->assertQueryContentContains('h2', 'Edit Feature by ID');		
	}
	
	public function testIfRetrieveDataIsSuccessful() {
		$this->dispatch('/features/index/edit/id/1');
		$this->assertQueryContentContains('td', 'new feature');		
	}
	
}