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
	}
	
	public function indexAction() {
		$table = new Features();
		$this->view->features = $table->fetchAll();
	}
}
?>

