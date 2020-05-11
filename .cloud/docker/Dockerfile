FROM php:7.1-fpm

# Installing dependencies
#RUN apt-get update && apt-get install -y mysql-client
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Changing Workdir
WORKDIR /application
