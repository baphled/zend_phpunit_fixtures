<?php
require_once dirname(__FILE__) .'/../../libs/TestHelper.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

/**
 * ZendConfigSettingsTest
 * 
 * Basic little class that handles all our config settings, we basically
 * call each method when we need setup a particular setting.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2009
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 */
class ZendConfigSettingsTest extends PHPUnit_Framework_TestCase {
	public function __construct() {
		$this->setName ( 'Zend_ConfigSettings Testcase' );
	}
	
	function setUp() {
		parent::setUp();
		$this->_config = new Zend_Config_Ini(realpath(dirname(__FILE__) .'/../../../configs/environments.ini'));
	}
	
	function tearDown() {
		parent::tearDown();
	}

	function testConfigNotNull() {
		$this->assertNotNull($this->_config);
		
	}
	
	function testConfigSectionsAreLoaded() {
		$this->assertTrue($this->_config->areAllSectionsLoaded());
	}
	
	function testListSections() {
		$this->assertType('array',$this->_config->toArray());
	}
	
	function testListSetttingsSections() {
		foreach($this->_config->toArray() as $environment=>$values) {
			$environments[] = $environment;
			$this->assertNotNull($environments);
		}
	}
	
	function testZendConfigsSettingsHasGetEnvironments() {
		$this->assertNotNull(Zend_ConfigSettings::getEnvironments());
	}
	
	function testZendConfigsSettignGetEnvironmentsReturnsArray() {
		$this->assertType('array',Zend_ConfigSettings::getEnvironments());
	}
	
	function testZendConfigSettingsGetEnvironmentTakesConfigFileAsParam() {
		$this->assertNotNull(Zend_ConfigSettings::getEnvironments('/../../configs/environments.ini'));
	}
	
	function testZendConfigSettingsGetEnvironmentReturnsAnArrayOfEnvironments() {
		$this->assertContains('local',Zend_ConfigSettings::getEnvironments());
		$this->assertContains('staging',Zend_ConfigSettings::getEnvironments());
		$this->assertContains('test',Zend_ConfigSettings::getEnvironments());
		$this->assertContains('development',Zend_ConfigSettings::getEnvironments());
	}
}