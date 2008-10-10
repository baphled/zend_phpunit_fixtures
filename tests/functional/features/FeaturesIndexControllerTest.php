<?php

require_once dirname(__FILE__) . '/../../libs/TestHelper.php';

class FeaturesIndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase { 
	
	public $bootstrap;
	private $_featureFixtures;
	
	public function setup() {
		$this->_featureFixtures = new FeatureFixture();
		$this->_featureFixtures->setup();
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
	
	
	public function testFeaturesUrlHasTitleAsOneOfTheHeading() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('h3', 'Title');
	}
	
	
	public function testFeaturesUrlHasDescriptionAsOneOfTheHeading() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('h3', 'Description');
	}
	
	
	public function testDefaultFeaturesUrlShowsErrorMessageIfNoDataExists() {
		$this->_featureFixtures->truncate();
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
	
	public function testLinksToEditFunctionsPageAreAdded() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('a', 'Edit');
	}
	
	public function testLinksToRelatedFunctionsAreCorrect() {
		$this->dispatch('/features');
		$this->assertQuery('td a[href*="index/edit/id/1"]');
	}
	
	public function testUrlForFeatureEditIdPage() {
		$this->dispatch('/features/index/edit');
		$this->assertRedirectTo('/features');
	}
	
	public function testUrlForEditFeatureLink() {
		$this->dispatch('/features/index/edit/id/1');
		$this->assertQueryContentContains('h1', 'Edit Feature by ID');		
	}
	
	public function testToCheckIfRetrievingFeatureEditDataIsSuccessful() {
		$this->dispatch('/features/index/edit/id/1');
		$this->assertQueryContentContains('td', 'new feature');		
	}
	
	public function testLinksToDeleteFunctionsPageAreAdded() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('a', 'Delete');
	}
	
	public function testLinksToDeleteFunctionsAreCorrect() {
		$this->dispatch('/features');
		$this->assertQuery('td a[href*="index/delete/id/3"]');
	}
	
	/*Begining of the tests for the addeddate and the moddate 
	 * 
	 * 
	 */

	public function testFeaturesUrlHasAddeddateAsOneOfTheHeading() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('h3', 'Added Date');
	}
	
	
	public function testFeaturesUrlHasModdateAsOneOfTheHeading() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('h3', 'Modified Date');
	}
	
	
	public function testDefaultFeaturesUrlShouldShowListOfFeaturesIfAddedDateExists() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('td', '2008-10-10 15:56:03');								
	}
	
	
	public function testDefaultFeaturesUrlShouldShowListOfFeaturesIfModDateExists() {
		$this->dispatch('/features');
		$this->assertQueryContentContains('td', '2008-10-10 15:56:05');								
	}
}