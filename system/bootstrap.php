<?php
require('../etc/conf.php');   
require('libs/validate_email.php'); 

# Classes 
require('site.php');
require('log.php');
require('i18n.php');
require('auth.php');
require('dbase.php');
require('time.php');
require('tasker.php');
require('keywords.php');
require('lists.php');
require('rest.php');
# require('class/events.php');


# Debug
include_once 'libs/FirePHPCore/fb.php';
FB::setEnabled(DEBUG); 
?>