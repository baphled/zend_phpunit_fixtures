<?php

/**
 * FeatureFunction
 *  
 * @author  Nadjaha Wohedally
 * @version $Id$
 */

require_once 'Zend/Db/Table/Abstract.php';

class FeatureFunction extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'feature2function';
	protected $_primary = array('feature_id', 'function_id');

	
}
