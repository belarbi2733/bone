FROM amine2733/cardio-test
COPY main_Vertebrae.cpp /root/main_Vertebrae.cpp

RUN cd /root && g++ main_Vertebrae.cpp -o main_Vertebrae -lopencv_core -lopencv_imgproc -lopencv_highgui -lopencv_ml -lopencv_video -lopencv_features2d -lopencv_calib3d -lopencv_objdetect -lopencv_contrib -lopencv_legacy -lopencv_flann
RUN chmod +x /root/main_Vertebrae
COPY script.sh /root/script.sh
RUN sudo chmod +x /root/script.sh
CMD cd /home && /root/script.sh