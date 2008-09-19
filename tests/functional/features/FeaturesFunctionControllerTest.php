<?php

require_once dirname(__FILE__) . '/../../libs/TestHelper.php';

class FeaturesFunctionControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	public $bootstrap;
	private $_functionFixtures;
	
	protected function setUp() {
		$this->_functionFixtures = new FunctionFixture();
		$this->_functionFixtures->setupTable();
		$this->_functionFixtures->populate();
		$this->bootstrap = dirname(__FILE__) . '/../../libs/bootstrap.php';
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->_functionFixtures = null;
	}
	
	public function testFunctionsUrlCallShouldUseFunctionModule() {
		$this->dispatch('/features/functions');
		$this->assertModule('features');
	}
	
	public function testFunctionUrlShouldUseFunctionsController() {
		$this->dispatch('/features/functions');
		$this->assertController('functions');
	}
	
	public function testDefaultFunctionsUrlHasFunctionsAsItsHeading() {
		$this->dispatch('/features/functions');
		$this->assertQueryContentContains('h2', 'Functions');
	}

	public function testDefaultFunctionsUrlShouldShowListOfFunctionsIfDataExists() {
		$this->dispatch('/features/functions');
		$this->assertQueryContentContains('td', 'To test a new description');
	}
	
	public function testLinksToViewFeaturesAreAdded() {
		$this->dispatch('/features/functions');
		$this->assertQueryContentContains('a', 'View Features');
	}
	
	public function testLinksToRelatedFeaturesAreCorrect() {
		$this->dispatch('/features/functions');
		$this->assertQuery('td a[href*="/functions/edit/id/1"]');
	}
	
	public function testUrlForFunctionEditIdPage() {
		$this->dispatch('/features/functions/edit');
		$this->assertRedirectTo('/features/functions');		
	}
	
	public function testUrlForEditFunctionLink() {
		$this->dispatch('/features/functions/edit/id/1');
		$this->assertQueryContentContains('h2', 'Edit Function by ID');				
	}
	
	public function testToCheckIfRetrievingFunctionEditDataIsSuccessful() {
		$this->dispatch('/features/functions/edit/id/1');
		$this->assertQueryContentContains('td', 'To test a new description');				
	}
}

