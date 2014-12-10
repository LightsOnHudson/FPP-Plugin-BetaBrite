<?php
# These can stay unchanged
$cfgTimeOut=10;
$default_color="\x1c\x32"; //     # Green. Codes in sub start_up
$delay_per_char=.12; 
$sign_type="Z";    //     # Z=all signs ( See protocol doc if you have more than one sign on network
$sign_address="00"; //    # 00=broadcast, 01=sign 01, 02=sign 02, etc
//        # Set our variables
        $NUL            = "\0\0\0\0\0\0"; //      # Send 6 nulls for wake up sign and set baud
        $SOH            = "\x01";          //     # Start of header - NEVER CHANGES
        $TYPE           = "$sign_type";     //    # Z = All signs. See Protocol doc for more info
        $SIGN_ADDR      = "$sign_address";   //   # 00 = broadcast, 01 = sign address 1, etc
        $STX            = "\x02";             //  # Start of Text character - NEVER CHANGES
        $EOT            = "\004";             //  # End of transmission

       // # All above combined to make life easier
        $INIT="$NUL$SOH$TYPE$SIGN_ADDR$STX";

        $WRITE          ="A";             //      # Write TEXT file
        $WRITE_SPEC     ="E";              //     # Write SPECIAL FUNCTION file
        $WRITE_DOT      ="I"; //# Write DOT file

        $CALL_DOT       ="\x14"; //# Call dot file. Must be followed by DOTS PICTURE File label.


        $DPOS           ="\x1b\x20";  //          # Set for BetaBrite one line sign
        $ROTATE         ="\x61";       //         # Message travels right to left.

        $FONT1          = "\x1a\x31"; //# Five high standard
        $FONT2          = "\x1a\x33"; //# seven high standard

?>
