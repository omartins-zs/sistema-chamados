#!/bin/sh
php -r '$s=@fsockopen("127.0.0.1",9000,$e,$t,1); if($s){fclose($s); exit(0);} exit(1);'
