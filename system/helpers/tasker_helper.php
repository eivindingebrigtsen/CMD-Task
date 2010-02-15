<?
class TaskerHelper extends Tasker {
	public function __construct(){
		parent::__construct();
	}
	public function getPostData(){
		#FB::error($_POST, "POST");
		$data = array();
		$data['nada'] 	= true;
		$data['raw'] 	= false;
		$data['text'] 	= false;
		$data['desc'] 	= 'NULL';
		$data['date'] 	= 'NULL';
		$data['keys'] 	= false;
		if(isset($_POST['raw']) && trim($_POST['raw']) != ''){
			$data['raw'] = $_POST['raw'];
			$data['nada'] = false;
			if(isset($_POST['text'])){
				$data['text'] = $_POST['text'];
			}
			if(isset($_POST['desc'])){
				$data['desc'] = $_POST['desc'];
			}
			if(isset($_POST['date'])){
				$data['date'] = strtotime($_POST['date']);
			}
			if(isset($_POST['keys'])){
				$data['keys'] = $_POST['keys'];
			}
		}
		return $data;
		
	}
	public function addRelations($name, $type, $task){
		$type = Keywords::getTypeID($type);
		if($type){
			$keyword = Keywords::addKey($name, $type);
			Keywords::addTaskUserKeyRelation($task, Site::$user, $keyword);
		}
	}
	public function writeTask($data){
		$sql = "INSERT INTO  `tasks` (`id` , `text` , `raw_string` , `desc` , `date` , `date_created` , `date_updated`) 
				VALUES (NULL ,  '". $data['text'] ."', '". $data['raw'] ."',  '". $data['desc'] ."',  ".($data['date'] ? $data['date'] : 'NULL' ).",  ". time().",  ". time() .");";
		$result = Site::$db->query($sql);
		return Site::$db->insert_id;
	}
	public function addItem($data){
		if(!$data){
		   $data = self::getPostData(); 
		}
		#FB::info($data['nada'], 'Nothing here?');
		if(!$data['nada']){ 
			#FB::info($data, 'adding');
			$task = self::writeTask($data);
			#FB::log($data, $task);
			$user = Site::$user;
			$list = 1;
			self::addTaskUserRelation($task, $user);
			ListsHelper::addListTaskRelation($list, $task);
			if($data['keys']){
				foreach($data['keys'] as $key => $name){
					if(is_array($name)){
						foreach($name as $key => $name){
							#FB::info($name, $key);
							self::addRelations($name, $key, $task);
						}
					}else{
						#FB::info($name, $key);
							self::addRelations($name, $key, $task);
					}
					#FB::info($name, $key);
				}				
			}			
			return '{
				"status": "success",
				"string": "Added a task with id '.$task.'"
			}';
		}else{
			return '{
				"status": "warn",
				"string": "Nothing sent up"
			}';
			
		}
	}
	public function hideItem($id){
		$sql = "UPDATE `tasks` SET `tasks`.`hidden` = 1, `date_updated` = ". time()." WHERE `tasks`.`id` = ". $id .";";
		$result = Site::$db->query($sql);
		if($result){			
			return '{"status": "success"}';
		}
		return '{"status": "error"}';
		
	}
	
	public function setItemDone($id){
		$sql = "UPDATE `tasks` SET `tasks`.`done` = 1, `date_updated` = ". time()." WHERE `tasks`.`id` = ". $id .";";
		$result = Site::$db->query($sql);
		if($result){			
			return '{"status": "success"}';
		}
		return '{"status": "error"}';
	}
	public function setItemUndone($id){
		$sql = "UPDATE `tasks` SET `tasks`.`done` = 0, `date_updated` = ". time()." WHERE `tasks`.`id` = ". $id .";";
		$result = Site::$db->query($sql);
		if($result){			
			return '{"status": "success"}';
		}
		return '{"status": "error"}';
	}
	public function addTaskUserRelation($task, $user){
		if(empty($user)){
			$user = Site::$user;
		}
		$sql = "INSERT INTO `tasks_user`(`task`, `user`) VALUES (".$task.", ".$user.");";
		$results = Site::$db->query($sql);
		return $results;
	}
	public function getTaskArray($task){
		$item = array();
		$item['id'] = $task->id;
		$item['raw'] = $task->raw_string;
		$item['text'] = $task->text;
		$item['date'] = $task->date;
		$item['desc'] = $task->desc;
		$item['done'] = $task->done;
		$item['date_created'] = $task->date_created;
		$item['date_updated'] = $task->date_updated;
		$item['hidden'] = $task->hidden;
		$item['priority'] = $task->priority;
		$item['replyto'] = $task->replyto;
		$item['keywords'] = Keywords::getKeywordsForTask($task->id);
		return $item;
	}
	public function getItem($id){
			$item = array();
			$sql = "SELECT `tasks`.`id`, `tasks`.`text`, `tasks`.`raw_string`, `tasks`.`desc`, `tasks`.`date`, `tasks`.`done`,`tasks`.`date_created`,`tasks`.`date_updated`, `tasks`.`priority`,`tasks`.`hidden`, `tasks`.`replyto`
					FROM `tasks` 
					WHERE `tasks`.`id` = ".$id.";";
			$result = Site::$db->query($sql);
			$task = $result->fetch_object();
			return self::getTaskArray($task);
	}
	public function updateItem($id){
		return true;
	}
	public function deleteItem($id){
		$item = self::getItem($id);
		#FB::info($item, 'ITEM');
		$sql  = "DELETE FROM `tasks` WHERE `id` = ".$id.";
			DELETE FROM IGNORE `key_relations` WHERE `task` = ".$id.";
  			DELETE FROM `lists_tasks` WHERE `task` = ".$id.";
  			DELETE FROM `tasks_user` WHERE `task` = ".$id.";";

		#FB::log($sql, 'SQL');
		$result = Site::$db->multi_query($sql);
		#FB::log($result, 'RESULT');
		if($result){			
			return '{"status": "success"}';
		}
   		return '{"status": "error"}';
	}
	
}