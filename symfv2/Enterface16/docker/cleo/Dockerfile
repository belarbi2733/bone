FROM java:8
RUN apt-get update && apt-get install -y p7zip
RUN cd /opt && wget  http://jogamp.org/deployment/java3d/1.6.0-final/jogamp-java3d.7z && p7zip -d jogamp-java3d.7z 
RUN cd /opt && cp vecmath.jar /usr/lib/jvm/java-1.8.0-openjdk-amd64/jre/lib/ext/ && cp j3dutils.jar /usr/lib/jvm/java-1.8.0-openjdk-amd64/jre/lib/ext/ && cp j3dcore.jar /usr/lib/jvm/java-1.8.0-openjdk-amd64/jre/lib/ext/
COPY Combined-dist6.jar /root/cleo.jar
COPY script.sh /root/script.sh
RUN chmod +x /root/script.sh
RUN cd /root && mkdir sharefolder
CMD cd /home && /root/script.sh
