FROM php:8.2.20-apache-bullseye
#COPY . /var/www/html/ #uncomment for evaluation

#ARG UID
#ARG GID

RUN apt update && \
    apt install -y sudo && \
    apt install -y sqlite3 

RUN a2enmod rewrite
WORKDIR /var/www/html/

#USER marvin

ENTRYPOINT ["bash", "config/config.sh"]