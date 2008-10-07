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
	
	/**
	 * @var String $_name Name of table 
	 */
	protected $_name = 'features';
	
	/**
	 * Adds new feature to features list
	 *
	 * @param Array $data
	 * @return bool	True on success, false on failure
	 * 
	 * @todo add user_id into functionality, once userapi is complete.
	 * 
	 */
	function addNewFeature($data){
		if(!is_array($data)){
			throw new ErrorException('Must be an array');
		}
		$params = array('userid','title','description','addeddate');
		return CrudHandler::add($data,$params,$this);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id
	 */
	function show($id){		
		return $this->find($id)->current();
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
