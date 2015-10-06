<?php

function single_line_scroll ($combined, $scroller_color){


       include 'config/config.inc';
include_once '/opt/fpp/www/config.php';
include_once '/opt/fpp/www/common.php';
global $settings, $pluginName;
// Let's start the class

logEntry("inside SINGLE LINE SCROLL");
$SERIAL_DEVICE="/dev/ttyUSB0";
logEntry("SERIAL DEVICE: ".$SERIAL_DEVICE);
//$pluginName = "BetaBrite";

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
//$pluginConfigFile = "/home/fpp/media/config" . "/plugin." .$pluginName;

logEntry("plugin config file: ".$pluginConfigFile);
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);

	$STATIC_TEXT_PRE = urldecode($pluginSettings['STATIC_TEXT_PRE']);
	$STATIC_TEXT_POST = urldecode($pluginSettings['STATIC_TEXT_POST']);
	$ENABLED = $pluginSettings['ENABLED'];
	$LOOPTIME = $pluginSettings['LOOPTIME'];
	$SEPARATOR = urldecode($pluginSettings['SEPARATOR']);
	
	logEntry("STATIC PRE: ".$STATIC_TEXT_PRE);
	logEntry("STATIC POST: ".$STATIC_TEXT_POST);
	

	if($STATIC_TEXT_PRE != "") {
		$combined = $STATIC_TEXT_PRE. " ".$SEPARATOR." ".$combined;
	}
	
	if($STATIC_TEXT_POST != "") {
		$combined = $combined ." ".$SEPARATOR." ".$STATIC_TEXT_POST;
	}


//$serial = new phpSerial;
//$serial->deviceSet($SERIAL_DEVICE);
//$serial->deviceOpen();
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


        //# Send the message to the sign.
        //$serial->sendMessage($INIT . "AA" . $DPOS . $ROTATE . $scroller_color . $combined .  $EOT);
        //$serial->sendMessage("$INIT" . "AA" . "$DPOS" . "$ROTATE" . "$scroller_color" . "$combined" .  "$EOT");
        ///# Modify the runlist.
       // $serial->sendMessage("$INIT" . "$WRITE_SPEC" . "\x2eSUA" .  "$EOT");

        //# Send the message to the sign.
       $CMD = $INIT . "AA" . $DPOS . $ROTATE . $scroller_color . $combined .  $EOT;
       $CMD .= "$INIT" . "AA" . "$DPOS" . "$ROTATE" . "$scroller_color" . "$combined" .  "$EOT";
        //# Modify the runlist.
        $CMD .= "$INIT" . "$WRITE_SPEC" . "\x2eSUA" .  "$EOT";
        //exec("php-cgi ./nextbus-route_stop_predictions.php route=" . $route['route_id'] . " 2>/dev/null >&- < &- >/dev/null &");
        
        $execCMD = "nohup /home/fpp/media/plugins/BetaBrite/BBOut.php ".$CMD." 2>/dev/null >&- < &- >/dev/null &";
        
        logEntry("EXEC CMD: ".$execCMD);
        exec($execCMD);
        
        //# Close filehandle.
        //close (BETABRITE);
        sleep(1);
        //logEntry("RETURN DATA: ".hex_dump($serial->readPort()));
      //  $serial->deviceClose();

        //# Wait for message to scroll off before returning.
        sleep($delay);
	logEntry("Sent message??");
}

