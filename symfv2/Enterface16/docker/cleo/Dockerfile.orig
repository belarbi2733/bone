FROM java:8
RUN apt-get update && apt-get -y install xvfb libjava3d-java libjava3d-jni && apt-get clean
RUN cp /usr/share/java/j3d*.jar /usr/lib/jvm/java-8-openjdk-amd64/jre/lib/ext/
RUN cp /usr/lib/jni/libj3dcore-ogl.so /usr/lib/jvm/java-8-openjdk-amd64/jre/lib/amd64/
RUN cp /usr/share/java/vecmath-*.jar /usr/lib/jvm/java-8-openjdk-amd64/jre/lib/ext/
COPY Combined-dist6.jar /root/cleo.jar
COPY script.sh /root/script.sh
RUN chmod +x /root/script.sh
CMD cd /home && /root/script.sh
