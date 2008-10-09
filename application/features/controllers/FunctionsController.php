<?php

/**
 * FunctionController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Features_FunctionsController extends Zend_Controller_Action {
	
	public function init() {
		parent::init();
		$this->_helper->layout->setLayout('features');
		$this->_table = new Functions();
	}

	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->view->functions = $this->_table->fetchAll();
	}
	
	
	public function addAction() {
		$request = $this->getRequest();
		if($request->isPost())	{
			$filters = array(
				'title' => 'StripTags',
				'desc' => 'StripTags'
			);
			
			$validation = array(
				'title' => array(),
				'desc' => array()
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if($zfi->isValid()) {				
				$clean = array();
				$clean['title'] = $zfi->title;
				$clean['description'] = $zfi->desc;
				
				$this->view->functions = $this->_table->addNewFunction($clean);
				$this->getHelper('redirector')->goto('index');			
			}
		}
	}	
	
	
	
	public function editAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id');
		
		if(null === $id) {
			$this->getHelper('redirector')->goto('index');
		}				

		if($request->isPost())	{
			$filters = array(
				'title' => 'StripTags',
			);
			
			$validation = array(
				'title' => array(),
				'desc' => array()
			);
			
			$zfi = new Zend_Filter_Input($filters, $validation, $request->getPost());
			
			if($zfi->isValid()) {
				$data = array();
				$data['title'] 		 = $zfi->title;
				$data['description'] = $zfi->desc;
				
				$this->view->functions = $this->_table->updateFunction($id, $data);				
				$this->getHelper('redirector')->goto('index');
			}
		}		
		$this->view->functions = $this->_table->viewFunction($id);		
	}

	public function deleteAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$this->view->functions = $this->_table->deleteFunction($id);		
		$this->getHelper('redirector')->goto('index');		
	}	
}





