<?php
/**
 * DbBasedTestFixture
 * 
 * Test Fixture layout, will be used to determine the functionality
 * and integrity of the fixturemanager and fixture class.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage TestSuite_Fixtures
 *
 * $LastChangedBy$
 * Date: 01/09/2008
 * Created to test our fixture(Manager) classes
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();

class DbTestFixture extends PHPUnit_Fixture_DB {
	protected $_table = 'apples';
	
	protected $_fields = array(
            'id' => array('type' => 'integer', 'length' => 10, 'key' => 'primary'),
            'apple_id' => array('type' => 'integer', 'length' => 10, 'null' => true),
            'color' => array('type' => 'string', 'length' => 255, 'default' => 'green'),
            'name' => array('type' => 'string', 'length' => 255, 'default' => ''),
            'created' => array('type' => 'datetime', 'null' => FALSE),
            'date' => array('type' => 'date', 'null' => FALSE),
            'modified' => array('type' => 'datetime', 'null' => FALSE)
        );
        
	protected $_fixtures = array(
           array('ALIAS'=>'first', 'id' => 1, 'apple_id' => 2, 'color' => 'Red 1', 'name' => 'Red Apple 1', 'created' => '2006-11-22 10:38:58', 'date' => '1951-01-04', 'modified' => '2006-12-01 13:31:26'),
           array('id' => 2, 'apple_id' => 1, 'color' => 'Bright Red 1', 'name' => 'Bright Red Apple', 'created' => '2006-11-22 10:43:13', 'date' => '2014-01-01', 'modified' => '2006-11-30 18:38:10'),
           array('id' => 3, 'apple_id' => 2, 'color' => 'blue green', 'name' => 'green blue', 'created' => '2006-12-25 05:13:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:24'),
           array('id' => 4, 'apple_id' => 2, 'color' => 'Blue Green', 'name' => 'Test Name', 'created' => '2006-12-25 05:23:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:36'),
           array('id' => 5, 'apple_id' => 7, 'color' => 'Green', 'name' => 'Blue Green', 'created' => '2006-12-25 05:24:06', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:16'),
           array('id' => 6, 'apple_id' => 6, 'color' => 'My new appleOrange', 'name' => 'My new apple', 'created' => '2006-12-25 05:29:39', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:39'),
           array('id' => 7, 'apple_id' => 8, 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21')
       );
}