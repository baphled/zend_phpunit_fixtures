<?php
/**
 * @author Nadjaha (baphled) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 * 
 * Features fixtures class used to store and handle our test data.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FunctionFixture extends PHPUnit_Fixture {
	public $_table = 'functions';
	
	public $_fields = array(
					 'id' 	  		=> array('type' => 'integer', 'length'=>10, 'key' => 'primary'),
					 'userid' 		=> array('type' => 'integer', 'length'=>10, 'null', FALSE),
					 'title' 	   	=> array('type' =>'string', 'length' => 255, 'null', FALSE),
					 'description' 	=> array('type'=>'string','length'=>255, 'null', FALSE)
					  );
	
	public $_testData = array(
						array('id' => 1, 'userid' => 10,'title' => 'new function','description' => 'To test a new description'),
						array('id' => 2, 'userid' => 20,'title' => 'second function','description' => 'second description'),
						array('id' => 3, 'userid' => 30,'title' => 'another function','description' => 'another description')
						);
}
?>