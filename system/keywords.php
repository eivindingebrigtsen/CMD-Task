<?php
class Keywords {
	public static $type;	
	public static $keys;
	public function __construct() {
		self::getUserKeywords();
  	}
	public function getUserKeywords(){
		//#FB::info(Site::$user, 'User');
		if(Site::$user){
			$sql = "SELECT `keywords`.`keyword`, `keywords`.`id`, `keytypes`.`type`,`keytypes`.`code`,`key_relations`.`task`
					FROM `keywords` 
					INNER JOIN `key_relations`
 					ON `key_relations`.`keyword` = `keywords`.`id`
					INNER JOIN `keytypes`
					ON `keywords`.`type` = `keytypes`.`id`
					WHERE `key_relations`.`user` = ". Site::$user ."
					ORDER BY `keywords`.`id`;";
			$result = Site::$db->query($sql);
            $name = array();
			while($obj = $result->fetch_assoc()){				   
				if(! in_array($obj['keyword'].'-'.$obj['type'], $name)){
						$name[] = $obj['keyword'].'-'.$obj['type'];
						self::$keys[ $obj['type'] ][] = $obj;				
					}
				}
			//#FB::info(self::$keys,'User Keywords');
		}
	}
	public function getKeywordsForTask($task){
		$keywords = array();
		$sql = "SELECT `keywords`.`keyword`, `keywords`.`id`, `keytypes`.`type`, `keytypes`.`code`
				FROM `key_relations` 
				INNER JOIN `keywords`
				ON `keywords`.`id` = `key_relations`.`keyword`
				INNER JOIN `keytypes`
				ON `keywords`.`type` = `keytypes`.`id`
				WHERE `key_relations`.`task` = ". $task ."
				ORDER BY `keywords`.`type`;";
		$result = Site::$db->query($sql);
		while($obj = $result->fetch_assoc()){				   
			$keywords[$obj['type']][] = $obj;				
		}                
		return $keywords;
	}
	public function keyExists($keyword, $type){
	    $sql = "SELECT `keywords`.`keyword`, `keywords`.`id`, `keywords`.`type`
				FROM `keywords`
				INNER JOIN `keytypes`
				ON `keytypes`.`id` = `keywords`.`type`
				WHERE `keywords`.`keyword` = '".$keyword."'";
		
		if(is_numeric($type)){
			$sql .= " AND `keytypes`.`id` = ".$type.";";
		}else{
			$sql .= " AND `keytypes`.`code` = ".$type.";";
		}
		$result = Site::$db->query($sql);
		#FB::log($result->num_rows, 'keyExists SQL');
		if($result->num_rows){			
			return $result->fetch_assoc();
		}else{
			return false;
		}                     
		
	}
	public function addKey($keyword, $type){	   
			$key = self::keyExists($keyword, $type);
			if($key){
				#FB::log($keyword, 'WE have it');
				#FB::log($type, 'WE have it');
				return $key['id'];
			}else{
				return self::writeKey($keyword, $type);
			}
	}
	public function writeKey($keyword, $type){
		$sql = "INSERT INTO `keywords` (`id`, `keyword`, `type`)
				VALUES (NULL,'".$keyword."',".$type.");";
		$result = Site::$db->query($sql);
		return Site::$db->insert_id;
	}
	public function getKeyID($keyword, $type){
		$typeid = $type;
		if(!is_numeric($type)){
			$typeid = self::getTypeID($type);			
		}
		$sql = "SELECT * FROM `keywords` WHERE `keywords`.`keyword` = '".$keyword."' AND `keywords`.`type` = '".$typeid."';";
		$result = Site::$db->query($sql);
		while($obj = $result->fetch_assoc()){				   
			return $obj['id'];			
		}
	}   

	public function getKeyTypes(){
		$sql = "SELECT * FROM `keytypes`;";
		$result = Site::$db->query($sql);
		while($obj = $result->fetch_assoc()){				   
			$types[] = array($obj['type'], $obj['code'], $obj['id']);			
		}
		return $types;
	}   

	public function getKeyCodes(){
		$sql = "SELECT * FROM `keytypes`;";
		$result = Site::$db->query($sql);
		$codes = array();
		while($obj = $result->fetch_assoc()){				   
			$codes[$obj['id']] = $obj['code'];
		}
		return $codes;
	}   
	public function getTypeID($type){
		$sql = "SELECT `keytypes`.`type`, `keytypes`.`id`
				FROM `keytypes` 
				WHERE `keytypes`.`type` = '". $type ."';";
		$result = Site::$db->query($sql);
		while($obj = $result->fetch_assoc()){				   
			return $obj['id'];
		}
	}   
	
	public function addTaskUserKeyRelation($task, $user, $keyword){
	    $sql = "SELECT `key_relations`.`keyword`
				FROM `key_relations`
				WHERE `key_relations`.`task` = '".$task."' 
				AND `key_relations`.`keyword` = '".$keyword."' 
				AND `key_relations`.`user` = '".$user."';"; 
		$results = Site::$db->query($sql);
		if($results->num_rows>0){
			return $results;
		}
		$sql = "INSERT INTO  `key_relations` (`user`,`task` , `keyword` ) VALUES (".$user." ,".$task." ,". $keyword .");";
		$result = Site::$db->query($sql);
		return $result;
		
	}    
	public function getTypesInfo(){
		$markup = '';
		if(self::$keys){
			#FB::warn(self::$keys, 'Keywords::$keys');
  	  	foreach(self::$keys as $key => $type){
				$markup[] = ''. $key. '';			
			}
    	Site::$inlinejs .= <<<JAVASCRIPT
		$('aside li').live('click',function(ev){
			$('#tasks > li > ul').find('li:not(.task-keywords li)').hide();
			$('#tasks > li > ul').find('li.'+$(this).attr('rel')).show().parents('li').show();
		});
JAVASCRIPT;
		return implode($markup, '');
		}
	}
	public function outputAsideGUI(){
		$markup = '';
		if(self::$keys){
  	  	foreach(self::$keys as $key => $type){
				$markup[] = '<h3>'. $key. '</h3>';			
				$markup[] = '<ul id="'. $key. '" class="keywords">';			
				$items = '';
				foreach($type as $item){
					$markup[] = '<li id="'.$key.'-'.$item['id'].'" class="'.$key.'" rel="'.$item['keyword'].'">'.$item['code'].$item['keyword'].'</li>';
					$items[] = '"'.$item['code'].$item['keyword'].'"';
				}
				$arr = '['.implode($items,',').']';
				Site::$inlinejs .= '$(document).data("'.$key.'", '.$arr.');';
				#FB::warn($key, $arr);
				$markup[] = '</ul>';			
			}
		return implode($markup, '');
		}
	}
}
?>