<?php

function single_line_scroll ($combined, $scroller_color){


global $settings, $pluginName, $DEBUG,$BAUD,$PARITY,$CHAR_BITS,$STOP_BITS,$DEVICE,$DEVICE_CONNECTION_TYPE;
include_once 'php_serial.class.php';

// Let's start the class

$pluginConfigFile=$settings['configDirectory']."/"."plugin.".$pluginName;
if($DEBUG) {
	logEntry("DEBUG: inside SINGLE LINE SCROLL");
	logEntry("DEBUG: pluginname: ".$pluginName);

}
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
       // exec($execCMD);//$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);

	if($DEBUG)
		logEntry("Device_connection_type: ".$DEVICE_CONNECTION_TYPE);


switch($DEVICE_CONNECTION_TYPE) {

	case "SERIAL":

		logEntry("Sending SERIAL COMMAND");
		logEntry("SERIAL DEVICE: ".$SERIAL_DEVICE);
        $serial = new phpSerial;


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
?>
