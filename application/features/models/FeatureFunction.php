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

	function add($data){
		if(!is_array($data)){
			throw new ErrorException('Should be an integer.');
		}
		$params = array('title','id');
		return CrudHandler::add($data,$params,$this);
	}
	
	function show($id){
		return $this->find($id);
	}
	
	function Exists($data){
		return CrudHandler::exists($data,$this);
	}
	
	function updateFeatureFunction($id, $data){
		return CrudHandler::update($id,$data,$this);
	}
	
	function deleteFeatureFunction($id){
		return CrudHandler::delete($id,$this);
	}
}
