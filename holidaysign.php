<?php
error_reporting(0);
require 'config/config.inc';
include 'config/functions.inc';

include 'config/php_serial.class.php';

$cfgServer = "192.168.192.239";


if(isset($_GET['color'])) {
	$cl_color = $_GET['color'];
} else {
	$cl_color = "";
}

        if ( $cl_color == "" ) {
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


// Let's start the class
//$serial = new phpSerial;

//$serial->deviceSet($SERIAL_DEVICE);
//$serial->deviceOpen();


//# Send line to scroller
//single_line_scroll($line, $scroller_color);

$fs = fsockopen($cfgServer, $cfgPort, $errno, $errstr, $cfgTimeOut);

if(!$fs) {
	echo "Error connecting to sign";
	
}

$delay = ip_single_line_scroll($fs, $line, $scroller_color);

sleep($delay);
//send a blank line to clear out last message
$line = "";
ip_single_line_scroll($fs, $line, $scroller_color);
fclose($fs);


?>
