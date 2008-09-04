<?php

/**
 * featureModel
 *  
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package
 *
 */

require_once 'Zend/Db/Table/Abstract.php';

class FeatureModel extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'features';

}
