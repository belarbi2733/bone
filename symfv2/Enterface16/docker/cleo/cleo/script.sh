#!/bin/bash

cd /home

[ -f "BMDvalues1.txt" ] || exit 1
[ -f "Phantom1.txt" ] || exit 2
[ -f "fichier0.dcm" ] || exit 3

xvfb-run java -jar /root/cleo.jar /home/fichier0.dcm /home/BMDvalues1.txt /home/Phantom1.txt

exit 0;

target_file="../2.avi"
mkdir -p output
cd output
/root/maintracking $target_file save || exit 1

ffmpeg -i Result.mpeg Results.webm
rm -f Result.mpeg

cd ..
find output -type f -exec chmod 644 \{\} \;
find output -type d -exec chmod 755 \{\} \;

chown -R output --reference 2.avi

