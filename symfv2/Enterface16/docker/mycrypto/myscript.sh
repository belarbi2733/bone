#!/bin/bash

genpasswd() {
  local length=${1:-20}
  tr -dc A-Za-z0-9_ < /dev/urandom | head -c ${length} | xargs
}

export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

hadoop-fuse-dfs dfs://10.142.0.4 /mnt

[ -d /mnt ] || exit 1;

cd /home

touch Results_missing
[ -d Results ] || exit 2;
rm Results_missing

touch userid.txt_missing
[ -f userid.txt ] || exit 3;
rm userid.txt_missing

touch resultid.txt_missing
[ -f resultid.txt ] || exit 4;
rm resultid.txt_missing

#Compression du répertoire
tar cfz Results.tar.gz Results

#Generation d'une clef de cryptage symétrique
varlocal=$(genpasswd)

#enregistrement de la clef
echo -n $varlocal > master.txt

#Cryptage du fichier results
gpg --armor --cipher-algo AES256 --batch --passphrase=$varlocal --symmetric  Results.tar.gz

#Suppression du fichier résultats en clair
rm -f Results.tar.gz

name=$(cat userid.txt)
resultid=$(cat resultid.txt)

cat >  $name <<EOF
     %echo Generating a basic OpenPGP key
     Key-Type: 1
     Key-Length: 1024
     Name-Real: $name
     Name-Comment: pass
     Name-Email: $name@mail.com
     Expire-Date: 0
     Passphrase: enterface
     %pubring $name.pub
     %secring $name.sec
     # Do a commit here, so that we can later print "done" :-)
     %commit
     %echo done
EOF

gpg --batch --gen-key  $name
rm -f $name

ls -l $name.*
 
gpg --yes --batch --import $name.pub
gpg --trust-model always -e -r $name master.txt
#gpg --yes --batch  --delete-keys $name
rm -f master.txt

chown -R . --reference Results
rm -rf Results

mkdir -p /mnt/$name/$resultid

/bin/cp $name.sec /mnt/$name/$resultid/$name.sec
echo $?
sleep 1

ls -l  $name.sec  /mnt/$name/$resultid/$name.sec

rm -f userid.txt resultid.txt $name.sec

exit 0;

