<?php

/**
 * Features
 *  
 * @author Nadjaha (ibetxadmin) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 *  
 */

require_once 'Zend/Db/Table/Abstract.php';

class Features extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'features';
	
	function addNewFeature($newFeature){		
		if(!is_array($newFeature)) {
			throw new ErrorException('Must be a valid array');
		}
		if(null === $newFeature['title']){
			throw new ErrorException('Feature must have a title.');
		}
		else{
			return (int)$this->insert($newFeature);
		}
	}
	
	function _featureExists($feature) {
		$db = Zend_Registry::get('db');
		$select = $db->select()
					->from('features',array('title'))
					->where('title = ?',$feature['title']);
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();
		if(count($result)) {
			return true;		
		}
		else {
			return false;
		}
	}
	
	function listFeatures(){
		$result = $this->fetchAll()->toArray();
		if(0 === count($result)){
			throw new ErrorException('Feature list empty.');			
		}
		return $result;
	}
	
	function viewFeatureById($id){
		$result = $this->find($id);
		return $result->current()->toArray();
	}

}
