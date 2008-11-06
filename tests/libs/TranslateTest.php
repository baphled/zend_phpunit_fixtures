<?php

require_once '../../library/Ibetx/Translate/Translate.php';
require_once '../../library/Ibetx/Base/Controller.php';
require_once 'Zend/Controller/Request/Simple.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/View.php';
require_once '../../library/Ibetx/Api/Client.php';
require_once 'Zend/Config/Ini.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Translate test case.
 */
class TranslateTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var _Translate
	 */
	private $_Translate;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->_Translate = new Translate();
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated TranslateTest::tearDown()
		

		$this->_Translate = null;
		
		parent::tearDown ();
	}
	
	private function _getPathToConfigsDir () {
		$root = dirname(__FILE__);
		if (FALSE !== strpos($root,'/application')) {
			$root = substr($root, 0, strpos($root, '/application')).'/configs/';
		}
		elseif (FALSE !== strpos($root,'/test')) {
			$root = substr($root, 0, strpos($root, '/test', TRUE)).'/configs/';
		}
		elseif (FALSE !== strpos($root,'/library')) {
			$root = substr($root, 0, strpos($root, '/library', TRUE)).'/configs/';
		}
		return  $root;
	}
	
	private function _getPathToApplicationDir () {
		$root = dirname(__FILE__);
		if (FALSE !== strpos($root,'/application')) {
			$root = substr($root, 0, strpos($root, '/application')).'/application';
		}
		elseif (FALSE !== strpos($root,'/test')) {
			$root = substr($root, 0, strpos($root, '/test', TRUE)).'/application';
		}
		elseif (FALSE !== strpos($root,'/library')) {
			$root = substr($root, 0, strpos($root, '/library', TRUE)).'/application';
		}
		return  $root;
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
   		$config = new Zend_Config_Ini( $this->_getPathToConfigsDir().'settings.ini', 'general');
    	Zend_Registry::set('settings', $config);
	}
	
	/**
	 * Tests Translate->__construct() The constructor does nothing so there is no
	 * real test to do here.
	 */
	public function test__construct() {
		//$this->markTestIncomplete ( "__construct test not implemented" );
		$this->_Translate->__construct();
	}
	
	/**
	 * Tests Translate->__destruct()
	 */
	public function test__destruct() {
		// TODO Auto-generated TranslateTest->test__destruct()
		$this->markTestIncomplete ( "__destruct test not implemented" );		
		$this->_Translate->__destruct(/* parameters */);	
	}
	
	/*
	 * Test that the getTranslation method correctly rejects a parameter that is
	 * not an instance of Ibetx_Base_Controller
	 */
	public function testGetTranslationMethodRejectsParameterIfNotAnObject() {
		$this->setExpectedException('Exception','getTranslation parameter is not an object');
		$result = $this->_Translate->getTranslation('fred');
	}

	/*
	 * The parameter to getTranslation needs to be an instance of Ibetx_Base_Controller
	 * as it is the properties of _currentController and _currentAction that are
	 * needed
	 */
	
	/*
	 * Test that getTranslation rejects an object that is not a derivative
	 * of Ibetx_Base_Controller.
	 */
	public function testGetTranslationMethodRejectsParameterIfNotIbetxBaseController() {
		$obj = new stdClass();
		$this->setExpectedException('Exception','getTranslation parameter is not an Ibetx_Base_Controller object');
		$result = $this->_Translate->getTranslation($obj);
	}
	
	/*
	 * Some tests that assume the small function being tested is changed
	 * from private to public.
	 */
	
