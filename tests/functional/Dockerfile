FROM php:8.3-cli
RUN apt update && apt-get install -y libzip-dev chromium-driver libpq-dev && docker-php-ext-install zip && docker-php-ext-enable zip && docker-php-ext-install pdo_pgsql && docker-php-ext-enable pdo_pgsql && pecl install xdebug && docker-php-ext-enable xdebug
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install -y symfony-cli
ENV DISPLAY=:99
RUN cd /tmp && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && php -r "unlink('composer-setup.php');" && mv composer.phar /usr/local/bin/composer
WORKDIR /app/tests/functional
ENTRYPOINT ["symfony", "serve"]