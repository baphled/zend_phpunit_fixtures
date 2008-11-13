<?php
/**
 *
 * FeatureTestCase
 * 
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd 2008
 * @package FeaturesList
 * @subpackage TestSuite_FeaturesList
 *
 * $LastChangedBy$
 *
 * Date: 21 Aug 2008
 * 
 */

require_once dirname(__FILE__) . '/../../libs/TestHelper.php';

class FeatureTest extends PHPUnit_Framework_TestCase {
	
	private $_feature;

	public function __construct() {			
		$this->setName ( 'FeatureTest Case' );
		$this->_featureFixtures = new FeatureFixture();
	}
	
	public function setUp() {
		parent::setUp ();
		$this->_featureFixtures->setup();
		$this->_date = date('Ymd');
		$this->_feature = new Features();
	}

	public function tearDown() {
		$this->_feature = null;
		$this->_featureFixtures = null;
		parent::tearDown();
	}
	
	/**
	 * Helper functions.
	 */
	function _setupSingleFixtures() {
		$this->_featureFixtures->autoGen(1);
	}
	
	function _getTestData() {
		$this->_setupSingleFixtures();
		return $this->_featureFixtures->get();
	}
	
	private function _initialiseCompleteFeature(){
		$data = $this->_getTestData();
		return $this->_feature->addNewFeature($data[0]);	
	}
	
	private function _getDummyData() {
		return array('userid'=>'1',
					  'title'=>'new feature',
					  'description' => 'To test a new feature'
				      );
	}

	/**
	 * Auto increments ID
	 */
	function _returnFeatureData() {
		$this->_setupSingleFixtures();
		return $this->_featureFixtures->retrieveResults();
	}
	
	private function _returnDataAndAddFeature() {
		$data = $this->_returnFeatureData();
		$this->_feature->addNewFeature($data[0]);
		return $data;
	}
	
	/*
	 * Helper End.
	 */
	
	public function testConstructor(){
		$this->assertNotNull($this->_feature);
	}
	
