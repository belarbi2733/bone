FROM amine2733/cardio-test
COPY Vertebrae_adv.cpp /root/Vertebrae_adv.cpp

RUN cd /root && g++ Vertebrae_adv.cpp -o Vertebrae_adv `pkg-config --cflags --libs opencv`
RUN chmod +x /root/Vertebrae_adv
COPY script.sh /root/script.sh
RUN sudo chmod +x /root/script.sh
CMD cd /home && /root/script.sh