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
	public static $header; 
	public static $footer;
	public static $string;
	public static $html;
	public static $vars;
	public static $inlinejs;
	public static $offset;
	public static $file;
	/**
	 * Construct
	 * Initiate needed classes
	 */
	public function __construct() {
		self::$i18n			= new i18n(); 
		FB::log(self::$lang, 'i18n');
		self::$db 			= new dBase();
		self::$auth 		= new Auth();
		#self::$event		= new EvData();
		self::$title 		= '+Task';
		self::$user 		= Auth::$user;
        self::$action 		= false;
        self::$section 		= false;		
        self::$subsection 	= false;		
        self::$do 			= false; 
		FB::info($_SERVER, 'Site construct');
		self::getOffset();
		self::display();
	}
	private function getOffset(){
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
		self::$log = new log();
		FB::log(self::$offset, 'Offset');
		
	}
	private function offsetGet(){
		if( ! Auth::$authenticated ){
				Auth::handleAuth();
			return;
		}
		if( ! Site::$action ){
						header('location: tasker');	
		}
		switch( Site::$action ){
			case 'tasker':
				$tasker = new Tasker();
				$tasker->offsetGet(Site::$section, Site::$subsection, Site::$do);
				FB::info($tasker, 'Tasker');
			break;
		}
	}

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
			return preg_replace('/(\n|\r|\t)/', '', $content);
		}
	}

 	private function display(){
		self::offsetGet();
		if( isset(Site::$html) ){  
			$analytics = '';
			$userpanel = '';
			if( empty(Site::$inlinejs) ){
				Site::$inlinejs = '';
			}
			if( Auth::$authenticated ){
				$userpanel .= '<a href="admin/logout">Logout</a>';
			}

			if( ANALYTICS_CODE ){
				$analytics = Site::$log->getAnalytics();
			}
			$base = BASEPATH;
			$action = Site::$action;
			$inlinejs = Site::$inlinejs;
			$title = Site::$title;
		    $html = Site::$html;
		    $header = Site::$header;
		    $footer = Site::$footer;

			require('view/global/header.php');      
			echo <<<HTML
						
			<section class="container {$action}"> 
				{$html}
			</section>		
HTML;
			require('view/global/footer.php');

		} else if ( isset( Site::$string ) ){
				echo Site::$string;
		}
	
	}
}
?>