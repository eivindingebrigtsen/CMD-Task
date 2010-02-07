<?php
class Lists {
	public static $lists;	
	public function __construct() {
		self::writeLists();
  }
	public function writeLists(){
		//FB::info(Site::$user, 'User');
		if(Site::$user){			
			self::$lists = array();
			$sql = "SELECT `lists`.`id`, `lists`.`name`
					FROM `list_user` 
					INNER JOIN `lists`
					ON `lists`.`id` = `list_user`.`list`
					WHERE `list_user`.`user` = ". Site::$user .";";
			$result = Site::$db->query($sql);
			while($list = $result->fetch_object()){				   
					self::$lists[$list->id]['list'] = $list;
					self::$lists[$list->id]['tasks'] = $this->getTasks($list->id);					
			}
			FB::info(self::$lists,'SELF List');
		}
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
			$item = Tasker::getTaskArray($task);
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
	public function outputContentGUI(){
		$markup[] = '<h3>Lists</h3>';			
		$markup[] = '<ul id="lists" class="lists">';			
		foreach(self::$lists as $list){
			//FB::log($list, 'ListItem');
			$markup[] = '<li id="list_'.$list['list']->id. '" class="lists">'.$list['list']->name.'</li>';
		}
		$markup[] = '</ul>';			
		return implode($markup, '');
	}
}
?>