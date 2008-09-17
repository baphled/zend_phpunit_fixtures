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
	}

	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$table = new Functions();
		$this->view->functions = $table->fetchAll();
	}
	
	public function editAction() {
		
	}

	public function deleteAction() {
		
	}
	
}
?>

