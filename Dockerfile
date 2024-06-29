FROM php:8.2.20-apache-bullseye
#COPY . /var/www/html/ #uncomment for evaluation

#ARG UID
#ARG GID

RUN apt update && \
    apt install -y sudo sqlite3 libapache2-mod-evasive

COPY /config/evasive.conf /etc/apache2/mods-enabled/evasive.conf
RUN a2enmod rewrite
RUN a2enmod evasive 
WORKDIR /var/www/html/

#USER marvin

ENTRYPOINT ["bash", "config/config.sh"]