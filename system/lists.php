<?php
class Lists {
	public static $lists;	
	public static $helper; 
	public function __construct() {		
  	} 
	public function offsetGet($offset){
		$helper = new ListsHelper();  		
		if($offset){
			$keys = Keywords::getKeyCodes();
			$exp = '[';
			foreach($keys as $id => $code){
				$exp .= '\\'.$code;
			}
			$exp .= ']';
			preg_match('/^('.$exp.')(.*?)$/', $offset, $matches);
			if(count($matches)){
				$exists = array_search($matches[1], $keys);
				if($exists !== false){
					if($matches[2]){
						$helper->getTasksByKeyword($matches[2], $exists);
						#FB::info($helper, 'Tasks for Keyword');						
					}else{
						$helper->getKeynamesByKeyType($exists);
					}
				}				
			}else{
				if(Keywords::keyExists($offset, "'/'")){          
					/**
					 *  Handling directory as a list
					**/
					$type = array_search('/', $keys);
					#FB::log($type, 'TYPE');
					Site::$section = '/'.$offset;
					$helper->getTasksByKeyword($offset, $type);
					#FB::info(self::$lists, 'Tasks for Keyword 2');
				}else{
					#FB::error($offset, 'We don\'t know this');
				}
			}			
		}else{
			/**
			 *  @todo add javascript handling of location.hash 			
			**/			                                       
			#FB::error('@todo location.hash js');
			$helper->getLists();
		}
	 	self::$lists = $helper->lists;	
}

  
	public function outputContentGUI(){
		$markup[] = '<h3>Lists</h3>';			
		$markup[] = '<ul id="lists" class="lists">';			
		foreach(self::$lists as $list){
			//#FB::log($list, 'ListItem');
			$markup[] = '<li id="list_'.$list['list']->id. '" class="lists">'.$list['list']->name.'</li>';
		}
		$markup[] = '</ul>';			
		return implode($markup, '');
	}
}
?>