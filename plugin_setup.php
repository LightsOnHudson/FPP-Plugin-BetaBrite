<?php
//$DEBUG=true;
include_once "/opt/fpp/www/common.php";

$pluginName = "BetaBrite";
include_once "functions.inc.php";


$betaBriteSequencePATH  = $settings['sequenceDirectory'];

createBetaBriteSequenceFiles();

if(isset($_POST['submit']))
{
	$name = htmlspecialchars($_POST['station']);
	$device = htmlspecialchars($_POST['device']);
	$device_connection_type = htmlspecialchars($_POST['device_connection_type']);
	$rt_text_path= htmlspecialchars($_POST['rt_text_path']);
	$ip= htmlspecialchars($_POST['ip']);
	$port= htmlspecialchars($_POST['port']);
	$loopMessage= htmlspecialchars($_POST['loopMessage']);
	$color= htmlspecialchars($_POST['color']);
	$looptime = htmlspecialchars($_POST['looptime']);
	$static_text_pre = htmlspecialchars($_POST['static_text_pre']);
	$static_text_post = htmlspecialchars($_POST['static_text_post']);
	//echo "Station Id set to: ".$name;

	WriteSettingToFile("STATION_ID",trim($name),$pluginName);
	WriteSettingToFile("DEVICE",trim($deivce),$pluginName);
	WriteSettingToFile("DEVICE_CONNECTION_TYPE",trim($device_connection_type),$pluginName);
	WriteSettingToFile("IP",trim($ip),$pluginName);
	WriteSettingToFile("PORT",trim($port),$pluginName);
	WriteSettingToFile("LOOP_MESSAGE",trim($loopMessage),$pluginName);
	WriteSettingToFile("COLOR",trim($COLOR),$pluginName);
	WriteSettingToFile("LOOPTIME",trim($looptime),$pluginName);
	WriteSettingToFile("STATIC_TEXT_PRE",urlencode(trim($STATIC_TEXT_PRE),$pluginName));
	WriteSettingToFile("STATIC_TEXT_POST",urlencode(trim($STATIC_TEXT_POST),$pluginName));
	WriteSettingToFile("ENABLED",$_POST["ENABLED"],$pluginName);



} else {

	$STATION_ID = ReadSettingFromFile("STATION_ID",$pluginName);
	$DEVICE = ReadSettingFromFile("DEVICE",$pluginName);
	$DEVICE_CONNECTION_TYPE = ReadSettingFromFile("DEVICE_CONNECTION_TYPE",$pluginName);
	$IP = ReadSettingFromFile("IP",$pluginName);
	$PORT = ReadSettingFromFile("PORT",$pluginName);
	$LOOPMESSAGE = ReadSettingFromFile("LOOPMESSAGE",$pluginName);
	$COLOR = ReadSettingFromFile("COLOR",$pluginName);
	$STATIC_TEXT_PRE = urldecode(ReadSettingFromFile("STATIC_TEXT_PRE",$pluginName));
	$STATIC_TEXT_POST = urldecode(ReadSettingFromFile("STATIC_TEXT_POST",$pluginName));
	$ENABLED = ReadSettingFromFile("ENABLED",$pluginName);
	
	
	}
	

?>

<html>
<head>
</head>

<div id="rds" class="settings">
<fieldset>
<legend>BetaBrite Support Instructions</legend>

<p>Known Issues:
<ul>
<li>The Device SERIAL port is not remembered at this time to present to this screen. it is HOWEVER, saved in the file and will be used when click SAVE</li>
</ul>

<p>Configuration:
<ul>
<li>Configure your connection type, IP, Serial, Static text you want to send in front of Artist and song and post text, loop time if you want looping and color</li>
</ul>

<form method="post" action="http://<? echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=BetaBrite&page=plugin_setup.php">
echo "ENABLE PLUGIN: ";

if($ENABLED== 1 ) {
		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
	} else {
		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
}


