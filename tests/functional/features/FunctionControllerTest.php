<?php

require_once dirname(__FILE__) . '/../../TestHelper.php';

class FunctionControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	public $bootstrap;
	private $_functionFixtures;
	
	protected function setUp() {
		$this->_functionFixtures = new FunctionFixture();
		$this->_functionFixtures->setupTable();
		$this->_functionFixtures->populate();
		$this->bootstrap = dirname(__FILE__) . '/../../bootstrap.php';
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
		$this->assertQueryContentContains('h1', 'Functions');
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
		$this->assertQuery('td a[href*="/index/index/id/1"]');
	}
	
}

