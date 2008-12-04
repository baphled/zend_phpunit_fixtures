<?php
/**
 * My new Zend Framework project
 * 
 * @author Ekerete Akpan <ekerete@ibetx.com>
 * @author Yomi Akindayini <yomi@ibetx.com>
 * @version $Id: Initializer.php 385 2008-12-01 13:44:44Z dean $
 * @copyright 2008
 * @package IX9
 */
require_once realpath(dirname(__FILE__) .'/../library/Zend/ConfigSettings.php');
require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * 
 * Initializes configuration depndeing on the type of environment 
 * (test, development, production, etc.)
 *  
 * This can be used to configure environment variables, databases, 
 * layouts, routers, helpers and more
 *   
 */
class Initializer extends Zend_Controller_Plugin_Abstract {
    /**
     * @var Zend_Config
     */
    protected $_general;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     * 
     * @param  string $env 
     * @param  string|null $root 
     * @return void
     */
    public function __construct($root = null)
    {
        
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../');
        }
        $this->_root = $root;
        Ibetx_SetupInit::setupInit();
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_initErrorReporting();
		$tmz = Ibetx_ConfigSettings::setupTimeZone();
		date_default_timezone_set($tmz);

    }

    private function _initErrorReporting() {
    	Ibetx_SetupInit::initErrorReporting();
    	// set the test environment parameters
        if ('local' === Ibetx_SetupInit::$_env) {
			$this->_front->throwExceptions(true);  
        }
    }
    
    /**
     * Initialize environment
     * 
     * @param  string $env 
     * @return void
     */
    protected function _setEnv() 
    {
		$this->_env = $this->_general->environment; 	
    }
    
    /**
     * Route startup
     * 
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->initHelpers();
        $this->initView();
        $this->initControllers();
    }

    /**
     * Initialize action helpers
     * 
     * @return void
     */
    public function initHelpers()
    {
    	// register the default action helpers
    	Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers', 'Zend_Controller_Action_Helper');
    }
    
    /**
     * Initialize view 
     * 
     * @return void
     */
    public function initView()
    {
		$view = new Ibetx_Template_View();
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view)
						->setViewBasePathSpec(":moduleDir/views/scripts");
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);    	
    }

    /**
     * Initialize Controller paths 
     * 
     * @return void
     * @todo Refactor to parse over applications directory, storing each controller directory.
     */
    public function initControllers()
    {
    	$this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
    	$this->_front->addControllerDirectory($this->_root . '/application/myaccount/controllers', 'myaccount');
    }
}
?>
