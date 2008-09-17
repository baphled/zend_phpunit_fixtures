<?php
set_include_path('.' . PATH_SEPARATOR .
				dirname(__FILE__) . '/fixtures' . PATH_SEPARATOR .  
				dirname(__FILE__) . '/libs' . PATH_SEPARATOR . 
				dirname(__FILE__) . '/../application/default/models/' . PATH_SEPARATOR .
				dirname(__FILE__) . '/../library/' . PATH_SEPARATOR .
				dirname(__FILE__) . '/../application/features/models/' . PATH_SEPARATOR . 
				get_include_path());
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();