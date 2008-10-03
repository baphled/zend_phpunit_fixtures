<?php
class Zend_View_Helper_SiteUrl {
	function siteUrl($file = null) {
  		$baseUrl = str_replace('/index.php', '', Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl());
  		$hostUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];		
  		$siteUrl = $hostUrl . $baseUrl . ($file ? ('/' . trim((string) $file, '/\\')) : '');
  		return $siteUrl;
  	}
}