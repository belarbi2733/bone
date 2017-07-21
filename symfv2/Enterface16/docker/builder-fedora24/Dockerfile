FROM fedora24-opencv
VOLUME /home
CMD cd /home && ( for fichier in *.cpp; do g++ -o $fichier.out $fichier  $(pkg-config --static --cflags --libs opencv); chown --reference $fichier $fichier.out ; done)
