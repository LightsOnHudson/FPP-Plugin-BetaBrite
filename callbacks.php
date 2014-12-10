#!/usr/bin/php
<?
error_reporting(0);
include 'config.inc';

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

function escapeshellarg_special($file) {
  return "'" . str_replace("'", "'\"'\"'", $file) . "'";
}

function processCallback($argv) {

        $cmd = "/usr/bin/killall -9 signLoop.php";
        system($cmd,$output);

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
				if($songArtist != "") {
					logEntry("Song Title: ".$songTitle." Artist: ".$songArtist);
					sendLineMessage($songTitle,$songArtist);
					} else {
					logEntry("No Song title or artist: was this an event type maybe");
					exit(0);
					}
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

function sendLineMessage($songTitle,$songArtist) {

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

        logEntry("reading config file");
        logEntry("Station_ID: ".$STATION_ID." DEVICE: ".$DEVICE." DEVICE_CONNECTION_TYPE: ".$DEVICE_CONNECTION_TYPE." IP: ".$IP. " PORT: ".$PORT." LOOPMESSAGE: ".$LOOPMESSAGE." STATIC TEXT PRE: ".$STATIC_TEXT_PRE. " STATIC TEXT POST: ".$STATIC_TEXT_POST." LOOP TIME: ".$LOOPTIME."  Color: ".$cl_color);


	logEntry("Sending Message to sign Looper: LOOP: ".$LOOPMESSAGE);
	
	$line = $songTitle. " - ".$songArtist;
			$cmd = "/usr/bin/killall -9 signLoop.php";
			system($cmd,$output);
	switch ($LOOPMESSAGE) {


		case "YES":

			sleep(1);
			do {
				$cmd = "/usr/bin/php /opt/fpp/plugins/BetaBrite/signLoop.php ".escapeshellarg_special($line);
				system($cmd,$output);
				sleep($LOOPTIME);
			} while ($LOOPMESSAGE=="YES");
			break;


		case "NO":
			//send once
			$cmd = "/usr/bin/php /opt/fpp/plugins/BetaBrite/signLoop.php ".escapeshellarg_special($line);
                        system($cmd,$output);	
			break;


	}
}
?>
