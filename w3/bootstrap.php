<?php
require('../etc/conf.php');   
require('libs/validate_email.php'); 

# Classes 
require('class/site.php');
require('class/log.php');
require('class/i18n.php');
require('class/auth.php');
require('class/dbase.php');
require('class/time.php');
require('class/tasker.php');
require('class/keywords.php');
require('class/lists.php');
# require('class/events.php');


# Debug
include_once 'libs/FirePHPCore/fb.php';
FB::setEnabled(DEBUG); 
?>