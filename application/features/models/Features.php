<?php

/**
 * Features
 *  
 * @author Yomi (baphled) Akindayini 2008
 * @version $Id$
 * @copyright ibetX Ltd2008
 * @package
 *  
 */

class Features {
	function addNewFeature($data){
		if(!is_array($data)){
			throw new ErrorException('ffkkd');
		}
		if(null === $data['title'] || empty($data['title'])){
			throw new ErrorException('title must not be empty');			
		}
		if(null === $data['userId'] || empty($data['userId'])){
			throw new ErrorException('User id must not be empty');			
		}		
		return 1;
	} 
}
