<?
/**
* 
*/
class ListsHelper extends Lists
{  
	public static $lists;
	function __construct()
	{
		self::getLists();
	}
	public function getLists(){
		//#FB::info(Site::$user, 'User');
		if(Site::$user){			
			self::$lists = array();
			$sql = "SELECT `lists`.`id`, `lists`.`name`
					FROM `list_user` 
					INNER JOIN `lists`
					ON `lists`.`id` = `list_user`.`list`
					WHERE `list_user`.`user` = ". Site::$user .";";
			$result = Site::$db->query($sql);
			while($list = $result->fetch_assoc()){				   
					self::$lists[$list['id']]['list'] = $list;
					self::$lists[$list['id']]['tasks'] = self::getTasks($list['id']);					
			}
			//#FB::info(self::$lists,'SELF List');
		}
	}
	public function getTasksByKeyword($keyword, $type){
		$tasks = array();
		$keyid = Keywords::getKeyID($keyword, $type);			
		#FB::info('Type '.$type.' Keyword '.$keyword.' Keyid '.$keyid, 'getTasksByKeyword');
		$sql = "SELECT `tasks`.`id`, `tasks`.`text`, `tasks`.`raw_string`, `tasks`.`desc`, `tasks`.`date`, `tasks`.`done`,`tasks`.`date_created`,`tasks`.`date_updated`, `tasks`.`priority`,`tasks`.`hidden`,`tasks`.`replyto`
				FROM `key_relations` 
				INNER JOIN `tasks`
				ON `tasks`.`id` = `key_relations`.`task`
				WHERE `key_relations`.`keyword` = ". $keyid ." AND `key_relations`.`user` = ".Site::$user."
				ORDER BY `key_relations`.`id` DESC;";				
		$result = Site::$db->query($sql);
		#FB::error($result->num_rows, 'SQL');
		if($result->num_rows > 0){
			while($task = $result->fetch_object()){				   				
				$item = TaskerHelper::getTaskArray($task);         
				#FB::info($item, 'ListsHelper');
				$tasks[]= $item;
			}
		}		
		$this->lists[$keyid]['list'] = array('id' => $keyid, 'name' => $keyword);
		$this->lists[$keyid]['tasks'] = $tasks;					
	}
	public function getKeynamesByKeyType($type){
		$tasks = array();
		#FB::info('Type '.$type ,'getKeynamesByKeyType');
		$sql = "SELECT `keywords`.`id`, `keywords`.`keyword`, 
					   `keywords`.`type`, `keytypes`.`type`,
					   `keytypes`.`code`
				FROM `keywords` 
				LEFT JOIN `key_relations`
				ON `key_relations`.`keyword`  = `keywords`.`id`
				RIGHT JOIN `keytypes`
				ON `keytypes`.`id` = ".$type."
				WHERE `keywords`.`type` = ".$type."
				ORDER BY `keywords`.`id` DESC;";				
		$result = Site::$db->query($sql);
		if($result->num_rows > 0){
			while($task = $result->fetch_object()){				   				
				#FB::error($task, 'SQL');
				$item = TaskerHelper::getTaskArray($task);         
				#FB::info($item, 'ListsHelper');
				$tasks[]= $item;
			}
		}		
		$this->lists[$keyid]['list'] = array('id' => $keyid, 'name' => $keyword);
		$this->lists[$keyid]['tasks'] = $tasks;							
	}


	public function getTasks($list){
		$tasks = array();
		$sql = "SELECT `tasks`.`id`, `tasks`.`text`, `tasks`.`raw_string`, `tasks`.`desc`, `tasks`.`date`, `tasks`.`done`,`tasks`.`date_created`,`tasks`.`date_updated`, `tasks`.`priority`,`tasks`.`hidden`,`tasks`.`replyto`
				FROM `lists_tasks` 
				INNER JOIN `tasks`
				ON `tasks`.`id` = `lists_tasks`.`task`
				WHERE `lists_tasks`.`list` = ". $list ." AND `tasks`.`hidden` != 1
				ORDER BY `tasks`.`id` DESC;";				
		$result = Site::$db->query($sql);		
		while($task = $result->fetch_object()){				   
			$item = TaskerHelper::getTaskArray($task);
			$tasks[]= $item;
		}
		return $tasks;
	}
	public function exists($name){
		$sql = "SELECT `lists`.`id` 
				FROM  `lists` 
				WHERE `lists`.`name` = '".$name."';";
		$result = Site::$db->query($sql);
		if($result->num_rows>0){
			return true;
		}
		return false;
	}
	public function addList($name){
		$sql = "INSERT INTO  `lists` (`id` , `name` ) VALUES (NULL ,  '". $name ."');";
		$result = Site::$db->query($sql);
		return Site::$db->mysql_insert_id;
	}
	public function addListTaskRelation($list, $task){
		$sql = "INSERT INTO  `lists_tasks` (`task` , `list` ) VALUES (".$task." ,  ". $list .");";
		$result = Site::$db->query($sql);
		return $result;
	}    
	public function removeTaskRelation($list, $task){
		return true;
	}
	public function updateList(){
		return true;
	}
}
