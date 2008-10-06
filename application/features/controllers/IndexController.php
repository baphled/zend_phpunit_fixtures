<?php

/**
 * IndexController
 * 
 * @author Ekerete Akpan
 * @version $Id: IndexController.php 162 2008-09-15 08:39:28Z yomi $
 */

require_once 'Zend/Controller/Action.php';

class Features_IndexController extends Zend_Controller_Action {

	public function init() {
		parent::init();
		$this->_table = new Features();
	}
	
	public function indexAction() {
		$this->view->features = $this->_table->fetchAll();
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
				
				$this->view->features = $this->_table->addNewFeature($clean);
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
				'desc' => 'StripTags'
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
				
				$this->view->features = $this->_table->updateFeature($id, $data);				
				$this->getHelper('redirector')->goto('index');
			}
		}
		$this->view->features = $this->_table->show($id);
	}	
	
	public function deleteAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id');
		$this->view->features = $this->_table->deleteFeature($id);		
		$this->getHelper('redirector')->goto('index');
	}
}

