FROM php:7.4-fpm
RUN docker-php-ext-install pdo pdo_mysql 
RUN docker-php-ext-enable pdo pdo_mysql
