<?php

/**
 * Funtions
 *  
 * @author Nadjaha Wohedally 2008
 * @version $Id$
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
		$params = array('title','description');
		return CrudHandler::add($data,$params,$this);
	}
	
	function viewFunction($id){
		return $this->find($id)->current();
	}
	
	function _functionExists($data){
		$param = 'title';
		return CrudHandler::exists($data,$param,$this);
	}
	
	function updateFunction($id, $data){
		return CrudHandler::update($id,$data,$this);		
	}
	
	function deleteFunction($id){
		return CrudHandler::delete($id,$this);
	}
	
}
