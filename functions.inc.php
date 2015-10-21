<?php

function single_line_scroll ($combined, $scroller_color){


global $settings, $pluginName, $DEBUG;

include_once 'php_serial.class.php';

// Let's start the class

if($DEBUG)
	logEntry("DEBUG: inside SINGLE LINE SCROLL");


$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
//
//
//config inc
$default_color="\x1c\x32"; //     # Green. Codes in sub start_up
$delay_per_char=.12; 
$sign_type="Z";    //     # Z=all signs ( See protocol doc if you have more than one sign on network
$sign_address="00"; //    # 00=broadcast, 01=sign 01, 02=sign 02, etc

//        # Set our variables
        $NUL            = "\0\0\0\0\0\0"; //      # Send 6 nulls for wake up sign and set baud
        $SOH            = "\x01";          //     # Start of header - NEVER CHANGES
        $TYPE           = "$sign_type";     //    # Z = All signs. See Protocol doc for more info
        $SIGN_ADDR      = "$sign_address";   //   # 00 = broadcast, 01 = sign address 1, etc
        $STX            = "\x02";             //  # Start of Text character - NEVER CHANGES
        $EOT            = "\004";             //  # End of transmission

       // # All above combined to make life easier
        $INIT="$NUL$SOH$TYPE$SIGN_ADDR$STX";

        $WRITE          ="A";             //      # Write TEXT file
        $WRITE_SPEC     ="E";              //     # Write SPECIAL FUNCTION file
        $WRITE_DOT      ="I"; //# Write DOT file

        $CALL_DOT       ="\x14"; //# Call dot file. Must be followed by DOTS PICTURE File label.


        $DPOS           ="\x1b\x20";  //          # Set for BetaBrite one line sign
        $ROTATE         ="\x61";       //         # Message travels right to left.

        $FONT1          = "\x1a\x31"; //# Five high standard
        $FONT2          = "\x1a\x33"; //# seven high standard

if($DEBUG)
	logEntry("DEBUG: plugin config file: ".$pluginConfigFile);

if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);

	$STATIC_TEXT_PRE = urldecode($pluginSettings['STATIC_TEXT_PRE']);
	$STATIC_TEXT_POST = urldecode($pluginSettings['STATIC_TEXT_POST']);
	$ENABLED = $pluginSettings['ENABLED'];
	$LOOPTIME = $pluginSettings['LOOPTIME'];
	$SEPARATOR = urldecode($pluginSettings['SEPARATOR']);
	$DEVICE = $pluginSettings['DEVICE'];
	
	//$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
	$DEVICE_CONNECTION_TYPE = $pluginSettings['DEVICE_CONNECTION_TYPE'];

	$SERIAL_DEVICE="/dev/".$DEVICE;
	
	if($DEBUG){
		logEntry("DEBUG: STATIC PRE: ".$STATIC_TEXT_PRE);
		logEntry("DEBUG: STATIC POST: ".$STATIC_TEXT_POST);
		logEntry("DEBUG: SEPARATOR: ".$SEPARATOR);
	}

	if($STATIC_TEXT_PRE != "") {
		$combined = $STATIC_TEXT_PRE. " ".$SEPARATOR." ".$combined;
	}
	
	if($STATIC_TEXT_POST != "") {
		$combined = $combined ." ".$SEPARATOR." ".$STATIC_TEXT_POST;
	}

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

      

        //# Send the message to the sign.
       $CMD = $INIT . "AA" . $DPOS . $ROTATE . $scroller_color . $combined .  $EOT;
       $CMD .= "$INIT" . "AA" . "$DPOS" . "$ROTATE" . "$scroller_color" . "$combined" .  "$EOT";
        //# Modify the runlist.
        $CMD .= "$INIT" . "$WRITE_SPEC" . "\x2eSUA" .  "$EOT";

        if($DEBUG)
        logEntry("DEBUG: EXEC CMD: ".$CMD);
     

	if($DEBUG)
		logEntry("Device_connection_type: ".$DEVICE_CONNECTION_TYPE);


switch($DEVICE_CONNECTION_TYPE) {

	case "SERIAL":

		logEntry("Sending SERIAL COMMAND");
		logEntry("SERIAL DEVICE: ".$SERIAL_DEVICE);
        $serial = new phpSerial;

       $BAUD = "9600";
	$PARITY="none";
	$CHAR_BITS="8";
	$STOP_BITS="1";

	if($DEBUG) {
		logEntry("DEBUG: BAUD: ".$BAUD);
		logEntry("DEBUG: CHAR BITS: ".$CHAR_BITS);
		logEntry("DEBUG: STOP BITS: ".$STOP_BITS);
		logEntry("DEBUG: PARITY: ".$PARITY);

	}
 
	$serial->deviceSet($SERIAL_DEVICE);
        $serial->confBaudRate($BAUD);
        $serial->confParity($PARITY);
        $serial->confCharacterLength($CHAR_BITS);
        $serial->confStopBits($STOP_BITS);
        $serial->deviceOpen();
	
		
		$serial->sendMessage("$CMD");
		sleep(1);
		logEntry("RETURN DATA: ".hex_dump($serial->readPort()));
		$serial->deviceClose();
		

        exit(0);
        break;
        
	case "IP":
		
		break;
		
	default:
		break;
}
		
	
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

	//if($DEBUG)
		//print_r($argv);
	//argv0 = program

	//argv2 should equal our registration // need to process all the rgistrations we may have, array??
	//argv3 should be --data
	//argv4 should be json data

	$registrationType = $argv[2];

	if($DEBUG)
	logEntry("registration type: ".$registrationType);
	
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
					if($DEBUG)
					logEntry("MESSAGE to send: ".$messageToSend);
					sendLineMessage($messageToSend,$clearMessage);

				break;
				case "both":
						
					logEntry("MEDIA ENTRY: EXTRACTING TITLE AND ARTIST");
						
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
				
				
					$messageToSend = $songTitle." ".$SEPARATOR." ".$songArtist;
					if($DEBUG)
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

	global $DEBUG;
	if($DEBUG)
	logEntry("inside Send Line message");
	
	$scroller_color="\x1c\x31";

	if($DEBUG)
		logEntry("Default scroller collor: ".$scroller_color);
	single_line_scroll($line, $scroller_color);	
	
	if($DEBUG)
	logEntry("Leaving SendLine Message");
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
