<?
session_start();
ob_start();                       
/*
 * Bootstraps and includes all necessary files for the application
 * includes: conf.php, debug.php, connect.php
 */
require('bootstrap.php');
/*
 * Initiate Site
 */
$site = new Site();		
Site::$db->getDebug();
ob_end_flush();
?>