// This test requires the _getDefaultLanguage method to be public, which it was
// for testing. Now it has been changed back to private so this test is
// commented out, but left for reference in case it is needed in the future.
//	/*
//	 * Test that the _getDefaultLanguage can find the default language via the
//	 * registry from the settings.ini file.
//	 */
//	public function testGetDefaultLanguageCanGetEN() {
//		$this->assertEquals('en', $this->_Translate->_getDefaultLanguage());
//	}
	
	
// These tests require the _validateLangString method to be public, which it was
// for testing. Now it has been changed back to private so this they have been
// commented out, but left for reference in case it is needed in the future.
//	/*
//	 * Test that _validateLangString returns the default language of en if
//	 * it is given more than 5 characters as the locale parameter.
//	 */
//	public function test_validateLangStringReturnsDefaultEnWithMoreThan5Chars() {
//		$this->assertEquals('en', $this->_Translate->_validateLangString('123456'));
//	}
//	
//	/*
//	 * Test that _validateLangString returns the default language of en if
//	 * it is given 4 characters as the locale parameter.
//	 */
//	public function test_validateLangStringReturnsDefaultEnWith4Chars() {
//		$this->assertEquals('en', $this->_Translate->_validateLangString('1234'));
//	}
//	
//	/*
//	 * Test that _validateLangString returns the default language of en if
//	 * it is given less than 2 characters as the locale parameter.
//	 */
//	public function test_validateLangStringReturnsDefaultEnWith1Char() {
//		$this->assertEquals('en', $this->_Translate->_validateLangString('1'));
//	}
//	
//	/*
//	 * Test that _validateLangString returns the same as the locale parameter
//	 * if it is given a two character string.
//	 */
//	public function test_validateLangStringReturnsSame2CharsAsInput() {
//		$this->assertEquals('fr', $this->_Translate->_validateLangString('fr'));
//	}
//	
//	/*
//	 * Test that _validateLangString returns the same as the locale parameter
//	 * if it is given a five character string.
//	 */
//	public function test_validateLangStringReturnsSame5CharsAsInput() {
//		$this->assertEquals('fr_FR', $this->_Translate->_validateLangString('fr_FR'));
//	}
//	
//	/*
//	 * Test that _validateLangString returns the default locale 
//	 * if it is given a five character string but without an underscore.
//	 */
//	public function test_validateLangStringRejects5CharsWithoutUnderscore() {
//		$this->assertEquals('en', $this->_Translate->_validateLangString('frxFR'));
//	}
	
	
// This test requires the _getTranslationsDirectoryPath method to be public, which it was
// for testing. Now it has been changed back to private so this test is
// commented out, but left for reference in case it is needed in the future.
//	/*
//	 * Test that _getTranslationsDirectoryPath returns the path from the
//	 * "application" directory to that with the language translation files.
//	 */
//	public function test_getTranslationsDirectoryPathReturnsPathInSettingsIni() {
//		$this->assertEquals('languages', $this->_Translate->_getTranslationsDirectoryPath());
//	}
	
// This test requires the _getPathToApplicationDir method to be public, which it was
// for testing. Now it has been changed back to private so this test is
// commented out, but left for reference in case it is needed in the future.
//	/*
//	 * Test that the _getPathToApplicationDir method returns the
//	 * given hard-coded path. Note this will only work where the
//	 * path is /var/www/ix9front/application so is sensitive to any
//	 * directory structure change from computer to computer.
//	 */
//	public function test_getPathToApplicationDirReturnsPath() {
//		$path = $this->_Translate->_getPathToApplicationDir();
//		$this->assertEquals('/var/www/ix9front/application',$path);
//	}
	
