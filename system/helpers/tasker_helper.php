<?
class TaskerHelper extends Tasker {
	public function __construct(){
		parent::__construct();
	}
	public function addItem(){
		$nada 	= true;
		$raw 	= false;
		$text 	= false;
		$desc 	= NULL;
		$date 	= 0;
		$keys 	= false;
		if(isset($_POST['raw']) && trim($_POST['raw']) != ''){
			$raw = $_POST['raw'];
			$nada = false;
			if(isset($_POST['text'])){
				$text = $_POST['text'];
			}
			if(isset($_POST['desc'])){
				$desc = $_POST['desc'];
			}
			if(isset($_POST['date'])){
				$date = strtotime($_POST['date']);
			}
			if(isset($_POST['keys'])){
				$keys = $_POST['keys'];
			}
		}
		FB::info($nada, 'Nothing here?');
		if(!$nada){ 
			FB::info($raw, 'adding');
			$sql = "INSERT INTO  `tasks` (`id` , `text` , `raw_string` , `desc` , `date` , `date_created` , `date_updated`) 
					VALUES (NULL ,  '". $text ."', '". $raw ."',  '". $desc ."',  ".$date.",  ". time().",  ". time() .");";
			$result = Site::$db->query($sql);
			$task = Site::$db->insert_id;
			$user = Site::$user;
			$list = 1;
			self::addTaskUserRelation($task, $user);
			ListsHelper::addListTaskRelation($list, $task);
			if($keys){
				foreach($keys as $key => $name){
					$type = Keywords::getTypeID($name[0]);
					$keyword = Keywords::addKey($name[1], $type);
					Keywords::addTaskUserKeyRelation($task, $user, $keyword);
					FB::info($type, $keyword);
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
		FB::info($item, 'ITEM');
		$sql  = "DELETE FROM `tasks` WHERE `id` = ".$id.";
			DELETE FROM IGNORE `key_relations` WHERE `task` = ".$id.";
  			DELETE FROM `lists_tasks` WHERE `task` = ".$id.";
  			DELETE FROM `tasks_user` WHERE `task` = ".$id.";";

		FB::log($sql, 'SQL');
		$result = Site::$db->multi_query($sql);
		FB::log($result, 'RESULT');
		if($result){			
			return '{"status": "success"}';
		}
   		return '{"status": "error"}';
	}
	
}