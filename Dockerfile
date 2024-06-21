FROM php:8.2.20-apache-bullseye
#COPY . /var/www/html/ #uncomment for evaluation

ARG UID
ARG GID

RUN apt update && \
    apt install -y sudo && \
    apt install -y sqlite3 && \
    addgroup --gid $GID marvin && \
    adduser --uid $UID --gid $GID --disabled-password --gecos "" marvin && \
    echo 'marvin ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers

RUN a2enmod rewrite
WORKDIR /var/www/html/

#USER marvin

ENTRYPOINT "apache2-foreground"
#ENTRYPOINT ["bash", "config/config.sh"]