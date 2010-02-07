<?php
class Tasker  {
	public static $items;
	public static $tags;
	public static $lists;
	public static $projects;
	public static $keywords;
	public static $contexts;
	public static $people;
	public static $services;
	const VERSION = '0.1';    
	
	public function __construct() {
		Site::$title 	= 'Tasks';
		Site::$header 	= Site::parseFile('view/tasker/top.html');
		Site::$footer 	= Keywords::getTypesInfo();
  	}
	public function offsetGet($offset){
		switch($offset){
			case 'add':   
				Site::$string = self::addItem();
			break;
			case 'delete':  
			if(isset($_POST['id'])){
			 	Site::$string = self::hideItem($_POST['id']);				
			}
			break;
			case 'undo':  
				if(isset($_POST['id'])){
				 	Site::$string = self::setItemUndone($_POST['id']);				
				}
				break;
			case 'do':  
				if(isset($_POST['id'])){
				 	Site::$string = self::setItemDone($_POST['id']);				
				}
				break;
			case 'tasks':  
		 		$list = new Lists();  
				Site::$string =  $this->writeLists(); 

			break;
			case 'keys':  
			 	$keys = new Keywords();
				Site::$string = $keys->outputAsideGUI();
			break; 
			case 'dates':
				Site::$string = Time::getDates($_POST['dates']);
			break;
			case 'date':
				Site::$string = Time::getDateFromString(Site::$subsection);
			break;
			case 'view':  
			 	self::getItem(Site::$subsection);
			break;
			case 'update':   
			 	self::getItem(Site::$subsection);
			break;
			case 'import':
				self::getImportInterface();
			break;
			case 'setup':
				self::setupDB();
			break;
			default:
				self::getInterface();			
			break;
		}
		return true;
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
		if(!$nada){
			$sql = "INSERT INTO  `tasks` (`id` , `text` , `raw_string` , `desc` , `date` , `date_created` , `date_updated`) 
					VALUES (NULL ,  '". $text ."', '". $raw ."',  '". $desc ."',  ".$date.",  ". time().",  ". time() .");";
			$result = Site::$db->query($sql);
			$task = Site::$db->insert_id;
			$user = Site::$user;
			$list = 1;
			self::addTaskUserRelation($task, $user);
			Lists::addListTaskRelation($list, $task);
			FB::error($keys, 'Keys');
			if($keys){
				foreach($keys as $key => $name){
					$type = Keywords::getTypeID($name[0]);
					$keyword = Keywords::add_if_not_exists($name[1], $type);
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
	public function writeLists(){  	
		$markup = array();
		$markup[] = '<ul id="tasks">';
		foreach(Lists::$lists as $list){
			FB::log($list,'List');
			$markup[] = '<li><h2>'. $list['list']->name. '</h2>';
			$markup[] = '<ul id="'. $list['list']->name. '">';
			foreach($list['tasks'] as $task){
				$keys = array();
				$class = array();
				$keys[] = '<div class="task-keywords"><ul class="task-keywords">';
				$done = ($task['done'] ? 'done' : 'todo' );
				
				foreach($task['keywords'] as $type){
					foreach($type as $key){					
					$keys[] = '<li class="'.$key['type'].'">';
					$keys[] = '<span>'.$key['code'].$key['keyword'].'</span>';
					$keys[] = '</li>';
					$class[] = $key['keyword'];
				  }  
				}
				$keys[] = '</ul></div>';
				$markup[] = '<li id="task_'.$task['id'].'" class="task '.implode(' ',$class).' '. $done .'"><div class="task-item">';
				$markup[] = '<input type="hidden" name="id" value="'. $task['id']. '" />';
				$markup[] = '<span class="task-raw">'. $task['raw']. '</span>';
				$markup[] = '<span class="task-delete">X</span>';
				$markup[] = '<span class="task-done '. $done .'"></span>';
				$markup[] = '<span class="task-text">'. $task['text']. '</span>';
				$markup[] = '<span class="task-desc">'. $task['desc']. '</span>';
				$markup[] = '<div class="task-meta">';
				$markup[] = implode($keys, '');
				if($task['date']>0){
					$markup[] = '<span class="task-date">Due in '. Time::timeTo($task['date']). '</span>';
				}
				$markup[] = '<span class="task-updated">Updated '. Time::timeSince($task['date_updated']). '</span>';
				$markup[] = '</div></div></li>';
			}
			$markup[] = '</ul></li>';
		}
		$markup[] = '</ul>';
		return implode($markup, '');
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

	public function getAside(){
		$markup[] = Keywords::outputAsideGUI();
		return implode($markup, '');
	}
	public function getImportInterface(){
	  Site::$html = Site::parseFile('view/tasker/import.html');  
	  Site::$inlinejs .= Site::parseFile('static/javascript/import.js');	
	}
	public function getInterface(){
		self::$keywords = new Keywords();
		self::$lists = new Lists();
		Site::$vars['aside']  = $this->getAside();
		Site::$vars['items']  = $this->writeLists();
		Site::$html = Site::parseFile('view/tasker/dashboard.html');
		Site::$inlinejs .= self::getInputJs();
	}
	public function getInputJs(){ 
		$types = Keywords::getKeyTypes();		
		foreach($types as $key => $type){
			$shortcuts[] = <<<JAVASCRIPT
				case '{$type[1]}':
					ins.addClass('keyword {$type[0]}');
					type = '{$type[0]}';
				break;
JAVASCRIPT;
			$codes[] = <<<JAVASCRIPT
				$(document).data('codes').push('{$type[1]}');
JAVASCRIPT;
		}
		Site::$vars['handleshortcuts'] = implode($shortcuts, ' ');
		Site::$vars['codes'] = implode($codes, ' ');
		return Site::parseFile('static/javascript/tasker.js');
	}

}
?>