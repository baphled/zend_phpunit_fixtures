<?php
/**
 * PHPUnit_Fixture_DynamicDB
 * 
 * Deals with setting up an truncating our transaction
 * based fixture data. Here we can deal with the actual
 * setting up and cleaning of our test data and its tables.
 * 
 * @author Yomi (baphled) Colledge <yomi@boodah.net> 2008
 * @version $Id: DB.php 258 2008-10-31 13:37:10Z yomi $
 * @copyright 2008
 * @package Zend_PHPUnit_Scaffolding
 * @subpackage PHPUnit_Fixture_Transactions
 * 
 */
abstract class PHPUnit_Fixture_DynamicDB extends PHPUnit_Fixture {
	
	/**
     * Our fixture manager, used to handle the meat of fixture interactions.
     *
     * @access private
     * @var FixtureManager
     * 
     */
    protected $_fixMan = null;
    
    /**
     * Stores the fixtures table name
     *
     * @access protected
     * @var String
     * 
     */
    protected $_table = null;
    
    /**
     * Stores our configurations
     *
     * @access protected
     * @var Zend_Config
     * 
     */
    protected $_general;
    
    /**
     * Array of SQL schemas.
     * 
     * As this property will only be used within these objects we make it private.
     * 
     * @access private
     * @var Array
     * 
     */
    private $_schemas;
    
    /**
     * Constructs our Dynamic DB settings, basically sets up our configurations
     * & constructs our parents functionality.
     *
     * @access public
     * 
     */
	public function __construct($env=null) {
		parent::__construct();
		$this->_schemas = array();
		if (null !== $env) {
			$this->_fixMan = new FixturesManager($env);
		} else {
			$this->_fixMan = new FixturesManager();
		}
		Zend_ConfigSettings::setUpConfig();
		$this->_general = Zend_Registry::get('general');
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
     * Used to call our CRUD methods, which we use to control our DB state (clean, truncate, delete, etc).
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
     * Gets our URI, which we'll need to retrieve our SQL schema.
     * At the moment it is setup to work with MySQL Workbench, will
     * refactor once other source become available.
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
    		if (!isset($this->_general->schema->url) || empty($this->_general->schema->url)) {
    			throw new Zend_Exception('Must submit a URL, via param or schema.url');    			
    		}
    		$url = $this->_general->schema->url;
    	}
    	$uri = Zend_Uri::factory($url);
    	if ('http' !== $uri->getScheme()) {
    		throw new Zend_Exception('URL must have a HTTP prefix.');
    	}
    	return $uri;
    }

    /**
     * Gets our HTML from MySQL Workbench for us.
     *
     * @access private
     * @param Zend_Response $response
     * @return String
     * 
     */
    private function _getHTMLResponse($response) {
    	if (200 === $response->getStatus()) {
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
    private function _requestResponse($uri) {
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
    
    private function _checkSchemaList() {
    	if (0 === count($this->_schemas)) {
    		$this->retrieveSQLSchema();
    	}
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
    	$response = $this->_requestResponse($uri);
    	$body = $this->_getHTMLResponse($response);
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
    			$query = strip_tags($query);
    			$stmts[] = str_replace('ndbclusterCOMMENT', 'ndbcluster COMMENT', $query);
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
    	$this->_checkSchemaList();
    	return $this->_schemas;
    }

    /**
     * Finds a particular SQL schema from MySQL Workbench
     *
     * @access public
     * @param 	String $name
     * @return 	String	$schema	Found SQL schema, returns false if not found.
     * 
     */
    public function findSchema($name) {
    	$this->_checkSchemaList();
    	foreach ($this->_schemas as $schema) {
    		if (preg_match("/`{$name}`/i", $schema)) {
    			return $schema;
    		}
    	}
    	return false;
    }
    
    /**
     * Another wrapper function, this time used for deleting
     * our test tables.
     *
     * @access public
     * @return bool
     * 
     */
    public function drop() {
        return $this->_callMethod('drop');
    }
}