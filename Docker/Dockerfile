FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql mysqli
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /code
CMD composer install ; wait-for-it mysql:3306 -- bin/console doctrine:migrations:migrate ;  php-fpm
EXPOSE 9000