FROM php:8.2.20-apache-bullseye
#COPY . /var/www/html/ #uncomment for evaluation
WORKDIR /var/www/html/
EXPOSE 8080
RUN a2enmod rewrite
ENTRYPOINT "apache2-foreground"