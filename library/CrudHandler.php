<?php

/**
 * CrudHandler, handles the creation, removal,updating
 * and deletion of our data.
 *  
 * @author Yomi (baphled) Akindayini
 * @version $Id$
 * @copyright 2008
 * @package IntraBetxTestSuite
 */

class CrudHandler {
	
	/**
     * Add our data.
     * 
     * @access static
     * @param $data the data we want to add.
     * @param $params the params we want to check have values.
     * @param $odj the object we want to submit to
     * @return int
     * 
     */
	static function add($data,$params,$obj){
		if(!is_array($params)){
			throw new ErrorException('Parameter must be an array');
		}
		self::_parseParameteres($params,$data);
		return $obj->insert($data);
	}

	/**
	 * Parses through parameters, determining whethers
	 * the values are valid and present.
	 * 
	 * @access private
	 * @param $params The parameters we want to parse.
	 * @param $data The data we want to validate against
	 * 
	 */
	private function _parseParameteres($params,$data) {
	   foreach($params as $param) {
            if(array_key_exists($param, $data)) {
                if(null === $data[$param] || empty($data[$param])){
                    throw new ErrorException($param .' must not be empty');
                }
            }
            else {      // @todo create exception test for functionality
                throw new ErrorException('Parameter:' .$param .' does not exist.');
            }
        }
	}
	/**
	 * Update our data.
	 * 
	 * @access static
	 * @param $id our datas id
	 * @param $data our our updated data.
	 * @param $odj the object we want to do the update on
	 * @return bool
	 * 
	 */
	static function update($id, $data,$obj){
		if(null === $id || empty($id)){
			throw new ErrorException('Id must not be empty');			
		}	
		$where 	= $obj->getAdapter()->quoteInto('id = ?', $id);
		$result = $obj->update($data, $where); 
		return $result ? true : false;		
	}
	
	/**
     * Deletes our data.
     * 
     * @access static
     * @param $id our datas id
     * @param $odj the object we want to do the delete.
     * @return bool
     * 
     */
	static function delete($id,$obj){
		$where 	= $obj->getAdapter()->quoteInto('id = ?',$id);
		$result = $obj->delete($where);
		return $result ? true : false;		
	}
	
	/**
     * Checks to see if out data exists
     * 
     * @access static
     * @param $data the data we want to check.
     * @param $odj the object we want to check.
     * @return bool
     * 
     */
	static function exists($data,$obj) {
		$result = $obj->fetchAll($obj->select()->where('title = ?', $data['title']));
		return (count($result)) ? true : false;
	}
	
	
}
