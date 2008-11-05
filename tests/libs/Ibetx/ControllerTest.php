<?php
/**
 * 
 * ControllerTest instantiate the Ibetx Api Client
 * 
 * @author Nadjaha Wohedally 2008
 * @version $Id$
 * @copyright iBetX Ltd 2008
 * @package IX9
 * @subpackage IX9_Front_Account
 *
 */

class Ibetx_ControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	private $_client;
	
	/**
	 * instantiate the Api Client
	 *
	 */
	public function __construct() {
		$this->_client = new Ibetx_Api_Client(); 
	}
}
