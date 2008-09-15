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

class FeatureFixture extends PHPUnit_Fixture {
	public $_table = 'features';
	
	public $_fields = array(
					 'id' => array('type' => 'integer', 'length'=>10, 'key' => 'primary'),
					 'userid' => array('type' => 'integer', 'length'=>10, 'null', FALSE),
				 	 'title' => array('type' =>'string', 'length' => 255, 'null', FALSE),
					 'description' => array('type'=>'string','length'=>255, 'null', FALSE)
					  );
	
	public $_testData = array(
						array('id' => 1, 'userid' => 1,'title' => 'new feature','description' => 'To test a new feature'),
						array('id' => 2, 'userid' => 23,'title' => 'anuva feature','description' => 'feature description'),
						array('id' => 3, 'userid'=> 13,'title' => 'second feature','description' => 'second feature')
						);
}
?>