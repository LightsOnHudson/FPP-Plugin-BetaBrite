#!/usr/bin/php
<?php
//$pluginName ="BetaBrite";
$pluginName = basename(dirname(__FILE__));  //pjd 8-20-2019   added per dkulp 


$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include_once("config.inc");               //pjd 8-20-2019   remove /config/ from the beginning of config.inc
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include 'php_serial.class.php';



//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";


sendLineMessage("test");

?>
