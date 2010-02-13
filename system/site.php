<?php
/**           
 * Site class
 * Holds and executes other classes
 *
 * @version 	0.1
 * @copyright   Copyright (C) 2009-2010 Eivind Ingebrigtsen
 * @author      Eivind Ingebrigtsen <eivindingebrigtsen@gmail.com>
 * @license     MIT
 * @package     Site
 */

class Site {
	public static $db;
	public static $auth; 
	public static $user;
	public static $i18n;
	public static $log; 
	public static $event; 
	public static $action;
	public static $section;
	public static $subsection;
	public static $do;  
	public static $url;
	public static $title;
	public static $lang;
	public static $header = ''; 
	public static $footer = '';
	public static $response;
	public static $page;
	public static $vars;
	public static $inlinejs;
	public static $offset;
	public static $file;
	public static $defaults; 
	public static $debug; 
	/**
	 * Construct
	 * Initiate needed classes
	 */
	public function __construct() {
		self::$title 		= '+Task';
        self::$action 		= false;
        self::$section 		= false;		
        self::$subsection 	= false;		
        self::$do 			= false; 
		/*
		 * References to instances of classes accessible for other classes
		 */
		self::$i18n			= new i18n(); 
		self::$db 			= new dBase();
		self::$auth 		= new Auth();
		
		self::$user 		= Auth::$user;
		
		$config 			= Config::getInstance();
		self::$defaults 	= $config->getSection('DEFAULTS');   
		self::$debug 		= $config->getSection('DEBUG');   
		self::getOffsetArray();
		self::display();		
		self::getDebug();
		self::$log = new log();   

		##FB::info($_REQUEST, 'Request data');
		##FB::info($_SERVER, 'Site construct');
	}
	private function getOffsetArray(){
		// From the url
		if(isset($_GET['action'])){
			self::$action = strtolower($_GET['action']);
			self::$offset['action'] = self::$action;
		}
		if(isset($_GET['section'])){
			self::$section = strtolower($_GET['section']);			
			self::$offset['section'] = self::$section;
		}
		if(isset($_GET['subsection'])){
			self::$subsection = strtolower($_GET['subsection']);
			self::$offset['subsection'] = self::$subsection;
		}
		if(isset($_GET['do'])){
			self::$do = strtolower($_GET['do']);
			self::$offset['do'] = self::$do;
		}
		#FB::log(self::$offset, 'Offset');
	}
	private function offsetGet(){
		if( ! Auth::$authenticated ){
		 		Auth::handleAuth();
		 	return;
	    }
		switch( Site::$action ){
			case 'tasks':
				$tasker = new Tasker();
				$tasker->offsetGet(Site::$section, Site::$subsection, Site::$do);
				//#FB::info($tasker, 'Tasker');
			break;
			case 'api': 
				$request = new RestRequest($_SERVER['REQUEST_URI'], Site::$section);  
				$request->execute();  
				echo '<pre>' . print_r($request, true) . '</pre>';
				
			break;
		}
	}                                
	/**
	 *  @return File parsed with variables and strings replaced
	**/
	
	public function parseFile($file){
		if (file_exists($file)){
		   	$arr = file($file);
			$content = '';
			foreach($arr as $line){				
				# VARIABLES
				$line = preg_replace_callback(
					'/{\$(.*?)}/', 
					create_function(
					 '$matches',
		             'return Site::$vars[$matches[1]];'
					), $line);
				# LANGUAGES
				$line = preg_replace_callback(
					'/\[i18n\((.*?)\)\]/', 
					create_function(
					 '$matches',
		             'return Site::$lang[$matches[1]];'
					), $line);
				$content .= $line;
			}                 
			# Stripping all new lines, returns and tabs 
			return preg_replace('/(\n|\r|\t)/', '', $content);
		}
	}

 	private function display(){
		self::offsetGet();
		/**
		 *  @todo rewrite display function
		**/
		
		if( isset(Site::$page) ){  
			self::$vars['analytics'] = '';
			self::$vars['userpanel'] = '';
			if( empty(Site::$inlinejs) ){
				Site::$inlinejs = '';
			}
			if( Auth::$authenticated ){
				self::$vars['userpanel'] .= '<a href="admin/logout">Logout</a>';
			}

			if( self::$defaults['analytics'] ){
				self::$vars['analytics'] = Site::$log->getAnalytics();
			}
			self::$vars['base'] 	= self::$defaults['basepath'];
			self::$vars['action'] 	= self::$action;
			self::$vars['inlinejs'] = self::$inlinejs;
			self::$vars['title'] 	= self::$title;
			self::$vars['header'] 	= self::$header;
			self::$vars['footer'] 	= self::$footer;

			$html = self::$page;
			$head = self::parseFile('view/global/header.html');      
			$foot = self::parseFile('view/global/footer.html');
			echo <<<HTML
			{$head}
			<section class="container"> 
				{$html}
			</section>		
			{$foot}
HTML;

		} else if ( isset( self::$response ) ){
				echo self::$response;
		} else {
			echo 'no output';
		}
	
	}
	private function getDebug(){
		#FB::setEnabled(self::$debug['debug']);   
		self::$db->getDebug();
		#FB::log($_SERVER, 'SERVER');		
	}
}
?>
