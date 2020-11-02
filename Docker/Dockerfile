FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git autoconf make g++ && pecl install xdebug
RUN docker-php-ext-install pdo_mysql mysqli && \
 docker-php-ext-enable xdebug
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /code
CMD composer --version install ; wait-for-it mysql:3306 -- bin/console doctrine:migrations:migrate ;  php-fpm
EXPOSE 9000