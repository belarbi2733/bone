FROM centos:latest
MAINTAINER Sydney Bogaert <bogaert@gmail.com>
RUN curl https://archive.cloudera.com/cdh5/redhat/7/x86_64/cdh/cloudera-cdh5.repo -o /etc/yum.repos.d/cloudera-cdh5.repo
RUN rpm --import https://archive.cloudera.com/cdh5/redhat/7/x86_64/cdh/RPM-GPG-KEY-cloudera
RUN rpm --import http://mirror.centos.org/centos/RPM-GPG-KEY-CentOS-7
RUN yum -y install hadoop-hdfs-fuse java-1.8.0-openjdk-headless openssl && yum clean all
ADD myscript.sh /root/myscript.sh
RUN chmod +x /root/myscript.sh
CMD /root/myscript.sh

