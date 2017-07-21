#!/bin/bash

genpasswd() {
  local length=${1:-20}
  tr -dc A-Za-z0-9_ < /dev/urandom | head -c ${length} | xargs
}

export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

hadoop-fuse-dfs dfs://10.142.0.4 /mnt

[ -d /mnt ] || exit 1;

cd /home

ls -lR 

touch userid.txt_missing
[ -f userid.txt ] || exit 3;
rm userid.txt_missing

touch resultid.txt_missing
[ -f resultid.txt ] || exit 4;
rm resultid.txt_missing

name=$(cat userid.txt)
resultid=$(cat resultid.txt)

gpg --yes --batch --import /mnt/$name/$resultid/$name.sec

gpg --batch --trust-model always --passphrase=enterface -o master.txt -d master.txt.gpg

#Decryptage du fichier résultat
var=$(cat master.txt)

gpg --yes --batch --passphrase=$var Results.tar.gz.asc
tar -zxvf Results.tar.gz

rm -f userid.txt master.txt Results.tar.gz

exit 0;

