FROM php:8.1-fpm-alpine3.14 

WORKDIR /var/www/application

RUN apk update

RUN apk add --no-cache bash zlib-dev libpng-dev libjpeg-turbo-dev libjpeg-turbo freetype freetype-dev libxml2-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd mysqli pdo_mysql soap bcmath dom xml 

RUN apk add $PHPIZE_DEPS

RUN echo "max_file_uploads=500" >> /usr/local/etc/php/conf.d/docker-php-ext-max_file_uploads.ini
RUN echo "post_max_size=450M" >> /usr/local/etc/php/conf.d/docker-php-ext-post_max_size.ini
RUN echo "upload_max_filesize=450M" >> /usr/local/etc/php/conf.d/docker-php-ext-upload_max_filesize.ini
RUN echo 'max_execution_time = 600' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN sed -i "s/pm\.max_children = 5/pm.max_children = 20/" /usr/local/etc/php-fpm.d/www.conf

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"

RUN cp composer.phar /usr/local/bin/composer
RUN rm composer.phar

RUN rm -rf /var/www/html
RUN ln -s /var/www/application/public /var/www/html



COPY . /var/www/application


RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

RUN chown -R www-data:www-data /var/www/application

RUN pecl install openswoole
RUN pecl install redis
RUN pecl install excimer
RUN composer install --ignore-platform-req=ext-zip

USER root
RUN apk add --no-cache \
        wkhtmltopdf \
        xvfb \
        ttf-dejavu ttf-droid ttf-freefont ttf-liberation;

RUN ln -s /usr/bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf;
RUN chmod +x /usr/local/bin/wkhtmltopdf;
RUN echo 'extension=openswoole' > /usr/local/etc/php/conf.d/docker-php-ext-openswoole.ini
RUN echo 'extension=redis' > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini
RUN echo 'extension=excimer' > /usr/local/etc/php/conf.d/docker-php-ext-excimer.ini
RUN chown -R www-data:www-data /var/www/application
USER www-data

RUN php artisan cache:clear
RUN php artisan config:clear


CMD ["php", "artisan", "octane:start","--host=0.0.0.0"]