echo "<p/> \n";
Manually Set Station ID<br>
<p><label for="station_ID">Station ID:</label>
<input type="text" value="<? if($STATION_ID !="" ) { echo $STATION_ID; } else { echo "";};?>" name="station" id="station_ID"></input>
(Expected format: up to 8 characters)
</p>

<?

echo "Connection type: \n";

echo "<select name=\"device_connection_type\"> \n";
                        if($DEVICE_CONNECTION_TYPE != "")
                        {
				switch ($DEVICE_CONNECTION_TYPE)
				{
					case "SERIAL":
                                		echo "<option selected value=\"".$DEVICE_CONNECTION_TYPE."\">".$DEVICE_CONNECTION_TYPE."</option> \n";
                                		echo "<option value=\"IP\">IP</option> \n";
                                		break;
					case "IP":
                                		echo "<option selected value=\"".$DEVICE_CONNECTION_TYPE."\">".$DEVICE_CONNECTION_TYPE."</option> \n";
                                		echo "<option value=\"SERIAL\">SERIAL</option> \n";
                        			break;
			
				
	
				}
	
			} else {

                                echo "<option value=\"SERIAL\">SERIAL</option> \n";
                                echo "<option value=\"IP\">IP</option> \n";
			}
                
        
echo "</select> \n";
echo "<p/> \n";

echo "<p/> \n";
echo "SERIAL DEVICE: \n";
echo "<select name=\"device\"> \n";
        foreach(scandir("/dev/") as $fileName)
        {
                if (preg_match("/^ttyUSB[0-9]+/", $fileName)) {
			if($DEVICE == $filename)
			{
                        	echo "<option selected value=\"".$fileName."\">".$fileName."</option> \n";
			} else {
                       		echo "<option value=\"".$fileName."\">".$fileName."</option> \n";
			}
                }
        }
echo "</select> \n";
?>

<p/>
IP: 
<input type="text" value="<? if($IP !="" ) { echo $IP; } else { echo "";}?>" name="ip" id="ip"></input>

<p/>

PORT:
<input type="text" value="<? if($PORT !="" ) { echo $PORT; } else { echo "";}?>" name="port" id="port"></input>

<p/>

STATIC TEXT PRE:
<input type="text" size="64" value="<? if($STATIC_TEXT_PRE !="" ) { echo $STATIC_TEXT_PRE; } else { echo "";}?>" name="static_text_pre" id="static_text_pre"></input>


<p/>

STATIC TEXT POST:
<input type="text" size="64" value="<? if($STATIC_TEXT_POST !="" ) { echo $STATIC_TEXT_POST; } else { echo "";}?>" name="static_text_post" id="static_text_post"></input>

<p/>

LOOP time (in secs):
<input type="text" value="<? if($LOOPTIME !="" ) { echo $LOOPTIME; } else { echo "10";}?>" name="looptime" id="looptime"></input>


<p/>
LOOP:
<?
echo "<select name=\"loopMessage\"> \n";

		switch ($LOOPMESSAGE) {

			case "YES":
				echo "<option selected value=\"".$LOOPMESSAGE."\">".$LOOPMESSAGE."</option> \n";
                echo "<option value=\"NO\">NO</option> \n";
            	break;

			case "NO":
				echo "<option selected value=\"".$LOOPMESSAGE."\">".$LOOPMESSAGE."</option> \n";
                echo "<option value=\"YES\">YES</option> \n";
                break;
                
			default:
                  echo "<option value=\"NO\">NO</option> \n";
                  echo "<option value=\"YES\">YES</option> \n";
				break;
				}
                
        
echo "</select> \n";
?>
<p/>
COLOR:
<?

//create an array of color here
echo "<select name=\"color\"> \n";
                      echo "<option value=\"YELLOW\">YELLOW</option> \n";
                      echo "<option value=\"GREEN\">GREEN</option> \n";
                      echo "<option value=\"RAINBOW\">RAINBOW</option> \n";


echo "</select> \n";
?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
</form>


<p>To report a bug, please file it against the BetaBrite plugin project on Git: https://github.com/LightsOnHudson/FPP-Plugin-BetaBrite

</fieldset>
</div>
<br />
</html>
