<?php
/**
 * PHPUnit_Fixture_Transactions
 * 
 * Deals with setting up an truncating our transaction
 * based fixture data. Here we can deal with the actual
 * setting up and cleaning of our test data and its tables.
 * 
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id: DB.php 258 2008-10-31 13:37:10Z yomi $
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage PHPUnit_Fixture_Transactions
 * 
 * @TODO Finish implementing retrieving our list of SQL schemas.
 * 
 */
class PHPUnit_Fixture_DynamicDB extends PHPUnit_Fixture {
	
	/**
     * Our fixture manager, used to handle 
     * the meat of fixture interactions.
     *
     * @access private
     * @var FixtureManager
     * 
     */
    protected $_fixMan;
    
    protected $_config;
    
    private $_schemas;
    
	public function __construct() {
		$this->_schemas = array();
		$this->_fixMan = new FixturesManager();
		TestConfigSettings::setUpConfig();
		$this->_config = Zend_Registry::get('config');
	}
	
	/**
     * Drops all test data DB's when object is destructed.
     * Used to keep our DB in its original format.
     * 
     * @access public
     * 
     */
    public function __destruct() {
    	try {
	        if ($this->_fixMan->tablesPresent()) {
	        	$this->_fixMan->truncateTable();
	        }
	        $this->_fixMan = null;
    	}
    	catch(Exception $e) {
    		echo $e->getMessage();
    	}
    }
    
    /**
     * Used to call our CRUD methods
     * 
     * @access  private
     * @param   String    $call   The call we want to make.
     * @return  bool
     * 
     */
    protected function _callMethod($call) {
        try {
            $result = $this->_fixMan->fixtureMethodCheck($call, $this);
        }
        catch(Exception $e) {
        	$result = false;
            $e->getMessage();
        }
        return $result;
    }
    
    /**
     * Wrapper function for truncating our test tables.
     * 
     * @access public
     * @return bool
     * 
     */
    public function truncate() {
        return $this->_callMethod('truncate');
    }
    
    /**
     * Gets our URI, which we'll need to retrieve our SQL schema
     * 
     * Determines whether a URL has been passed, if
     * it hasn't we will use the one within the configuration file.
     *
     * @access private
     * @param 	String $url
     * @return 	String $uri
     * 
     */
    private function _getURI($url='') {
    	if (empty($url)) {
    		if (!isset($this->_config->schema->url) || empty($this->_config->schema->url)) {
    			throw new Zend_Exception('Must submit a URL, via param or schema.url');    			
    		}
    		$url = $this->_config->schema->url;
    	}
    	$uri = Zend_Uri::factory($url);
    	if ('http' !== $uri->getScheme()) {
    		throw new Zend_Exception('URL must have a HTTP prefix.');
    	}
    	return $uri;
    }

    /**
     * Gets our HTML for us.
     *
     * @access private
     * @param Zend_Response $response
     * @return String
     */
    private function _getHTML($response) {
    	if(200 === $response->getStatus()) {
    		$doc = Zend_Search_Lucene_Document_Html::loadHTML($response->getBody());
    		return $doc->getHTML();
    	}
    	return false;
    }
    
    /**
     * Gets our HTTP response, if an error occurs we throw and exception
     *
     * @access private
     * @param String $uri
     * @return Zend_Http_Response
     * 
     */
    private function _getResponse($uri) {
    	try {
	    	if (isset($uri) && $uri->valid()) {
	    		$client = new Zend_Http_Client($uri, array('maxredirects'=>0, 'timeout'=>15));
	    		$response = $client->request();
	    	}
    	}
    	catch(Zend_Http_Client_Adapter_Exception $e) {
    		throw new Zend_Exception($e->getMessage());
    	}
    	return $response;
    }
    
    /**
     * Retrieves our SQL schema for us.
     *
     * @access 	public
     * @param 	String $url
     * @return 	bool			False on failure.
     * 
     */
    public function retrieveSQLSchema($url='') {
    	$stmts = array();
    	$data = null;
    	$uri = $this->_getURI($url);
    	$response = $this->_getResponse($uri);
    	$body = $this->_getHTML($response);
    	if (false !== $body) {
    		preg_match_all("|<pre>(.*)<[^>]pre>|i", $body, $data, PREG_PATTERN_ORDER);
    		$schemas = $data[1];
    		if (0 === count($schemas)) {
    			throw new Zend_Exception('No Schemas found.');
    		}
    		foreach ($schemas as $query) {
    			if (!eregi('^CREATE', $query)) {
    				throw new Zend_Exception('Seems like we have a non SQL query in our results'); 
    			}
    			$stmts[] = str_replace("<br>", ' ', $query);
    		}
    		$this->_schemas = $stmts;
    		return true;
    	}
    	return false;
    }
    
    /**
     * Gets our SQL Schemas from our Workbench.
     *
     * @access public
     * @return Array
     * 
     */
    public function getSchemas() {
    	if (0 === count($this->_schemas)) {
    		$this->retrieveSQLSchema();
    	}
    	return $this->_schemas;
    }
    
    public function findSchema($name) {
    	return '';
    }
}