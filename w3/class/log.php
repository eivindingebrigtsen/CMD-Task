<?php
class log {
  public static $code;
  public function __construct() {
		self::$code = ANALYTICS_CODE;
		$this->logVisit();
  }     
	public function logVisit(){
		$url = Site::$action.Site::$section.Site::$subsection.Site::$do;
		$sql = "INSERT INTO  `visits` (`id` , `url` , `date` , `user`, `ip`) VALUES (NULL ,  '". $url ."', NULL,  ".Site::$user.", '".$_SERVER['REMOTE_ADDR']."');";
		$result = Site::$db->query($sql);
		FB::info($url,'URL');
		return true;		
		
	}
	public function getAnalytics(){
		Site::$vars['code'] = self::$code;
		return Site::parseFile('view/global/analytics.html');
	}
}
?>