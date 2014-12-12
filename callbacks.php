#!/usr/bin/php
<?
error_reporting(0);

//include '/opt/fpp/www/config.php';
//include_once("/opt/fpp/www/common.php");

//include_once("config.inc"); //'config.inc';

$settings=array();
$settings['mediaDirectory'] = "/home/pi/media";
$settings['logDirectory'] = "/home/pi/media/logs";

//include_once("signControl.inc");

$betaBriteSettingsFile = $settings['mediaDirectory']."/config/plugin.betabrite";


//arg0 is  the program
//arg1 is the first argument in the registration this will be --list
//$DEBUG=true;
$logFile = $settings['logDirectory']."/betabrite.log";
//$logFile = $logDirectory."/logs/betabrite.log";


logEntry("OPENING LOG FILE ".$logFile." but you know that, because you are seeing this now :) ");


$callbackRegisters = "media\n";
//var_dump($argv);
$lockFile =$settings['mediaDirectory']."/config/betabrite.loop";

$myProcessId = getmypid();


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

function escapeshellarg_special($file) {
  return "'" . str_replace("'", "'\"'\"'", $file) . "'";
}

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
				if($type == "sequence") {
					//we got a sequence... get the name
					
						$sequenceName = $obj->{'Sequence'};
						logEntry("Sequence name: ".$sequenceName);
						
						if(strtoupper($sequenceName) == "BETABRITE-CLEAR.FSEQ") {
							logEntry("Clear BetaBrite Sign");
							$messageToSend="";
							$clearMessage=TRUE;
							
						}
						
					exit(0);
				} else {
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
				//	if($songArtist != "") {
					logEntry("Song Title: ".$songTitle." Artist: ".$songArtist);
					$messageToSend = $sognTitle." - ".$songArtist;

				}
				
				sendLineMessage($messageToSend,$clearMessage);
			}
			exit(0);
			break;

	}

}

function logEntry($data) {

	global $logFile;

	$data = $_SERVER['PHP_SELF']." : ".$data;
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
		fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
		fclose($logWrite);
}


//function send the message

function sendLineMessage($line,$clearMessage=FALSE) {

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
                
                $configParts=explode("=",$settingParts[7]);
                $LOOPTIME= trim($configParts[1]);
                
                $configParts=explode("=",$settingParts[8]);
                $STATIC_TEXT_PRE= trim($configParts[1]);
                
                $configParts=explode("=",$settingParts[9]);
                $STATIC_TEXT_POST= trim($configParts[1]);
                
                
        }
        
        


     //   logEntry("reading config file");
        logEntry("Station_ID: ".$STATION_ID." DEVICE: ".$DEVICE." DEVICE_CONNECTION_TYPE: ".$DEVICE_CONNECTION_TYPE." IP: ".$IP. " PORT: ".$PORT." LOOPMESSAGE: ".$LOOPMESSAGE." STATIC TEXT PRE: ".$STATIC_TEXT_PRE. " STATIC TEXT POST: ".$STATIC_TEXT_POST." LOOP TIME: ".$LOOPTIME."  Color: ".$cl_color);


	logEntry("Sending Message to sign Looper: LOOP: ".$LOOPMESSAGE);
	
	
	
	//add pre and post text if they are here
	
	if($STATIC_TEXT_PRE != "") {
		$newLine = $STATIC_TEXT_PRE." ".$line;
		$line = $newLine;
	}
	
	if($STATIC_TEXT_POST != "") {
		$line .= " ".$STATIC_TEXT_POST;
	}

	
	logEntry("INSIDE SEND");
	//# Send line to scroller
	$cmd = "/opt/fpp/plugins/BetaBrite/alphasign";
	
	
	switch ($DEVICE_CONNECTION_TYPE) {
		
		case "SERIAL":
			$DEVICE=$DEVICE;
			$cmd .= "Serial ";
			
			break;
			
		case "IP":
			$DEVICE="\"".$IP.":".$PORT."\"";
			//$DEVICE=$IP;
			//$DEVICE = $IP." ".$PORT;
			$cmd .= "IP "; //set the name to call
	}
	
	//$line = "TEST - TEST";
	//$cmd .= "\"".$line."\"";
	
//	$cmd .= " ".$DEVICE;
	
	//process the clear sequence event //loop. but just send clear
	if($clearMessage)  {
		$LOOPMESSAGE="YES";
		$line="";
	}
	
	logEntry("COMMAND clearmessage: ".$clearMessage. " CMD: ".$cmd."\"".$line."\" ".$DEVICE);
	system($cmd."\"".$line."\" ".$DEVICE,$output);
	
	if($LOOPMESSAGE == "NO") {
		logEntry("no looping: sending clear line");
		//send a blank line after a few seconds
		sleep(30);
		
		$line = "";
		logEntry("COMMAND CMD: ".$cmd."\"".$line."\" ".$DEVICE);
		system($cmd."\"".$line."\" ".$DEVICE,$output);
		
		
	}
		
exit(0);			
}
?>
