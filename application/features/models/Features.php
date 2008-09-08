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
		if(null === $data['title'] || empty($data['title'])){
			throw new ErrorException('title must not be empty');			
		}
		if(null === $data['userid'] || empty($data['userid'])){
			throw new ErrorException('User id must not be empty');			
		}		
		return $this->insert($data);
	}
	
	function show($id){		
		return $this->find($id);
	}
	
	function _featureExists($feature){
		$result = $this->fetchAll($this->select()->where('title = ?', $feature['title']));
		return (count($result)) ? true : false;
	}
	
	function updateFeature($id, $data){
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}		
		
		$where = $this->getAdapter()->quoteInto('id = ?', $id);
		$result = $this->update($data, $where);
		return $result ? true : false;
	}
	
	function deleteFeature($id){
		$where = $this->getAdapter()->quoteInto('id = ?', $id);
		$result = $this->delete($where);
		return $result ? true : false;
	}
	
}
