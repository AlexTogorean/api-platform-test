FROM php:8.2-apache

RUN apt-get update \
  && apt-get install -y libzip-dev libicu-dev libjpeg-dev libpng-dev zlib1g-dev libfreetype6-dev libpq-dev git wget zip unzip --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_pgsql zip intl bcmath

#RUN docker-php-ext-configure gd \
#    && docker-php-ext-install gd

COPY php.ini /usr/local/etc/php/php.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY site.conf /etc/apache2/sites-available/site.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf &&\
    a2enmod rewrite &&\
    a2dissite 000-default &&\
    a2ensite site &&\
    service apache2 restart

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
