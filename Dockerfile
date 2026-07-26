FROM php:8.2-apache

# Տեղադրում ենք MySQLi և PDO մոդուլները բազայի հետ աշխատելու համար
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

RUN a2enmod rewrite

# Կարգավորում ենք Apache-ն, որ աշխատի Railway-ի դինամիկ պորտով
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE ${PORT}