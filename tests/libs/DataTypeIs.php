<?php
/**
 * DataTypeIs
 * 
 * Basically checks Data Types and sets them accordingly. 
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id$
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 *
 */
class DataTypeIs {
	/**
     * Checks that our data type is an integer, by default id's
     * are set to null, this is so that our db can create our
     * ID for us on insertion.
     *
     * @access 	public
     * @param 	String 			$dataType	The fixtures type
     * @param 	int 			$field		The field we want to check.
     * @param 	PHPUnit_Fixture $obj		The object of which we want to assign to.
     * 
     */
    static function anInt($dataType, $field, PHPUnit_Fixture $obj) {
       if ('integer' === $dataType) {
            if ('id' !== $field) {
                $obj->setResult($field, rand());
            } else {
            	$obj->setResult($field, NULL);
            }
       }
    }

    /**
     * Checks that our a string, if so we generate test data.
     *
     * @access 	public
     * @param 	String 			$dataType	The fixtures type
     * @param 	int 			$field		The field we want to check.
     * @param 	PHPUnit_Fixture $obj		The object of which we want to assign to.
     * 
     */
    static function aString($dataType, $field, $obj) {
       if ('string' === $dataType) {
           $obj->setResult($field, 'my string');
       }
    }

    /**
     * Checks to see if our data type is a date, if it is,
     * we generate the current date.
     *
     * @access 	public
     * @param 	String 			$dataType	The fixtures type
     * @param 	int 			$field		The field we want to check.
     * @param 	PHPUnit_Fixture $obj		The object of which we want to assign to.
     * 
     */
    static function aDate($dataType, $field, $obj) {
       if ('date' === $dataType) {
            $obj->setResult($field, date('Y-m-d'));
       }
    }

    /**
     * Checks to see if we have a datetype type, if we do
     * we generate the current date & time.
     *
     * @access 	public
     * @param 	String 			$dataType	The fixtures type
     * @param 	int 			$field		The field we want to check.
     * @param 	PHPUnit_Fixture $obj		The object of which we want to assign to.
     * 
     */
    static function aDateTime($dateType, $field, $obj) {
       if ('datetime' === $dateType) {
            $obj->setResult($field, date(DATE_RFC822));
       }    
    }
}