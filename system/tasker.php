<?php
class Tasker  {
	public static $items;
	public static $tags;
	public static $list;
	public static $lists;
	public static $projects;
	public static $keywords;
	public static $keytype;
	public static $keyword;
	public static $people;
	public static $services;
	const VERSION = '0.1';    
	
	public function __construct() {
//		Site::$footer 	= Keywords::getTypesInfo();
  	}
	public function offsetGet($offset){
		$helper = new TaskerHelper();
		self::$keywords = new Keywords();				
		self::$list = new Lists();				

		switch($offset){
			case 'add':   
				Site::$response = $helper->addItem(false);
			break;

			case 'delete':  
				if(isset($_POST['id'])){
			 		Site::$response = $helper->hideItem($_POST['id']);				
				}
			break;
			case 'undo':  
				if(isset($_POST['id'])){
				 	Site::$response = $helper->setItemUndone($_POST['id']);				
				}
				break;
			case 'do':  
				if(isset($_POST['id'])){
				 	Site::$response = $helper->setItemDone($_POST['id']);				
				}
				break;
			case 'tasks':  
		 		$list = new Lists();  
				Site::$response =  $this->writeLists(); 

			break;
			case 'interpret': 
				Site::$response = $this->interpret($_POST['raw']);
				break;
			case 'keys':  
			 	$keys = new Keywords();
				Site::$response = $keys->outputAsideGUI();
			break; 
			case 'dates':
				Site::$response = Time::getDates($_POST['dates']);
			break;
			case 'date':
				Site::$response = Time::getDateFromString(Site::$subsection);
			break;
			case 'view':  
			 	$helper->getItem(Site::$subsection);
			break;
			case 'update':   
			 	$helper->getItem(Site::$subsection);
			break;
			case 'import':
				$list = new Lists();				
				self::getImportInterface();
			break;
			case 'setup':
				self::setupDB();
			break;
			default:   
				#FB::error($offset, 'OFFSET NOT KNOWN');				
				self::$list->offsetGet($offset);
				self::getInterface();			
			break;
		}
		return true;
	}
    	public function writeLists(){  	
			$markup = array();
			$markup[] = '<ul id="tasks">';			
			FB::info(self::$list->lists, 'LISTS');
			if(isset(self::$list->lists)){
				
				foreach(self::$list->lists as $list){
					#FB::log($list,'List');
					$markup[] = '<li><h2>'. $list['list']['name']. '</h2>';
					$markup[] = '<ul id="'. $list['list']['name']. '">';
				 if($list['tasks']){
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
					}
					$markup[] = '</ul></li>';
				}
			}
			$markup[] = '</ul>';
			return implode($markup, '');
		}
	public function getAside(){
		$markup[] = Keywords::outputAsideGUI();
		return implode($markup, '');
	}
	public function getImportInterface(){
		self::$keywords = new Keywords();				
		Site::$title  					= 'cmdtask/'.Site::$section. '';
		Site::$vars['section'] 	= ''.Site::$section. '';
		Site::$vars['aside']  	= self::getAside();
		Site::$vars['items']  	= self::writeLists();
		Site::$header 					= Site::parseFile('view/tasker/top.phtml');
	  Site::$page 						= Site::parseFile('view/tasker/import.phtml');  
	  Site::$inlinejs 				.= Site::parseFile('static/javascript/import.js');	
	}
	public function getInterface(){	    		
		Site::$title 			= 'cmdtask/'.Site::$section. '';
		Site::$vars['section'] 	= ''.Site::$section. '';
		Site::$vars['aside']  	= self::getAside();
		Site::$vars['items']  	= self::writeLists();
		Site::$header 			= Site::parseFile('view/tasker/top.phtml');
		Site::$page 			= Site::parseFile('view/tasker/import.phtml');
		Site::$footer 			= Site::parseFile('view/tasker/bot.phtml');
	  Site::$inlinejs 				.= Site::parseFile('static/javascript/import.js');	
//		Site::$inlinejs 		.= self::getInputJs();
	}
	public function interpret ($string){
		$import = new StringImport();
		$text = $import->interpret($string);
		#FB::info($text);
		return $text;
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