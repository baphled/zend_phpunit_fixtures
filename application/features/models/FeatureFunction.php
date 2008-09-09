<?php

/**
 * FeatureFunction
 *  
 * @author ibetxadmin
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class FeatureFunction extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'featureFunction';

	function addNewFeatureFunction($data){
		if(!is_array($data)){
			throw new ErrorException('Should be an integer.');
		}
		return $this->insert($data);
	}
	
	function show($id){
		return $this->find($id);
	}
	
	function _featureFunctionExists($data){
		$result = $this->fetchAll($this->select()->where('functionid = ?', $data['functionid']));
		return (count($result)) ? true: false;
	}
	
	function updateFeatureFunction($id, $data){
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}		
		
		$where = $this->getAdapter()->quoteInto('featureid = ?', $id);
		$result = $this->update($data, $where);
		return $result ? true : false;
	}
	
	function deleteFeatureFunction($id){
		$where = $this->getAdapter()->quoteInto('featureid = ?', $id);
		$result = $this->delete($where);
		return $result ? true : false;
	}
}
