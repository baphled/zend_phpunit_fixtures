<?php

/**
 * Features
 *  
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 *  
 */

class Features extends Zend_Db_Table_Abstract {
	
	protected $_name = 'features';
	
	function addNewFeature($data){
		if(!is_array($data)){
			throw new ErrorException('Must be an array');
		}
		$params = array('title','userid');
		return CrudHandler::add($data,$params,$this);
	}
	
	function show($id){		
		return $this->find($id);
	}
	
	function _featureExists($feature){
		$param = 'title';
		return CrudHandler::exists($feature,$param,$this);
	}
	
	function updateFeature($id, $data){
		return CrudHandler::update($id,$data,$this);
	}
	
	function deleteFeature($id){
		return CrudHandler::delete($id,$this);
	}
	
}
