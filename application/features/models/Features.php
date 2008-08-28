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

class Features extends FeatureModel {
	function addNewFeature($data){
		if(!is_array($data)){
			throw new ErrorException('ffkkd');
		}
		if(null === $data['title'] || empty($data['title'])){
			throw new ErrorException('title must not be empty');			
		}
		if(null === $data['userid'] || empty($data['userid'])){
			throw new ErrorException('User id must not be empty');			
		}		
		return $this->insert($data);
	} 
}
