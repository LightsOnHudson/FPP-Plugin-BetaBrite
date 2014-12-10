#!/bin/bash
cd /tmp
/bin/mkdir /opt/fpp/plugins/BetaBrite
/usr/bin/wget http://50.194.140.209/fpp/plugins/BetaBrite/BetaBrite.zip
/usr/bin/unzip BetaBrite.zip -d /opt/fpp/plugins/BetaBrite
/bin/rm /tmp/BetaBrite.zip
/bin/chmod +x /opt/fpp/plugins/BetaBrite/callbacks.php
