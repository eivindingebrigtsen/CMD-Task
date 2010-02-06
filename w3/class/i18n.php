<?php
class i18n {
  public function __construct() {
	 Site::$lang = parse_ini_file('lang/lang_'.LANG.'.ini');   
  }
	public function get($CONST){
		if( defined( $CONST ) ){
			return constant($CONST);
		}else{
			return $CONST;
		}
	}
}
?>