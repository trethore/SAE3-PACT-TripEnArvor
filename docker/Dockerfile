FROM php:8.2-apache

RUN a2enmod rewrite
RUN apt-get update
RUN apt-get install -y libpq-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
COPY files/startup /usr/local/bin
RUN chmod +x /usr/local/bin/startup
ENTRYPOINT ["/usr/local/bin/startup"]
CMD ["apache2-foreground"]
