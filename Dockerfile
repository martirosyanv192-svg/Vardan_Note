FROM php:8.2-apache

# Ակտիվացնում և տեղադրում ենք mysqli-ն ու բազայի մոդուլները
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

# Ապահովում ենք URL-ների վերագրառումը (URL rewriting)
RUN a2enmod rewrite

# Բացում ենք պորտը
EXPOSE 80