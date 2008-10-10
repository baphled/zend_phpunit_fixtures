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
		if (!is_array($data)) {
			throw new ErrorException('Must be an array');
		}
		
		
		if (!$this->_validateDateRFC822Format($data,'addeddate'))
		{
			throw new ErrorException('Must be a valid date format of YYYY-MM-DD');
		}
		
		if (!$this->_validateDateRFC822Format($data,'moddate'))
		{
			throw new ErrorException('Must be a valid date format of YYYY-MM-DD');
		}
		
		$params = array('userid','title','description','addeddate','moddate');
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
		if(!array_key_exists('moddate',$data)) {
			throw new ErrorException('Must supply an modification date.');
		}
		return CrudHandler::update($id,$data,$this);
	}
	
	function deleteFeature($id){
		return CrudHandler::delete($id,$this);
	}
	
	
	private function _validateDateRFC822Format($data, $property)
	{
		if (!array_key_exists($property, $data)) {
			throw new ErrorException('Key: addeddate does not exist in the array');
		}
		if (!is_string($data[$property])) {
			throw new ErrorException('The date should be a string type.');
		}

		if ( $isDate = Zend_Date::isDate($data[$property],'EEE, dd MMM yy hh:mm:ss Z') ) {
			return true;
		}
		
		
		
		else {
			return false;
		}
	}
	
	
	
}
