#!/usr/bin/php
<?
error_reporting(0);

//$pluginName ="BetaBrite";
$pluginName = basename(dirname(__FILE__));  //pjd 7-10-2019   added per dkulp 
$DEBUG=true;

$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");

include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include_once("BetaBriteFunctions.inc.php");
include_once("commonFunctions.inc.php");


$ENABLED="";

$logFile = $settings['logDirectory']."/".$pluginName.".log";
$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;

if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);
        logEntry("DEBUG: plugin config file: ".$pluginConfigFile);

        $STATIC_TEXT_PRE = urldecode($pluginSettings['STATIC_TEXT_PRE']);
        $STATIC_TEXT_POST = urldecode($pluginSettings['STATIC_TEXT_POST']);
        $LOOPTIME = $pluginSettings['LOOPTIME'];
        $SEPARATOR = urldecode($pluginSettings['SEPARATOR']);	
        $DEVICE= urldecode($pluginSettings['DEVICE']);	
        $DEVICE_CONNECTION_TYPE= urldecode($pluginSettings['DEVICE_CONNECTION_TYPE']);	
$ENABLED = urldecode($pluginSettings['ENABLED']);

//arg0 is  the program
//arg1 is the first argument in the registration this will be --list

//$logFile = $logDirectory."/logs/betabrite.log";
//echo "Enabled: ".$ENABLED."<br/> \n";


if($ENABLED != "on" && $ENABLED != "1") {
	logEntry("Plugin Status: DISABLED Please enable in Plugin Setup to use & Restart FPPD Daemon");
	
	exit(0);
}
$callbackRegisters = "media\n";
$myPid = getmypid();
//var_dump($argv);



        $BAUD = "9600";
        $PARITY="none";
        $CHAR_BITS="8";
        $STOP_BITS="1";

$FPPD_COMMAND = $argv[1];

//echo "FPPD Command: ".$FPPD_COMMAND."<br/> \n";

if($FPPD_COMMAND == "--list") {

			echo $callbackRegisters;
			logEntry("FPPD List Registration request: responded:". $callbackRegisters);
			exit(0);
}

if($FPPD_COMMAND == "--type") {
		if($DEBUG)
			logEntry("DEBUG: type callback requested");
			//we got a register request message from the daemon
		$forkResult = fork($argv);
		if($DEBUG)
		logEntry("DEBUG: Fork Result: ".$forkResult);
		exit(0); 
		//	processCallback($argv);	
} else {

			logEntry($argv[0]." called with no parameteres");
			exit(0);
}
?>
