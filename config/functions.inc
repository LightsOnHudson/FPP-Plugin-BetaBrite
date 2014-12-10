<?php
function single_line_scroll ($combined, $scroller_color){

  include 'config/config.inc';
// Let's start the class
$serial = new phpSerial;
$serial->deviceSet($SERIAL_DEVICE);
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
