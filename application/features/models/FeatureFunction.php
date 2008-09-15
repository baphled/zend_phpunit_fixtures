<?php

/**
 * FeatureFunction
 *  
 * @author  Nadjaha Wohedally
 * @version $Id$
 */

require_once 'Zend/Db/Table/Abstract.php';

class FeatureFunction extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'feature2function';
	protected $_primary = array('feature_id', 'function_id');
	
	function add($data){
		if(!is_array($data)){
			throw new ErrorException('Must be an array');
		}
		$params = array('feature_id','function_id');
		return CrudHandler::add($data,$params,$this);
	}
	
	function updateRecord($function_id, $data) {
		return CrudHandler::update($function_id, $data, $this, 'function_id');
	}
	
	function deleteRecord($id, $field) {
		return CrudHandler::delete($id,$this, $field);
	}
	
	function findByField($field, $value) {
		$select = $this->select()->where("$field = ?", $value);
		return $this->fetchRow($select);
	}
	
}
