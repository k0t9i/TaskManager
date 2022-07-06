FROM php:8.1.4-fpm

RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list \
    && apt-get update && apt-get install -y unzip vim git libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libzip-dev libpq-dev libicu-dev symfony-cli \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install xdebug-3.1.3 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-install opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
    
COPY ./docker/configs/php /usr/local/etc/php/conf.d

RUN mkdir -p /opt/home
RUN chmod 777 /opt/home
ENV HOME /opt/home