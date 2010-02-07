<?php
class i18n {
  /**
   * Configuration storage
   * @var Array
   */
  private $configuration;
  public function __construct() {
    $this->configuration = $this->loadConfiguration();	 
	 Site::$lang = parse_ini_file('lang/lang_'.$this->configuration['lang'].'.ini');   
  }
	public function get($CONST){
		if( defined( $CONST ) ){
			return constant($CONST);
		}else{
			return $CONST;
		}
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