<?php
/*
 * TestHelper
 *
 * Used to build our include paths linked into ZendFramework.
 * For each path added to the application we need to add its
 * path here, so that our test cases can assess them accordingly.
 *
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage TestSuite_TestHelper
 *
 * @todo Can make this a procedure call, which can loop through ZF, picking up each of the relvant directories
 *
*/

set_include_path('.' . PATH_SEPARATOR .
	dirname(__FILE__) .  PATH_SEPARATOR .
	dirname(__FILE__) . '/../fixtures/' . PATH_SEPARATOR .
	dirname(__FILE__) . '/../../application/default/models/' . PATH_SEPARATOR .
	dirname(__FILE__) . '/../../init/' . PATH_SEPARATOR .
	dirname(__FILE__) . '/../../library/' . PATH_SEPARATOR .
	dirname(__FILE__) . '/../../application/' . PATH_SEPARATOR .
		dirname(__FILE__) . '/../unit/' . PATH_SEPARATOR .
	get_include_path());
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();
