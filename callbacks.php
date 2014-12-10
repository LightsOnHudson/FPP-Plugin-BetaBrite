#!/usr/bin/php
<?
error_reporting(0);
include 'config/config.inc';

$betaBriteSettingsFile = "/home/pi/media/plugins/betabrite.settings";
include 'php_serial.class.php';
//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = "/home/pi/media/logs/betabrite.log";

$callbackRegisters = "media\n";
//var_dump($argv);
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

	global $betaBriteSettingsFile,$default_color,$errno, $errstr, $cfgTimeOut;
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
		//# Send line to scroller
		single_line_scroll($line, $scroller_color,$DEVICE);
		break;

	
	}	

}

function ip_single_line_scroll ($fs, $combined, $scroller_color){

	include 'config/config.inc';
	// Let's start the class

	//      # =-=-= Start of character counting =-=-=
	//      # Added to the end of the message will be blank characters representing the length
	//      # of the display. This is so we can calculate how long it will take the message
	//      # to completely scroll off the end of the sign.
	//      # To calulate it correctly, the blanks have to be actual characters, which will
	//      #  be changed to blanks after it creates $combined2.

	$end="                 ";
	//        $end="XXXXXXXXXXXXXXXX|";     //# Wanna see the end? Uncomment this one

	if ( $combined != "" ) {
		$combined2 = $combined . $end;//        # Fake message for figuring out delay

		$combined = $combined . $end;// # Actual message to be sent
	} else {
		$combined2="";
	}



	//# reset the counter
	$char_count=0;

	//# Count the characters
	$char_count = strlen($combined2);

	# Create the delay
	$delay=($char_count*$delay_per_char);

	//echo "delay: ".$delay."\n";
	//#=-=-= End of character counting =-=-=

	//echo "sending message: ".$combined."<br/> \n";

	//# Send the message to the sign.
	// fputs($fs, $INIT . "AA" . $DPOS . $ROTATE . $scroller_color . $combined .  $EOT);
	fputs($fs,"$INIT" . "AA" . "$DPOS" . "$ROTATE" . "$scroller_color" . "$combined" .  "$EOT");
	//# Modify the runlist.
	fputs($fs,"$INIT" . "$WRITE_SPEC" . "\x2eSUA" .  "$EOT");

	//# Close filehandle.

	//        fclose($fs);

	//# Wait for message to scroll off before returning.

	//return the delay for the rest of the program to continue before sending next messag
	return $delay;

}
function single_line_scroll ($combined, $scroller_color,$DEVICE){

	
	// Let's start the class
	$serial = new phpSerial;
	$serial->deviceSet($DEVICE);
	$serial->deviceOpen();
	//      # =-=-= Start of character counting =-=-=
	//      # Added to the end of the message will be blank characters representing the length
	//      # of the display. This is so we can calculate how long it will take the message
	//      # to completely scroll off the end of the sign.
	//      # To calulate it correctly, the blanks have to be actual characters, which will
	//      #  be changed to blanks after it creates $combined2.

	//        $serial->sendMessage("$INIT" . "$WRITE_SPEC" . "\x24" . "AAU00FFFFFE" . "UDU07114000" . "DDU07114000" . "$EOT");
	//       $serial->sendMessage("$INIT" . "$WRITE_DOT" . "U" . "0711" . "00000000000\r00000200000\r00002220000\r00022222000\r00222222200\r02222222220\r00000000000\r" . "$EOT");

	//     $serial->sendMessage("$INIT" . "$WRITE_DOT" . "D" . "0711" . "00000000000\r01111111110\r00111111100\r00011111000\r00001110000\r00000100000\r00000000000\r" . "$EOT");

	//$end="XXXXXXXXXXXXXXXXX";
	$end="                 ";
	//        $end="XXXXXXXXXXXXXXXX|";     //# Wanna see the end? Uncomment this one

	if ( $combined != "" ) {
		$combined2 = $combined . $end;//        # Fake message for figuring out delay

		$combined = $combined . $end;// # Actual message to be sent
	} else {
		$combined2="";
	}


	//# reset the counter
	$char_count=0;

	//# Count the characters
	$char_count = strlen($combined2);

	# Create the delay
	$delay=($char_count*$delay_per_char);

	//echo "delay: ".$delay."\n";
	//#=-=-= End of character counting =-=-=

	//echo "delay: ".$delay."\n";
	//#=-=-= End of character counting =-=-=

	//echo "sending message: ".$combined."<br/> \n";

	//# Send the message to the sign.
	// fputs($fs, $INIT . "AA" . $DPOS . $ROTATE . $scroller_color . $combined .  $EOT);
	fputs($fs,"$INIT" . "AA" . "$DPOS" . "$ROTATE" . "$scroller_color" . "$combined" .  "$EOT");
	//# Modify the runlist.
	fputs($fs,"$INIT" . "$WRITE_SPEC" . "\x2eSUA" .  "$EOT");

	//# Close filehandle.

	//        fclose($fs);

	//# Wait for message to scroll off before returning.

	//return the delay for the rest of the program to continue before sending next messag
	return $delay;

}
?>
