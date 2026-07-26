FROM php:8.2-apache

# Ակտիվացնում ենք PDO-ն և MySQL դրայվերը
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo_mysql mysqli

# Միացնում ենք URL rewrite-ը
RUN a2enmod rewrite

# Ուղղում ենք Apache-ի պորտը Railway-ի դինամիկ PORT-ին
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
EXPOSE ${PORT}