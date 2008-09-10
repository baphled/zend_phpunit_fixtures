<?php

/**
 * CrudHandler
 *  
 * @author ibetxadmin
 * @version 
 */

class CrudHandler {
	
	static function add($data,$params,$obj){
		if(!is_array($params)){
			throw new ErrorException('Parameter must be an array');
		}
		foreach($params as $param) {
			if(array_key_exists($param, $data)) {
				if(null === $data[$param] || empty($data[$param])){
					throw new ErrorException('title must not be empty');
				}
			}
			else {		// @todo create exception test for functionality
				throw new ErrorException('Parameter:' .$param .' does not exist.');
			}
		}
		return $obj->insert($data);
	}

	static function update($id, $data,$obj){
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}	
		$where 	= $obj->getAdapter()->quoteInto('id = ?', $id);
		$result = $obj->update($data, $where); 
		return $result ? true : false;		
	}
	
	static function delete($id,$obj){
		$where 	= $obj->getAdapter()->quoteInto('id = ?',$id);
		$result = $obj->delete($where);
		return $result ? true : false;		
	}
	
	static function exists($data,$obj) {
		$result = $obj->fetchAll($obj->select()->where('title = ?', $data['title']));
		return (count($result)) ? true : false;
	}
	
	
}
