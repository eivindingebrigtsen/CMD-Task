<?php
require_once('Config.php');
require('libs/validate_email.php'); 

# Classes 
require_once('site.php');
require_once('i18n.php');
require_once('auth.php');
require_once('dbase.php');
require_once('time.php');
require_once('tasker.php');
require_once('keywords.php');
require_once('lists.php');
require_once('rest.php');
require_once('log.php');
# require('class/events.php');


# Debug
require_once('libs/FirePHPCore/fb.php');
FB::setEnabled(Site::$debug['debug']);   

/**
 * Make sure the systemwide configuration is loaded
 */
Config::getInstance();
?>