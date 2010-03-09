<?

/** DO NOT REMOVE THIS UNTIL WE GOT A SETUP UTILITY **/
require_once('../core/setup_autoloader.php');
$builder = new SetupAutoloader();
$builder->run();
/**
 * Bootstraps and includes all necessary files for the application
 */

require_once('../core/autoloader.php'); // or die('System Error: No sources present to load');

session_start();
ob_start();
$site = new Site();		
ob_end_flush();
?>
