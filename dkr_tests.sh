#!/usr/bin/env bash

docker-compose -f docker-compose-nginx.yml exec php-fpm composer test
