<?php
class log {
  public static $code; 
  private $configuration;
  public function __construct() {
		$this->configuration = $this->loadConfiguration();
		Site::$vars['code'] = $this->configuration['analytics'];
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
		Site::$vars['code'] = $this->configuration['analytics'];
		return Site::parseFile('view/global/analytics.html');
	}
	private function loadConfiguration() {
	    $config_obj = Config::getInstance();
	    $config = $config_obj->getSection('DEFAULTS');
	    if($config) {
	        return $config;
	    } else {
	        return false;
	    }
	}
}
?>