#!/bin/bash

target_file="2.mov"
/root/maintracking $target_file save || exit 1

ffmpeg -i Result.avi Results.webm
rm -f Result.avi

find . -type f -exec chmod 644 \{\} \;
find . -type d -exec chmod 755 \{\} \;

chown Results.webm --reference 2.mov

