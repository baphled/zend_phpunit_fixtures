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
		$db 	= Zend_Registry::get('db');
		$select = $this->select()
					    ->from('functions',array('id'))
					    ->where('id = ?', $id);
		$query 	= $db->query($select); 
		$result = $query->fetchAll();
		return $result;
	}
	
	function functionExists($data){
		$db 	= Zend_Registry::get('db');
		$select = $this->select()
					   ->from('functions', array('title'))
					   ->where('title = ?', $data['title']);
		$query 	= $db->query($select);
		$result = $query->fetchAll();
		if(count($result) > 0){
			return true;
		}
		return false;
	}
	
	function updateFunction($id, $data){
		$db 	 = Zend_Registry::get('db');
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}	
		
		$where[] = "id = $id";
		$result  = $db->update('functions', $data, $where); 
		if($result){
			return true;
		}
		return false;		
	}
	
	function deleteFunction($id){
		$db = Zend_Registry::get('db');
		$where[] = "id = $id";
		$result = $db->delete('functions',$where);
		if($result){
			return true;		
		}
		return false;
	}
	
}
