<?php

/**
 * IndexController
 * 
 * @author Ekerete Akpan
 * @version $Id: IndexController.php 162 2008-09-15 08:39:28Z yomi $
 */

require_once 'Zend/Controller/Action.php';

class Features_IndexController extends Zend_Controller_Action {
	
	protected $_flashMessenger;
	
	public function init() {
		parent::init();
		$this->_table = new Features();
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		if($this->_flashMessenger->hasMessages()){
			$this->view->messages = $this->_flashMessenger->getMessages();
		}
	}
	
	public function indexAction() {
		$this->view->features = $this->_table->fetchAll();
	}
	
	public function editAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id');
		if(null === $id) {
			$this->_flashMessenger->addMessage('We need the id of the page');
			$this->getHelper('redirector')->goto('index');
		}		
		$this->view->features = $this->_table->show($id);
	}	
	
	public function deleteAction() {
		
	}
}