	private function _setDate($date) {
		if (!empty($date)) {
			return new Zend_Date($date, Zend_Date::ISO_8601);
		}
		else {
			throw new ErrorException('Date is not specified.');
		}
	} 
	/**
	 * check if param set is valid
	 */
	function testInvalidParam(){
		$data = 'crappy stuff';
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);		
	}
	
	/**
	 * return exception if the title is null
	*/
	function testParamFeatureTitleNotNull() {
		$data = array(
					'title' 	  => null,
					'description' => 'To test a new feature'
				);
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testParamFeatureTitleNotEmpty() {
		$data = array(
					'title' 	  => '',
					'description' => 'To test a new feature'
				);
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	/**
	 * test that addNew returns true on success
	 */
	function testAddNewFeatureReturnsTrueOnSuccess(){
		$result = $this->_initialiseCompleteFeature();
		$this->assertEquals(1,$result);
	}
	
	/**
	 * @todo refactor!!!
	 *
	 */
	function testAddNewFeatureThrowsExceptionOnNoUserId(){
		$data = $this->_fixtures['noUserIDFeature'];
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testAddNewFeatureReturnsIntegerOnSuccess(){
		$this->_featureFixtures->autoGen(2);
		$data = $this->_featureFixtures->get();
		$this->_feature->addNewFeature($data[0]);
		$this->assertEquals(2,$this->_feature->addNewFeature($data[1]));
	}
	
	function testViewFeatureById(){
		$this->_returnDataAndAddFeature();
		$result = $this->_feature->show(1);
		$this->assertNotNull($result);
	}
	
	/**
	 * Need to test that if we have duplicate data our function
	 * returns false.
	 * 
	*/
	function testAddNewFeatureDoesNotAllowDuplicateData() {
		$data = $this->_returnFeatureData();
		$result = $this->_feature->_featureExists($data[0]);
		$this->assertEquals(FALSE,$result);
	}
	
	function testFeatureExistReturnsTrueOnFeatureDuplication(){
		$data = $this->_returnDataAndAddFeature();
		$result = $this->_feature->_featureExists($data[0]);
		$this->assertEquals(True,$result);
	}
	
	function testUserIdThrowExceptionNull(){
		$id = null;
		$data = $this->_returnFeatureData();
		$this->setExpectedException('ErrorException');
		$this->_feature->updateFeature($id,$data[0]);
		
	}
	
	function testUpdateFeaturesReturnTrueOnSuccess(){
		$data = $this->_returnDataAndAddFeature();
		$data[0]['title'] = 'shitty ting';
		$result = $this->_feature->updateFeature(1,$data[0]);
		$this->assertTrue($result);
	}
	
	function testUpdateFeaturesReturnFalseOnFailure(){
		$data = $this->_returnDataAndAddFeature();
		$result = $this->_feature->updateFeature(1,$data[0]);
		$this->assertFalse($result);		
	}
	
	function testDeleteFeatureReturnTrueOnSuccess(){
		$this->_returnDataAndAddFeature();
		$result = $this->_feature->deleteFeature(1);
		$this->assertTrue($result);
	}
	
	function testDeleteFeatureReturnFalseOnFailure(){
		$result = $this->_feature->deleteFeature(2);
		$this->assertFalse($result);		
	}


	/*
	 * The field "addedbydate" is now to be added to the list
	 * of fields. New tests for this are appended below. Some of the
	 * tests above are relevant, but none failed so none of the above
	 * tests have been altered.
	 */
	function testParamFeatureAddedDateMissing() {
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($this->_getDummyData());
	}
	
	function testParamFeatureIfAddedDateIsNull() {
		$data = $this->_getDummyData();
		$data['addeddate'] = NULL;
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	function testParamFeatureIfAddedDateNotCorrectFormat() {
		$data = $this->_getDummyData();
		$data['addeddate'] = 'Mary had a little lamb';
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
 	
	function testAddedDateIsString() {
		$data = $this->_returnFeatureData();
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data);
	}
	
	/**
	 * When a record with addeddatet gets updated, the original
	 * value for addeddate should remain. That is, when the
	 * record gets updated, addeddate should retain its 
	 * original value.
	 * 
	 * This is difficult to test for as there needs to be a 
	 * measurable time difference between when the record
	 * is originally added, and when it is updated. If there
	 * is not then it is not possible to distinguish between
	 * the timestamp the record was originally added and the
	 * timestamp of it being updated. This is
	 * the reason for the "sleep" function being used.
	 *
	 */
	function testAddedDateNotModifiedOnUpdate() {
		
		$dateToday = date('Y-m-d');
		$data = $this->_returnDataAndAddFeature();
		$data[0]['title'] = 'New title Rui';
		sleep(1);
		$this->assertTrue($this->_feature->updateFeature(1,$data[0]));
		$showResult = $this->_feature->show(1);
		$this->assertNotNull($showResult);

		$this->markTestIncomplete('datetime field not showing what it  should be showing in the db fixtures');
		//$this->assertEquals($dateToday, $showResult->addeddate);
	}
	
	/*
	 * Beginning of Modified date test
	 *
	 */
	
	function testFeatureModDateIsAString() {
		$data = $this->_returnFeatureData();
		$this->assertType('string',$data[0]['moddate']);
	}
	
	function testParamFeatureModifiedDateMissing() {
		//$this->markTestSkipped('cant test atm.');
		$data = $this->_getTestData();
		$this->_feature->addNewFeature($data[0]);
		$new = $this->_featureFixtures->retrieveResults();
		unset($new[0]['moddate']);
		$this->setExpectedException('ErrorException');
		$this->_feature->updateFeature(1,$new[0]);
	}
	
	
	function testParamFeatureIfModifiedDateIsNull() {
		$data = $this->_returnFeatureData();
		$data[0]['moddate'] = NULL;
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data[0]);
	}
	
	
	
	function testParamFeatureIfModDateNotCorrectFormat() {
		$data = $this->_returnFeatureData();
		$data[0]['moddate'] = 'Mary had a big lamb';
		$this->setExpectedException('ErrorException');
		$this->_feature->addNewFeature($data[0]);
	}
	
	
	function testModDateIsModifiedOnUpdate() {
		
		$dateToday = date('Y-m-d');
		$data = $this->_returnDataAndAddFeature();
		$moddate = $data[0]['moddate'];
		$data[0]['title'] = 'New title Rui';
		sleep(1);
		$this->assertTrue($this->_feature->updateFeature(1,$data[0]));
		$showResult = $this->_feature->show(1);
		$this->assertNotNull($showResult);
		// @todo test need to be added is that the modified date has changed.
		$this->markTestIncomplete('datetime field not showing what it  should be showing in the db fixtures');
		//$this->assertEquals($dateToday, $showResult->addeddate);
	}
	
}