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
					 'feature_id' => array('type' => 'integer', 'length'=>10),
					 'function_id' => array('type' => 'integer', 'length'=>10),
					  );
	
	public $_testData = array(
						array('feature_id' => 1, 'function_id' => 10),
						array('feature_id' => 2, 'function_id' => 20),
						array('feature_id' => 3, 'function_id' => 30),
						array('feature_id' => 1, 'function_id' => 40),
						array('feature_id' => 2, 'function_id' => 70),
						array('feature_id' => 3, 'function_id' => 90)
						);
}
?>