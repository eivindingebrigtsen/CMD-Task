<?
session_start();
ob_start();                       
/*
 * Bootstraps and includes all necessary files for the application
 */
require('../system/bootstrap.php');

/*
 * Initiate Site
 */
 	
$site = new Site();		
ob_end_flush();
?>