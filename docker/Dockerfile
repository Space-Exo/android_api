FROM php:8.1-apache



RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev unzip

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
  && echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
  && locale-gen

RUN curl -sS https://getcomposer.org/installer | php -- \
  && mv composer.phar /usr/local/bin/composer

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash \
  && apt-get -y install nodejs


ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
install-php-extensions ldap bcmath gd-2.3.3

#RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony5/bin/symfony /usr/local/bin

WORKDIR /var/www/
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli



RUN docker-php-ext-configure pcntl --enable-pcntl
RUN docker-php-ext-install pdo pdo_mysql opcache intl zip calendar dom mbstring zip xsl pcntl

RUN pecl install apcu && docker-php-ext-enable apcu

COPY php.ini /usr/local/etc/php/php.ini 

RUN apt-get update -y
RUN apt-get install -y tzdata
# timezone env with default
ENV TZ=Europe/Paris
RUN apt-get install -y ssl-cert openssl
RUN a2enmod ssl

WORKDIR /var/www/
