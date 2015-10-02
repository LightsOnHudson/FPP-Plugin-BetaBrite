#!/usr/bin/php
<?
error_reporting(0);

$pluginName ="BetaBrite";


$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include("config/config.inc");
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include_once("commonFunctions.inc.php");
include 'php_serial.class.php';

$ENABLED="";

$ENABLED = trim(urldecode(ReadSettingFromFile("ENABLED",$pluginName)));

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/".$pluginName.".log";
//$logFile = $logDirectory."/logs/betabrite.log";
//echo "Enabled: ".$ENABLED."<br/> \n";


if($ENABLED != "on" && $ENABLED != "1") {
	logEntry("Plugin Status: DISABLED Please enable in Plugin Setup to use & Restart FPPD Daemon");
	
	exit(0);
}
$callbackRegisters = "media\n";
$myPid = getmypid();
//var_dump($argv);

$FPPD_COMMAND = $argv[1];

//echo "FPPD Command: ".$FPPD_COMMAND."<br/> \n";

if($FPPD_COMMAND == "--list") {

			echo $callbackRegisters;
			logEntry("FPPD List Registration request: responded:". $callbackRegisters);
			exit(0);
}

if($FPPD_COMMAND == "--type") {
			logEntry("type callback requested");
			//we got a register request message from the daemon
			processCallback($argv);	
			exit(0);
}

			logEntry($argv[0]." called with no parameteres");
			exit(0);
?>
