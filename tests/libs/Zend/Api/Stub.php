<?php
/**
 * ExceptionHandlerTest
 *  
 * @author Yomi Colledge <yomi@ibetx.com>
 * @version $Id
 * @copyright 2009
 * @package Test_IX9
 * @subpackage Test_ZendApiStub
 * 
 */

class Zend_Api_Stub extends Ibetx_Api_Client {
	
	private $_head;
	private $_fixture;
	/**
	 * Basic constructor
	 *
	 */
	function __construct($adapter) {
		parent::__construct();
		$this->_adapter = $adapter;
		$this->_http = new Zend_Http_Client($this->_apiUrl,
			array('adapter' => $this->_adapter)
		);
		$this->_head = "HTTP/1.1 200 Found" ."\r\n" .
						"Content-Type: text/xml" . "\r\n" .
		                "\r\n";
		$this->_fixture = new ApiResponseFixture();
	}
	
	/**
	 * Sets up the response we want to stub out.
	 *
	 * @param unknown_type $response
	 */
	function setResponse($response) {
		$this->_adapter->setResponse($this->_head .$response);
	}
	
	/**
	 * php magic __call method. Resolves the API method name and arguments
	 *
	 * @param 	string 	$method 	Method name
     * @param 	array 	$args 		Method args
     * @return 	array
	 */
	public function __call($method, $params) {
		$requestParameters = $this->_processor->setRequestParameters($method, $params);
		$data = $this->_fixture->find($requestParameters);
		$this->setResponse($data[0]);
		return parent::__call($method, $params);
	}
}