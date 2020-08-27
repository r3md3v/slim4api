#!/usr/bin/env bash

docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_customers.php
docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_users.php
