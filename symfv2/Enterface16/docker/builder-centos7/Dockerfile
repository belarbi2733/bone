FROM registry.gitlab.com/bogaert/centos7-opencv:latest
VOLUME /home
CMD cd /home && ( for fichier in *.cpp; do g++ -o $fichier.out $fichier  $(pkg-config --static --cflags --libs opencv); done)
