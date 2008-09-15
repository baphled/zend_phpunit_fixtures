<?php
/**
 * @author Nadjaha (baphled) Wohedally 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 * 
 * FeatureFunction fixtures class used to store and handle our test data.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureFunctionFixture extends PHPUnit_Fixture {
	public $_table = 'feature2Function';
	
	public $_fields = array(
					 'id' => array('type' => 'integer', 'length'=>10),
					 'functionid' => array('type' => 'integer', 'length'=>10),
					  );
	
	public $_testData = array(
						array('id' => 1, 'functionid' => 10),
						array('id' => 2, 'functionid' => 20),
						array('id' => 3, 'functionid' => 30),
						array('id' => 1, 'functionid' => 40),
						array('id' => 2, 'functionid' => 70),
						array('id' => 3, 'functionid' => 90)
						);
}
?>