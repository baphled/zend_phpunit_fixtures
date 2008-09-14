<?php
/**
 *
 * FunctionsTestCase
 * 
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package 
 * @subpackage TestSuite
 *
 * Date: 5 Sep 2008
 * 
 */

require_once dirname(__FILE__) . '/../../TestHelper.php';

class FunctionsTest extends PHPUnit_Framework_TestCase {
	
	private $_functions;
	
	private function _initialiseCompleteFunction(){
		$data = $this->_functionFixtures->getTestData('userid', 10);
		return $this->_functions->addNewFunction($data);
	}
	
	public function __construct() {
		$this->setName ( 'FunctionsTest Case' );
		$this->_functionFixtures = new FunctionFixture();
		
		$this->_fixtures = array(
			'userFixture'	  => array('id'	=> 1,'fname' => 'nadjaha',	'lname' => 'wohedally','position' => 'developer'),
			'noUserIDFeature' => array(	'title' => 'second function','description' => 'second function'));
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_functionFixtures->setupTable();
		$this->_functions = new Functions();
	}
	
	public function tearDown() {
		$this->_functions = null;
		$this->_functionFixtures = null;
		parent::tearDown ();
	}
	
	/*
	 * test if the table is set up
	 */
	public function testConstructor() {
		$this->assertNotNull($this->_functions);
	}
	
	/*
	 * test if data passed when adding a new function is an array.
	 * if not, throw an ErrorException
	 */
	public function testInvalidParam(){
		$data = 'not an array';
		$this->setExpectedException('ErrorException');
		$this->_functions->addNewFunction($data);
	}
	
	/*
	 * test to verify if title is not null
	 * if null, throw an ErrorException
	 */
	public function testVerifyIfTitleNotNull(){
		$data = array(
					'title' => null,
					'description' => 'To test if title is not null'
				);
		$this->setExpectedException('ErrorException');
		$this->_functions->addNewFunction($data);
	}
	
	/*
	 * test if title is not empty
	 * if empty, throw an ErrorException
	 */
	public function testVerifyIfTitleNotEmpty(){
		$data = array(
					'title' => '',
					'description' => 'To test if title is not empty'
				);
		$this->setExpectedException('ErrorException');
		$this->_functions->addNewFunction($data);
	}
	
	/*
	 * test if addNewFunction is actually adding data in the table
	 * to test, we will check that the id=1,
	 * since we are implementing data with id=1
	 */
	public function testAddNewFunctionReturnsTrueOnSuccess(){
		$result = $this->_initialiseCompleteFunction();
		$this->assertEquals(1,$result);	
	}
	
	/*
	 * test if the userid is not null
	 * if null, throw exception
	 */
	public function testAddNewFunctionThrowsExceptionIfNoUserId(){
		$data = $this->_fixtures['noUserIDFeature'];
		$this->setExpectedException('ErrorException');
		$this->_functions->addNewFunction($data);
	}
	
	/*
	 * test to view data by id
	 * returns an array
	 */
	public function testViewFunctionByIdReturnsArray(){
		$this->_initialiseCompleteFunction();
		$result = $this->_functions->viewFunction(1);
		$this->assertNotNull($result);
	}
	
	/*
	 * test if there is no duplication of data
	 * if yes,return false
	 */
	public function testAddNewFunctionAllowNoDuplication(){
		$this->_initialiseCompleteFunction();
		$result = $this->_functionFixtures->getTestData('userid',20);
		$final 	= $this->_functions->_functionExists($result);
		$this->assertEquals(FALSE, $final);
	}
	
	/*
	 * test if there is duplication of data
	 * if yes, return true
	 */
	public function testAddNewFunctionReturnsTrueOnFunctionDuplication(){
		$data 	= $this->_functionFixtures->getTestData('userid',10);
		$this->_functions->addNewFunction($data);
		$result = $this->_functions->_functionExists($data);
		$this->assertEquals(TRUE, $result);
	}
	
	/*
	 * test if the userid is null
	 * if yes, throw exception
	 */
	public function testUserIdThrowExceptionIfNull(){
		$id = null;
		$this->setExpectedException('ErrorException');
		$this->_functions->updateFunction($id, $this->_functionFixtures->getTestData('userid', 10));
	}
	 
	/*
	 * test to update function data
	 * return true on success
	 * the first id is for userid, the id in updateFunction is for functionid
	 */
	public function testUpdateFunctionsReturnTrueOnSuccess(){
		$this->_initialiseCompleteFunction();
		$data['title'] = 'updated title';
		$result = $this->_functions->updateFunction(1, $data);
		$this->assertTrue($result);
	}
	
	/*
	 * test if function has been updated,
	 * if not, return false
	 */
	function testUpdateFeaturesReturnFalseOnFailure(){
		$data = $this->_functionFixtures->getTestData('userid',10);
		$this->_functions->addNewFunction($data);
		$result = $this->_functions->updateFunction(1,$data);
		$this->assertFalse($result);		
	}

	/*
	 * test if the data has been deleted
	 * return true on success
	 */
	function testDeleteFunctionReturnTrueOnSuccess(){
		$data = $this->_functionFixtures->getTestData('userid',10);
		$this->_functions->addNewFunction($data);
		$result = $this->_functions->deleteFunction(1);
		$this->assertTrue($result);
	}
	
	function testDeleteFunctionReturnFalseOnFailure(){
		$result = $this->_functions->deleteFunction(2);
		$this->assertFalse($result);		
	}
	
}