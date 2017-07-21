#!/bin/bash

target_file="/root/sharefolder/1.jpg"
sortie="/root/sharefolder/output"
mkdir $sortie
cd $sortie

rm -rf /root/sharefolder/output/Result_??_?_??

/root/main_Vertebrae $target_file save 

find $sortie -type f -exec chmod 644 \{\} \;
find $sortie -type d -exec chmod 755 \{\} \;
#mv -rf /root/Result_??_?_?? /root/sharefolder/output/

chown -R $sortie --reference $target_file