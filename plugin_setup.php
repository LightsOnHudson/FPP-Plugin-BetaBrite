<?php
//$DEBUG=true;
$miniRDSSettingsFile = $settings['mediaDirectory']."/config/plugin.betabrite";
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

		$betaBriteSettings = fopen($betaBriteSettingsFile, "w") or die("Unable to open file!");
		$txt = "STATION_ID=".trim($name)."\r\n";
		$txt .= "DEVICE=".$device."\r\n";
		$txt .= "DEVICE_CONNECTION_TYPE=".$device_connection_type."\r\n";

		$txt .= "IP=".trim($ip)."\r\n";
		$txt .= "PORT=".trim($port)."\r\n";
		$txt .= "LOOP_MESSAGE=".$loopMessage."\r\n";
		$txt .= "COLOR=".$color."\r\n";
		$txt .= "LOOPTIME=".trim($looptime)."\r\n";
		$txt .= "STATIC_TEXT_PRE=".trim($static_text_pre)."\r\n";
		$txt .= "STATIC_TEXT_POST=".trim($static_text_post)."\r\n";
		fwrite($betaBriteSettings, $txt);
		fclose($betaBriteSettings);
		$STATION_ID=$name;
		$DEVICE=$device;
		$DEVICE_CONNECTION_TYPE=$device_connection_type;
		$IP =$ip;
		$PORT =$port;
		$LOOPMESSAGE=$loopMessage;
		$COLOR = $color;
		$STATIC_TEXT_PRE=$static_text_pre;
		$STATIC_TEXT_POST=$static_text_post;

	//add the ability for GROWL to show changes upon submit :)
	//	$.jGrowl("Station Id: $STATION_ID");	
        
  
 

} else {

	if($DEBUG)
		echo "READING FILE: <br/> \n";
	//try to read the settings file if available


	if (file_exists($betaBriteSettingsFile)) {
		$filedata=file_get_contents($betaBriteSettingsFile);
	} 
	
	if($filedata !="" )
	{
		$settingParts = explode("\r",$filedata);
		$configParts=explode("=",$settingParts[0]);
		$STATION_ID = $configParts[1];
		
		$configParts=explode("=",$settingParts[1]);
		$DEVICE = $configParts[1];
		
		$configParts=explode("=",$settingParts[2]);
		$DEVICE_CONNECTION_TYPE = $configParts[1];
	
		$configParts=explode("=",$settingParts[3]);
		$IP = $configParts[1];
	
		$configParts=explode("=",$settingParts[4]);
		$PORT = $configParts[1];

                $configParts=explode("=",$settingParts[5]);
                $LOOPMESSAGE = $configParts[1];

		$configParts=explode("=",$settingParts[6]);
                $COLOR= $configParts[1];
           $configParts=explode("=",$settingParts[7]);
          $LOOPTIME= $configParts[1];

          $configParts=explode("=",$settingParts[8]);
          $STATIC_TEXT_PRE = $configParts[1];
          
          $configParts=explode("=",$settingParts[9]);
          $STATIC_TEXT_POST = $configParts[1];
                
	}
	fclose($file_handle);

}
        if($DEBUG) {
		echo "STATION: ".$STATION_ID."<br/> \n";
		echo "DEVICE: ".$DEVICE."<br/> \n";
		
                echo "IP: ".$IP."<br/> \n";
                echo "PORT: ".$PORT."<br/> \n";
                echo "DEVICE CONNECTION TYPE: ".$DEVICE_CONNECTION_TYPE."<br/> \n";
                echo "LOOP MESSAGE: ".$LOOPMESSAGE."<br/> \n";
                echo "COLOR: ".$COLOR."<br/> \n";
                echo "LOOP TIME: ".$LOOPTIME."<br/> \n";
                echo "STATIC TEXT PRE: ".$STATIC_TEXT_PRE."<br/> \n";
                echo "STATIC TEXT POST: ".$STATIC_TEXT_POST."<br/> \n";
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
<li>NONE</li>
</ul>

<p>Configuration:
<ul>
<li>Configure your connection type, IP, Serial, Static text you want to send in front of Artist and song and post text, loop time if you want looping and color</li>
</ul>

<form method="post" action="http://<? echo $_SERVER['SERVER_NAME']?>/plugin.php?plugin=BetaBrite&page=plugin_setup.php">
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
			if($device == $filename)
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
