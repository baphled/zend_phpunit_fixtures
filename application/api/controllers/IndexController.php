<?php

/**
 * IndexController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Api_IndexController extends Zend_Controller_Action {
	
	private $_url;
	private $_auth;
	
	public function init() {
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_url = 'http://192.168.0.98/usersapi/';
		$this->_auth = '?auth=1234';
		parent::init();
	}
	
	public function indexAction() {
		$xml =  $this->_apiCall();
		//echo '<pre>';
		
		//echo $xml->email;
		//var_dump($xml);
		//echo $xml->asXML();
		//echo '</pre>';
		
		echo $xml;
	}
	
	private function _apiCall($link = 'users', $params = array(), $method = 'get') {
		$api = new Zend_Rest_Client($this->_url . $link . '.xml/' . $this->_auth);
		if (!empty($params)) {
			
		}
		return $api->$method();
	}

}
?>

