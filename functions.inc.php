<?php


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

				} else {
					$songTitle = $obj->{'title'};
					$songArtist = $obj->{'artist'};
					//	if($songArtist != "") {
					logEntry("Song Title: ".$songTitle." Artist: ".$songArtist);
					$messageToSend = $songTitle." - ".$songArtist;

				}

				sendLineMessage($messageToSend,$clearMessage);
			}
				
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

	global $default_color,$errno, $errstr, $cfgTimeOut;

	$STATION_ID = ReadSettingFromFile("STATION_ID",$pluginName);
	$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);
	$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
	$IP = ReadSettingFromFile("IP",$pluginName);
	$PORT = ReadSettingFromFile("PORT",$pluginName);
	$LOOPMESSAGE = ReadSettingFromFile("LOOPMESSAGE",$pluginName);
	$COLOR = ReadSettingFromFile("COLOR",$pluginName);
	$STATIC_TEXT_PRE = urldecode(ReadSettingFromFile("STATIC_TEXT_PRE",$pluginName));
	$STATIC_TEXT_POST = urldecode(ReadSettingFromFile("STATIC_TEXT_POST",$pluginName));




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