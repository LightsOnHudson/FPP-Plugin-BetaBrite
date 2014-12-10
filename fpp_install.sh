#!/bin/bash

/bin/mkdir /opt/fpp/plugins/BetaBrite
cd /opt/fpp/plugins/BetaBrite/
/usr/bin/wget http://50.194.140.209/fpp/plugins/BetaBrite/BetaBrite.zip
/usr/bin/unzip BetaBrite.zip
rm BetaBrite.zip
/usr/bin/chmod +x /opt/fpp/plugins/BetaBrite/callbacks.php
