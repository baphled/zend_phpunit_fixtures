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
		if(null === $data['title'] || '' === $data['title']){
			throw new ErrorException('Title must not be empty');
		}
		if(null === $data['id'] || '' === $data['id']){
			throw new ErrorException('User Id must not be empty');
		}
		/*
		 * check if data sent to the table is inserted successfully
		 * returns the last id that has been inserted
		 * we are checking it against id inserted which is 1
		 */
		return $this->insert($data);
	}
	
	function viewFunction($id){
		return $this->find($id);
	}
	
	function _functionExists($data){
		$result = $this->fetchAll($this->select()->where('title = ?', $data['title']));
		return (count($result)) ? true : false;
	}
	
	function updateFunction($id, $data){
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}	
		
		$where 	= $this->getAdapter()->quoteInto('id = ?', $id);
		$result = $this->update($data, $where); 
		return $result ? true : false;		
	}
	
	function deleteFunction($id){
		$where 	= $this->getAdapter()->quoteInto('id = ?',$id);
		$result = $this->delete($where);
		return $result ? true : false;		
	}	
}
