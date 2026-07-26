FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Փոխում ենք Apache-ի default թղթապանակը, որ ուղղակիորեն կարդա մեր ֆայլերը
RUN sed -i 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY . /var/www/html/
EXPOSE 80