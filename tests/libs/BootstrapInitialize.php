<?php
/**
 * Controller Testcase bootstrap.
 * 
 * Will be used by all controller tests to bootrap out 
 * framework.
 * 
 * @author Yomi (baphled) Akindayini
 * 
 */

require_once 'Zend/Controller/Plugin/Abstract.php';

class BootstrapInitialize extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_frontController;

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
    public function __construct($env, $root = null)
    {
        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../../');
        }
        $this->_root = $root;
        Zend_Session::start();              // smelly, need to destruct afer each test
        //$this->initPhpConfig();
    }

    /**
     * Route startup
     * 
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->initDb();
        $this->initView();
        
        /*
        $this->initHelpers();
        $this->initPlugins();
        $this->initRoutes();
        */
        $this->initControllers();
    }
    
    private function initView() {
    	// bootstrap layouts
    	$layoutPath = $this->_root .'/application/default/layouts';
        Zend_Layout::startMvc(array(
                    'layoutPath' => $layoutPath,
                    'layout' => 'main'
        ));
    }
    
    private function _setEnv( $env ) {
    	$configFile = $this->_root .'configs/settings.ini';
    	$this->_config = new Zend_Config_Ini( $configFile, $env);
        Zend_Registry::set('config',$this->_config);
    }
    
    private function initDb() {
    	$config = $this->_config;
    	$params = array( 'host'     => $config->database->hostname,
                        'username' => $config->database->username,
                        'password' => $config->database->password,
                        'dbname'   => $config->database->database);
    	$db = Zend_Db::factory('PDO_MYSQL', $params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
    }

    private function initControllers() {
    	$this->_frontController = Zend_Controller_Front::getInstance();
    	$this->_frontController->addControllerDirectory($this->_root .'/application/default/controllers/');
    }
    // definition of methods would follow...
}