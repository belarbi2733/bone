FROM fedora:24
RUN dnf -y install --nogpgcheck http://download1.rpmfusion.org/free/fedora/rpmfusion-free-release-24.noarch.rpm \
    http://download1.rpmfusion.org/nonfree/fedora/rpmfusion-nonfree-release-24.noarch.rpm dnf-plugins-core.noarch && \
    rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-rpmfusion-nonfree-fedora-24 
RUN dnf -y install gcc-c++ opencv-devel findutils ffmpeg-devel ffmpeg && \
    dnf -y install gstreamer1-* gstreamer-* --exclude *docs*,*devel* \
    && dnf -y --setopt tsflags= install opencv-devel-docs && dnf -y clean all
RUN g++ -o edge /usr/share/OpenCV/samples/cpp/edge.cpp $(pkg-config --static --cflags --libs opencv)
