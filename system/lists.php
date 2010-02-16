<?php
class Lists {
	public static $lists;	
	public static $helper; 
	public function __construct() {		
//  		$helper = new ListsHelper();  		
// 		self::$lists = $helper->getLists();
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
						FB::info($helper->lists, 'Tasks for Keyword');
						$this->lists = $helper->lists;
						Site::$section = $matches[0];
					}else{
						$helper->getKeynamesByKeyType($exists);
						FB::info($helper->lists, 'Tasks for Keyword');
						$this->lists = $helper->lists;
					}
				}				
			}else{
				if(Keywords::keyExists($offset, "'/'")){          
					/**
					 *  Handling directory as a list
					**/
					$type = array_search('/', $keys);
					#FB::log($type, 'TYPE');
					$helper->getTasksByKeyword($offset, $type);
					Site::$section = '/'.$offset;
					FB::info($helper->lists, 'Tasks for Keyword 2');
					$this->lists = $helper->lists;
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