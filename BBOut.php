#!/usr/bin/php
<?php
error_reporting(0);
include 'php_serial.class.php';
include_once('projectorCommands.inc');

$skipJSsettings = 1;
include_once '/opt/fpp/www/config.php';
include_once '/opt/fpp/www/common.php';

$pluginName  = "BetaBrite";

include_once 'functions.inc.php';
include_once 'commonFunctions.inc.php';

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);

$logFile = $settings['logDirectory'] . "/".$pluginName.".log";
$myPid = getmypid();

//$cfgServer="192.168.192.15";
$cfgPort="3001";
$cfgTimeOut=10;
$DEBUG=false;
$SERIAL_DEVICE="";
$callBackPid="";

//$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);
$DEVICE = $pluginSettings['DEVICE'];

//$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
$DEVICE_CONNECTION_TYPE = $pluginSettings['DEVICE_CONNECTION_TYPE'];

//$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);
$ENABLED = $pluginSettings['ENABLED'];




$SERIAL_DEVICE="/dev/".$DEVICE;



$cmd= $argv[0];

logEntry("CMD/ARGV0: ".$cmd);

	logEntry("Sending SERIAL COMMAND");
	logEntry("SERIAL DEVICE: ".$SERIAL_DEVICE);
        $serial = new phpSerial;

        $serial->deviceSet($SERIAL_DEVICE);
        $serial->confBaudRate($PROJECTOR_BAUD);
        $serial->confParity($PROJECTOR_PARITY);
        $serial->confCharacterLength($PROJECTOR_CHAR_BITS);
        $serial->confStopBits($PROJECTOR_STOP_BITS);
        $serial->deviceOpen();
	
		
	$serial->sendMessage("$cmd");
	sleep(1);
	logEntry("RETURN DATA: ".hex_dump($serial->readPort()));
	$serial->deviceClose();
	exit(0);
	

?>
