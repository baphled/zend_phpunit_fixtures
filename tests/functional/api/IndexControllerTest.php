<?php

require_once dirname(__FILE__) . '/../../TestHelper.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase { 
	
	public $bootstrap;
	
	public function setup() {
		$this->bootstrap= dirname(__FILE__) . '/../../bootstrap.php';
		parent::setup();
	}
	
	public function testDefaultApiCallShouldUseApiModule() {
		$this->dispatch('/api');
        $this->assertModule('api');
	}
	
	public function testDefaultApiCallShouldUseIndexController() {
		$this->dispatch('/api');
		$this->assertController('index');
	}
	
	public function testDefaultApiCallShouldShowListOfUsers() {
		$this->dispatch('/api');
		$this->assertResponseCode(200);
	}
	
}