function ip_single_line_scroll ($fs, $combined, $scroller_color){

        include 'config/config.inc';
$scroller_color="\x1c\x31"; //red

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


function hex_dump($data, $newline="\n")
{
  static $from = '';
  static $to = '';

  static $width = 16; # number of bytes per line

  static $pad = '.'; # padding for non-visible characters

  if ($from==='')
  {
    for ($i=0; $i<=0xFF; $i++)
    {
      $from .= chr($i);
      $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
    }
  }

  $hex = str_split(bin2hex($data), $width*2);
  $chars = str_split(strtr($data, $from, $to), $width);

$HEX_OUT ="";
  $offset = 0;
  foreach ($hex as $i => $line)
  {
    $HEX_OUT.= sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']';
    $offset += $width;
  }
return $HEX_OUT;
}

function escapeshellarg_special($file) {
	return "'" . str_replace("'", "'\"'\"'", $file) . "'";
}

//process sequence types

function processSequenceName($sequenceName) {
	logEntry("Sequence name: ".$sequenceName);
	
	$sequenceName = strtoupper($sequenceName);
	
	switch ($sequenceName) {
		
		case "BETABRITE-CLEAR.FSEQ":

		logEntry("Clear BetaBrite Sign");
		$messageToSend="";
		sendLineMessage($messageToSend,$clearMessage=TRUE);
			break;
			exit(0);
			
		default:
			logEntry("We do not support sequence name: ".$sequenceName." at this time");
			
			exit(0);
			
	}
	
}
function processCallback($argv) {

	global $DEBUG,$pluginName;
	
	$SEPARATOR = urldecode(ReadSettingFromFile("SEPARATOR",$pluginName));

	if($DEBUG)
		print_r($argv);
	//argv0 = program

	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data

	$registrationType = $argv[2];
	$data =  $argv[4];

	logEntry("PROCESSING CALLBACK");
	$clearMessage=FALSE;

	switch ($registrationType)
	{
		case "media":
			if($argv[3] == "--data")
			{
				$data=trim($data);
				logEntry("DATA: ".$data);
				$obj = json_decode($data);

				$type = $obj->{'type'};
				
				switch ($type) {
					
					case "sequence":
								
					//$sequenceName = ;
					processSequenceName($obj->{'Sequence'});
					
					break;
					case "media":
					
					logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
					
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
				
				
					$messageToSend = $songTitle." ".$SEPARATOR." ".$songArtist;
					logEntry("MESSAGE to send: ".$messageToSend);
					sendLineMessage($messageToSend,$clearMessage);

				break;
				case "both":
						
					logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
						
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
				
				
					$messageToSend = $songTitle." ".$SEPARATOR." ".$songArtist;
					logEntry("MESSAGE to send: ".$messageToSend);
					sendLineMessage($messageToSend,$clearMessage);
				
					break;
				
					default:
						logEntry("We do not understand: type: ".$obj->{'type'}. " at this time");
						exit(0);
						break;
						
				}

				
			}
				
			break;
			exit(0);
			
		default:
			exit(0);

	}

}

function logEntry($data) {

	global $logFile,$myPid;

	$data = $_SERVER['PHP_SELF']." : [".$myPid."] ".$data;
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
	fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
	fclose($logWrite);
}


//function send the message

function sendLineMessage($line,$clearMessage=FALSE) {

	global $default_color,$errno, $errstr, $cfgTimeOut,$pluginName,$pluginDirectory;
	
	$STATION_ID = urldecode(ReadSettingFromFile("STATION_ID",$pluginName));
	$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);
	$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
	$IP = ReadSettingFromFile("IP",$pluginName);
	$PORT = ReadSettingFromFile("PORT",$pluginName);
	$LOOPMESSAGE = ReadSettingFromFile("LOOPMESSAGE",$pluginName);
	$COLOR = ReadSettingFromFile("COLOR",$pluginName);
	$STATIC_TEXT_PRE = urldecode(ReadSettingFromFile("STATIC_TEXT_PRE",$pluginName));
	$STATIC_TEXT_POST = urldecode(ReadSettingFromFile("STATIC_TEXT_POST",$pluginName));
	$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);
	$LOOPTIME = ReadSettingFromFile("LOOPTIME",$pluginName);
	
	//FORCE
	$LOOPMESSAGE="YES";

logEntry("STATION_ID: ".$STATION_ID);
logEntry("DEVICE: ".$DEVICE);
logEntry("DEVICE_CONNECTION_TYPE: ".$DEVICE_CONNECTION_TYPE);
logEntry("IP: ".$IP);
logEntry("PORT: ".$PORT);
logEntry("LOOPMESSAGE: ".$LOOPMESSAGE);
logEntry("COLOR: ".$COLOR);
logEntry("LOOPTIME: ".$LOOPTIME);
logEntry("STATIC_TEXT_PRE: ".$STATIC_TEXT_PRE);
logEntry("STATIC_TEXT_POST: ".$STATIC_TEXT_POST);
logEntry("ENABLED: ".$ENABLED);
logEntry("LOOPTIME: ".$LOOPTIME);


logEntry("-------");
logEntry("Sending command");
logEntry("message to send: ".$line);

$BAUD_RATE=9600;
$STOP_BITS="1";
$CHAR_BITS="8";
$PARITY="none";
$scroller_color="\x1c\x31";

single_line_scroll($line, $scroller_color);	
}



//create sequence files
function createBetaBriteSequenceFiles() {
	global $betaBriteSequencePATH;
	$betaBriteSequenceFileClear = $betaBriteSequencePATH."/"."BETABRITE-CLEAR.FSEQ";

	$tmpFile = fopen($betaBriteSequenceFileClear, "w") or die("Unable to open file BetaBrite Settings File!");
	fclose($tmpFile);

}


//create script to randmomize
function createRandomizerScript() {


	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript;


	logEntry("Creating Randomizer script: ".$radioStationRepeatScriptFile);

	$data = "";
	$data  = "#!/bin/sh\n";
	$data .= "\n";
	$data .= "#Script to run randomizer\n";
	$data .= "#Created by ".$pluginName."\n";
	$data .= "#\n";
	$data .= "/usr/bin/php ".$randomizerScript."\n";


	$fs = fopen($radioStationRepeatScriptFile,"w");
	fputs($fs, $data);
	fclose($fs);

}

//crate the event file
function createRandomizerEventFile() {

	global $radioStationRepeatScriptFile,$pluginName,$randomizerScript,$radioStationRandomizerEventFile,$MAJOR,$MINOR,$radioStationRadomizerEventName;


	logEntry("Creating Randomizer event file: ".$radioStationRandomizerEventFile);

	$data = "";
	$data .= "majorID=".$MAJOR."\n";
	$data .= "minorID=".$MINOR."\n";

	$data .= "name='".$radioStationRadomizerEventName."'\n";

	$data .= "effect=''\n";
	$data .="startChannel=\n";
	$data .= "script='".pathinfo($radioStationRepeatScriptFile,PATHINFO_BASENAME)."'\n";



	$fs = fopen($radioStationRandomizerEventFile,"w");
	fputs($fs, $data);
	fclose($fs);
}
?>
