<?
/**  
 * Authenticate class
 * Handles login to the system
 *
 * @copyright   Copyright (C) 2009-2010 Eivind Ingebrigtsen
 * @author      Eivind Ingebrigtsen <eivindingebrigtsen@gmail.com>
 * @license     MIT
 * @package     Site
 */

class Auth{         
	/**
   * Authenticate version
   *
   * @var string
   */
  public static $authenticated;
  public static $user;
  const VERSION = '0.1';    
	public function __construct(){		
		if(empty($_SESSION['loggedin'])){
			#FB::info('Not authenticated');
			$_SESSION['loggedin'] = false;
		 	self::$authenticated = false;   
			self::$user = 0; 
		}else{
			#FB::info('Autenticated');
			self::$user = 1; // Root ID for now
			self::$authenticated = true;
		}
		
	}      
	public function handleAuth(){
		switch(Site::$section){
			case 'login': 
			   	Site::$response = Site::$auth->authenticatePhrase();
				break;
			default: 
		   		Site::$inlinejs = Site::$auth->loginJs();
	    		Site::$page = Site::$auth->displayLogin();		
			break;
		}			 		
	}
	/**
	 * Authenticates user and password
	 * @see 		Auth->authenticate();
	 * @param 	$user: string (email@domain.tld), $pass: string
	 * @return 	Boolean
	 */
	public function authenticateUser ($user = null, $pass = null){
		#FB::log('Authenticated');
		return true;
	}
	/**
	 * Authenticates the keyphrase
	 * @see 		Auth->authenticatePhrase();
	 * @param 	$phrase: string
	 * @return 	Boolean
	 */
	
	public function authenticatePhrase(){
		$status = 'warning';
		$phrase = $_POST['phrase'];  
		#FB::log($phrase, 'Logging in with');
		if($phrase == Site::$defaults['keyphrase']){
			$status = 'success';
			#FB::log($phrase, 'Logged in');
			$_SESSION['loggedin'] = true;
			self::$authenticated = true; 
		}else{
			#FB::log($phrase, 'Not logged in');					
			$_SESSION['loggedin'] = false;
			self::$authenticated = false; 

		}
		return '{
			status: "'. $status .'"				
		}';
	} 
	public function logOut(){
		#FB::info('logging out');
		$_SESSION['loggedin'] = false;
		self::$authenticated = false;
		return '{loggedout: '.$_SESSION['loggedin'].'}';
	}
	public function displayLogin(){
		return include('view/global/login_form.phtml');
	}
	public function loginJs(){		
		return Site::parseFile('static/javascript/login.js');
	}
	public function generatePassword($length) {
	    $character = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $password = "";
	    for($i=0;$i<$length;$i++) {
	        $password .= $character[rand(0, 61)];
	    }
	    return $password;
	}
}
?>