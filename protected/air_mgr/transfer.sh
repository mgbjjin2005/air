#!/bin/bash
ftp -n<<!
open 10.0.0.1
user king ***King1985***
binary
hash
cd /bw
lcd /home/air_data
prompt
put $1 $1
close
bye
!
