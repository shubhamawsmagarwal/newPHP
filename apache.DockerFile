FROM ubuntu:latest
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update -y
RUN apt-get install -y nano
RUN apt-get install -y apache2 
RUN apt-get install -y php 
RUN apt-get install -y php-mysql
RUN apt-get install -y php-curl
RUN apt-get install -y tzdata
CMD apachectl -D FOREGROUND
ENV host='devmysql'
ENV database='userdatabase'
ENV user='root'
ENV password='anotherpassword'
ENV bucketName='myprojectstorage1107'
ENV IAM_PUBLIC=''
ENV IAM_PRIVATE=''
RUN a2enmod rewrite
EXPOSE 80