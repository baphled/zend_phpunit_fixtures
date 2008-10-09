<?php
/**
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 * 
 * Features fixtures class used to store and handle our test data.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureFixture extends PHPUnit_Fixture_DB {
	protected $_table = 'features';
	
	protected $_fields = array(
					 'id' => array('type' => 'integer', 'length'=>10, 'key' => 'primary'),
					 'userid' => array('type' => 'integer', 'length'=>10, 'null', FALSE),
				 	 'title' => array('type' =>'string', 'length' => 255, 'null', FALSE),
					 'description' => array('type'=>'string','length'=>255, 'null', FALSE),
					 'addeddate' => array('type'=>'date', 'null'=> FALSE)
	);
}