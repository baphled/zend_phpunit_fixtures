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
	protected $_name = 'feature2Function';
	protected $_primary = array('feature_id', 'function_id');

	function add($data){
		if(!is_array($data)){
			throw new ErrorException('Should be an integer.');
		}
		$params = array('feature_id', 'function_id');
		return CrudHandler::add($data,$params,$this);
	}
	
	function show($id){
		return $this->find($id[0], $id[1]); // too rigid. needs working on
	}
	
	function exists($data){
		$param = 'feature_id';
		return CrudHandler::exists($data,$param,$this);
	}
	
	function update($id, $data){
		return CrudHandler::update($id,$data,$this);
	}
	
	function delete($id){
		return CrudHandler::delete($id,$this);
	}
}
