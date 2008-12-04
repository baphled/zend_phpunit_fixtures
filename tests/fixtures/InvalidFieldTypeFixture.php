<?php
/**
 * InvalidFixture
 * 
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 */
class InvalidFieldTypeFixture extends PHPUnit_Fixture_DB {
	public $_table = 'snooker';
	
	public $_fields  = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'parent_id' => array('typed' => 'integer', 'length' => 10, 'null' => true),
            'model' => array('type' => 'strong', 'length' => 255, 'default' => ''),
            'alias' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'lft' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'rght' => array('type' => 'integer', 'length' => 10, 'null' => true)
        );
}