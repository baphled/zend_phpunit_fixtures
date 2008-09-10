<?php

/**
 * Funtions
 *  
 * @author Nadjaha (baphled) Wohedally 2008
 * @version $Id: Features.php 107 2008-09-04 14:26:23Z nadjaha $
 * @copyright ibetX Ltd2008
 * @package
 *  
 */

class Functions extends Zend_Db_Table_Abstract {
	protected $_name = 'functions';

	function addNewFunction($data){
		if (!is_array($data)){
			throw new ErrorException('Must be an array');
		}
		$params = array('title','id');
		return CrudHandler::add($data,$params,$this);
	}
	
	function viewFunction($id){
		return $this->find($id);
	}
	
	function _functionExists($data){
		return CrudHandler::exists($data,$this);
	}
	
	function updateFunction($id, $data){
		return CrudHandler::update($id,$data,$this);		
	}
	
	function deleteFunction($id){
		return CrudHandler::delete($id,$this);
	}
	
}
