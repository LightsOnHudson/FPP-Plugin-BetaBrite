#!/usr/bin/php
<?
error_reporting(0);

$skipJSsettings = 1;
include_once '/opt/fpp/www/config.php';
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");


//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/betabrite.log";
//$logFile = $logDirectory."/logs/betabrite.log";


logEntry("OPENING LOG FILE ".$logFile." but you know that, because you are seeing this now :) ");


$callbackRegisters = "media\n";
//var_dump($argv);

switch ($argv[1])
	{
		case "--list":
			echo $callbackRegisters;
			logEntry("FPPD List Registration request: responded:". $callbackRegisters);
			exit(0);
			break;

		case "--type":
			//we got a register request message from the daemon
			processCallback($argv);	
			exit(0);
			break;

		default:
			logEntry($argv[0]." called with no parameteres");
			exit(0);
			break;	
	}
	
exit(0);
?>