// This test requires the _makeUpTranslationPathName method to be public, which it was
// for testing. Now it has been changed back to private so this test is
// commented out, but left for reference in case it is needed in the future.
//	/*
//	 * Test that _getTranslationsDirectoryPath returns the path from the
//	 * "application" directory to that with the language translation files.
//	 * This assumes that settings.ini contains "language.directory = languages"
//	 * in the "[general]" section.
//	 */
//	public function test_makeUpTranslationPathNameReturnsPathInSettingsIni() {
//		$reqObj = new Zend_Controller_Request_Simple();
//		$reqObj->setActionName('myAction');
//		$reqObj->setControllerName('myController');
//		$reqObj->setParam('lang','fr');
//		$respObj = new Zend_Controller_Response_Cli();
//		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
//		$contObj->init();
//		$arr = $this->_Translate->_makeUpTranslationPathName($contObj);
//		$this->assertEquals($this->_getPathToApplicationDir().'/languages/fr/myController_myAction.fr.php', $arr['pathName']);
//	}
	
	/*
	 * Test that if the parameter is an Ibetx_Base_Controller with the 
	 * _currentController and _currentAction set then the values are not null.
	 * 
	 */
	public function testGetTranslationParameterObjectHasNonNullControllerAndActionNames() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		// Presently no way to access the controller and action names within
		// the Translate object.
		// @TODO Complete this test or remove it when access to data available.
		$this->markTestIncomplete();
	}
	
	/*
	 * Test that if the parameter params does not cause an error if it is null.
	 * It will need to default to a default language (en) but that has not been
	 * coded yet. Not much of a test now but will be as more code is added.
	 * 
	 */
	public function testGetTranslationControllerRequestObjParamsCatersForNull() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
	}
	
	/*
	 * Test that if the parameter params is an array if it is not null. 
	 * It will need to default to a default language (en) if not an array
	 * but that has not been coded yet. 
	 * Not much of a test now but will be as more code is added.
	 *  
	 */
	public function testGetTranslationControllerRequestObjParamsIsArrayIfNotNull() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		// Presently no way to access the controller and action names within
		// the Translate object.
		// @TODO Complete this test or remove it when access to data available.
		//$this->markTestIncomplete();
	}
	
	/*
	 * Test that calling the getTranslation method for the en test language file
	 * returns an array.
	 *  
	 */
	public function testGetTranslationOnTestEnFileReturnsArray() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertType("array", $result);
	}
	
	/*
	 * Test that calling the getTranslation method for the en test language file
	 * returns an array that contains the keys TEST_TEXT_1 and TEST_TEXT_2
	 * from the page specific file.
	 *  
	 */
	public function testGetTranslationOnTestEnFileReturnsArrayWithTestPageKeys() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$key1Exists = array_key_exists('TEST_TEXT_1', $result);
		$this->assertTrue($key1Exists,'Translation key TEST_TEXT_1 does not exist');
		$key1Exists = array_key_exists('TEST_TEXT_2', $result);
		$this->assertTrue($key1Exists,'Translation key TEST_TEXT_2 does not exist');
	}
	
	/*
	 * Test that calling the getTranslation method for the en test language file
	 * returns an array that contains the correct values for the keys
	 * TEST_TEXT_1 and TEST_TEXT_2 from the page specific file.
	 *  
	 */
	public function testGetTranslationOnTestEnFileReturnsArrayWithCorrectKeyValues() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertEquals('Test Text 1',$result['TEST_TEXT_1']);
		$this->assertEquals('Test Text 2',$result['TEST_TEXT_2']);
	}
	
	/*
	 * Test that calling the getTranslation method for the common en file
	 * returns an array that contains the key DUMMY_TEST
	 * from the common file.
	 *  
	 */
	public function testGetTranslationOnTestEnCommonFileReturnsArrayWithKey() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$key1Exists = array_key_exists('DUMMY_TEST', $result);
		$this->assertTrue($key1Exists,'Translation key DUMMY_TEST does not exist');
	}
	
	/*
	 * Test that calling the getTranslation method for the common en file
	 * returns an array that contains the key DUMMY_TEST
	 * from the common file.
	 *  
	 */
	public function testGetTranslationOnTestEnCommonFileReturnsCorrectData() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$key1Exists = array_key_exists('DUMMY_TEST', $result);
		$this->assertEquals('A dummy test message, do not delete.',$result['DUMMY_TEST']);
	}
	
	/*
	 * The above tests show that the translation data can be returned from
	 * the getTranslation method, but can it be assigned to the view of the
	 * controller object that is passed as parameter?
	 */
	public function testGetTranslationCanAssignToView() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertEquals('A dummy test message, do not delete.',$contObj->view->test1['DUMMY_TEST']);
	}
	
	/*
	 * Many of the commented out tests above required private methods to 
	 * be public. Now that translations can be got out of the public 
	 * routine, it is possible to now tell more about whether duff
	 * locale values are handled properly.
	 */
	
	/*
	 * Test whether a duff region part of a locale (e.g. XX) is correctly
	 * reduced to just the language (e.g. en). Use locale "en_XX" for this.
	 */
	public function testDuffRegion_en_XX() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','en_XX');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertEquals('A dummy test message, do not delete.',$contObj->view->test1['DUMMY_TEST']);
	}
	
	/*
	 * Test whether a duff locale (e.g. YY_XX) is correctly
	 * reduced to the default language (e.g. en).
	 */
	public function testDuffLocaleDefaultsto_en() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','YY_XX');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertEquals('A dummy test message, do not delete.',$contObj->view->test1['DUMMY_TEST']);
	}
	
	/*
	 * Test whether a duff 5 character locale (e.g. YYYXX) without the underscore is correctly
	 * reduced to the default language (e.g. en).
	 */
	public function testDuff5CharLocaleWithoutUnderscoreDefaultsto_en() {
		$reqObj = new Zend_Controller_Request_Simple();
		$reqObj->setActionName('myAction');
		$reqObj->setControllerName('myController');
		$reqObj->setParam('lang','YYYXX');
		$respObj = new Zend_Controller_Response_Cli();
		$contObj = new Ibetx_Base_Controller( $reqObj, $respObj);
		$contObj->init();
		$result = $this->_Translate->getTranslation($contObj);
		$this->assertEquals('A dummy test message, do not delete.',$contObj->view->test1['DUMMY_TEST']);
	}
	
}

