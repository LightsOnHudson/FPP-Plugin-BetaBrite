#!/usr/bin/php
<?php
$pluginName ="fork";


$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include_once("config/config.inc");
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include 'php_serial.class.php';



//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";

echo "logfile: ".$logFile."\n";

echo "argv: ".$argv."\n";

logEntry(" fork argv: ".$argv);

?>

