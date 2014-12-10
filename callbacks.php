#!/usr/bin/php
<?
error_reporting(0);
//require 'config/config.inc';
include 'config/functions.inc';
$betaBriteSettingsFile = "/home/pi/media/plugins/betabrite.settings";
include 'config/php_serial.class.php';
//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = "/home/pi/media/logs/betabrite.log";

$callbackRegisters = "media\n";

switch ($argv[1])
	{
		case "--list":
			echo $callbackRegisters;
			logEntry("FPPD List Registration request: responded:". $callbackRegisters);
			exit(0);

		case "--type":
			//we got a register request message from the daemon
			processCallback($argv);	
			break;

		default:
			logEntry($argv[0]." called with no parameteres");
			break;	
	}
exit(0);

function processCallback($argv) {

	global $DEBUG;

	if($DEBUG)
		print_r($argv);
	//argv0 = program
		
	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data

	$registrationType = $argv[2];
	$data =  $argv[4];

	logEntry($registrationType . " registration requestion from FPPD daemon");

	switch ($registrationType) 
	{
		case "media":
			if($argv[3] == "--data")
			{
				$data=trim($data);
				logEntry("DATA: ".$data);
				$obj = json_decode($data);
				$songTitle = $obj->{'title'};
				$songArtist = $obj->{'artist'};
				logEntry("Song Title: ".$songTitle." Artist: ".$songArtist);

				sendMessage($songTitle,$songArtist);

			}	
		break;

	}

}

function logEntry($data) {

	global $logFile;

	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
		fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
		fclose($logWrite);
}


//function send the message

function sendMessage($songTitle,$songArtist) {

	global $betaBriteSettingsFile;
	if (file_exists($betaBriteSettingsFile)) {
		$filedata=file_get_contents($betaBriteSettingsFile);
	} else {
		logEntry("BetaBriteSettings File does not exist, configure plugin first");
		exit(0);
	}
	if($filedata !="" )
	{
		$settingParts = explode("\r",$filedata);
		$configParts=explode("=",$settingParts[0]);
		$STATION_ID = $configParts[1];
		
		$configParts=explode("=",$settingParts[1]);
		$DEVICE = "/dev/".$configParts[1];
		
		$configParts=explode("=",$settingParts[2]);
		$DEVICE_CONNECTION_TYPE = $configParts[1];
	
		$configParts=explode("=",$settingParts[3]);
		$IP = $configParts[1];
	
		$configParts=explode("=",$settingParts[4]);
		$PORT = $configParts[1];

                $configParts=explode("=",$settingParts[5]);
                $LOOPMESSAGE = $configParts[1];

                $configParts=explode("=",$settingParts[6]);
                $cl_color= trim(strtolower($configParts[1]));
	}
	fclose($file_handle);

	logEntry("reading config file");
	logEntry("Station_ID: ".$STATION_ID." DEVICE: ".$DEVICE." DEVICE_CONNECTION_TYPE: ".$DEVICE_CONNECTION_TYPE." IP: ".$IP. " PORT: ".$PORT." LOOPMESSAGE: ".$LOOPMESSAGE." Color: ".$cl_color);


        if ( $cl_color== "" ) {
                $scroller_color="$default_color";
        } else {

                if ( $cl_color == "red" ) { $scroller_color="\x1c\x31"; }
                elseif ( $cl_color == "green" ) { $scroller_color="\x1c\x32"; }
                elseif ( $cl_color == "amber" ) { $scroller_color="\x1c\x33"; }
                elseif ( $cl_color == "darkred" ) { $scroller_color="\x1c\x34"; }
                elseif ( $cl_color == "darkgreen" ) { $scroller_color="\x1c\x35"; }
                elseif ( $cl_color == "brown" ) { $scroller_color="\x1c\x36"; }
                elseif ( $cl_color == "orange" ) { $scroller_color="\x1c\x37"; }
                elseif ( $cl_color == "yellow" ) { $scroller_color="\x1c\x38"; }
                elseif ( $cl_color == "rainbow1" ) { $scroller_color="\x1c\x39"; }
                elseif ( $cl_color == "rainbow2" ) { $scroller_color="\x1c\x41"; }
                elseif ( $cl_color == "mix" ) { $scroller_color="\x1c\x42"; }
                elseif ( $cl_color == "auto" ) { $scroller_color="\x1c\x43"; }
        }


$line = $songTitle." - ".$songArtist;

switch ($DEVICE_CONNECTION_TYPE) {

	case "IP":
		$fs = fsockopen($IP, $PORT, $errno, $errstr, $cfgTimeOut);
		if(!$fs) {
			logEntry( "Error connecting to sign");
			exit(0);
		}

		$delay = ip_single_line_scroll($fs, $line, $scroller_color);
		sleep($delay);
		//send a blank line to clear out last message
		$line = "";
		ip_single_line_scroll($fs, $line, $scroller_color);
		fclose($fs);

		break;

	case "SERIAL":

		// Let's start the class
		$serial = new phpSerial;
		
		$serial->deviceSet($DEVICE);
		$serial->deviceOpen();
		
		
		//# Send line to scroller
		single_line_scroll($line, $scroller_color);
		break;

	
	}	

}
?>
