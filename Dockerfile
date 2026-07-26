FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Փոխում ենք Apache-ի լռելյայն պորտը Railway-ի PORT փոփոխականին
RUN sed -i "s/80/\$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80