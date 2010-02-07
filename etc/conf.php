<?
define('DEBUG',true);
define('KEYPHRASE', 'abc');
define('SMTP', 'localhost');
define('DB', 'localhost');
define('DBTABLE','tasker');
define('DBUSER','root');
define('DBPASS','');   
define('LANG', 'en');
define('BASEPATH', "http://localhost/CMD-Task/");
define('ANALYTICS_CODE',false);
define('SEPERATOR', '/');
define('SEND_MAIL', true);
define('EMAIL_NAME', 'post@cmdtask.com');
define('EMAIL_SENDING', 'no-reply@cmdtask.com');
define('EMAIL_REPLY', 'post@cmdtask.com');
ini_set('SMTP', SMTP);
ini_set("sendmail_from","".EMAIL_NAME." <" .EMAIL_SENDING. ">");
?>
