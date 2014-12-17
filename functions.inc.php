<?php


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

	global $logFile;

	$data = $_SERVER['PHP_SELF']." : ".$data;
	$logWrite= fopen($logFile, "a") or die("Unable to open file!");
	fwrite($logWrite, date('Y-m-d h:i:s A',time()).": ".$data."\n");
	fclose($logWrite);
}


//function send the message

function sendLineMessage($line,$clearMessage=FALSE) {

	global $default_color,$errno, $errstr, $cfgTimeOut,$pluginName;
	
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

	

	//add pre and post text if they are here

	if($STATIC_TEXT_PRE != "") {
		$newLine = $STATIC_TEXT_PRE." ".$line;
		$line = $newLine;
	}

	if($STATIC_TEXT_POST != "") {
		$line .= " ".$STATIC_TEXT_POST;
	}



	//# Send line to scroller
	$cmd = "/opt/fpp/plugins/BetaBrite/alphasign";

	$cmd .= $DEVICE_CONNECTION_TYPE. " ";

	switch ($DEVICE_CONNECTION_TYPE) {

		case "SERIAL":
			$DEVICE=$DEVICE;
				
				
			break;
				
		case "IP":
			$DEVICE="\"".$IP.":".$PORT."\"";
			//$DEVICE=$IP;
			//$DEVICE = $IP." ".$PORT;
				
	}

	//process the clear sequence event //loop. but just send clear
	if($clearMessage)  {
		$LOOPMESSAGE="YES";
		$line="";
	}

	logEntry("SENDING COMMAND: ".$cmd."\"".$line."\" ".$DEVICE);
	system($cmd."\"".$line."\" ".$DEVICE,$output);

//	if($LOOPMESSAGE == "NO") {
//		logEntry("no looping: sending clear line");
//		//send a blank line after a few seconds
//		sleep(30);

//		$line = "";
//		logEntry("COMMAND CMD: ".$cmd."\"".$line."\" ".$DEVICE);
//		system($cmd."\"".$line."\" ".$DEVICE,$output);


//	}
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