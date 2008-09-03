<?php
/**
 * InvalidFixture
 * 
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package
 *
 */
class InvalidFieldTypeFixture extends PHPUnit_Fixture {
	public $_table = 'snooker';
	
	public $_fields = array(
	        'id' => array('type' => 'integer', 'length' => 10, 'key' => 'primary'),
            'apple_id' => array('tipe' => 'integer', 'length' => 10, 'null' => true)
	       );
}