<?php
/**
 * @author Nadjaha (baphled) Wohedally 2008
 * @version $Id: Features.php 41 2008-09-09 15:48:50Z nadjaha $
 * @copyright ibetX Ltd2008
 * @package
 * 
 * FeatureFunction fixtures class used to store and handle our test data.
 * 
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class FeatureFunctionFixture extends PHPUnit_Fixture {
	public $_table = 'featureFunction';
	
	public $_fields = array(
					 'featureid' => array('type' => 'integer', 'length'=>10, 'key' => 'primary'),
					 'functionid' => array('type' => 'integer', 'length'=>10),
					  );
	
	public $_testData = array(
						array('featureid' => 1, 'functionid' => 10),
						array('featureid' => 2, 'functionid' => 20),
						array('featureid' => 3, 'functionid' => 30)
						);
}
?>