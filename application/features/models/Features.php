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

class Features extends FeatureModel {
	
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
	
	function viewFeature($id){
		$db = Zend_Registry::get('db');
		$select = $this->select()
					    ->from('features',array('id'))
					    ->where('id = ?', $id);
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();
		if($result){
			return $result;
		}
		else{
			return false;
		}
	}
	
	function _featureExists($feature){
		$db = Zend_Registry::get('db');
		$select = $this->select()
					    ->from('features',array('title'))
					    ->where('title = ?', $feature['title']);
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();
		if(count($result)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function updateFeature($id, $data){
		$db = Zend_Registry::get('db');

		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}	
			
		$where[] = "id = $id";
		$result = $db->update('features',$data,$where);
		if($result) {
			return true;		
		}
		else {
			return false;
		}
	}
	
	function deleteFeature($id){
		$db = Zend_Registry::get('db');
		$where[] = "id = $id";
		$result = $db->delete('features',$where);
		if($result){
			return true;		
		}
		return false;
	}
	
}
