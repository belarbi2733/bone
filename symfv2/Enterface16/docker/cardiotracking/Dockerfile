FROM  amine2733/qt-opencv2410-gpu
#RUN sudo userdel -r developer
COPY maintracking.cpp.orig /root/maintracking.cpp
COPY video.mov /root/video.mov
RUN cd /root && g++ maintracking.cpp -o maintracking -lopencv_core -lopencv_imgproc -lopencv_highgui -lopencv_ml -lopencv_video -lopencv_features2d -lopencv_calib3d -lopencv_objdetect -lopencv_contrib -lopencv_legacy -lopencv_flann
#RUN chmod +x /root/maintracking 
COPY script.sh /root/script.sh
RUN sudo chmod +x /root/script.sh
#RUN sudo usermod -aG sudo developer
CMD cd /home && /root/script.sh
