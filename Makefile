.PHONY: vendor coverage genrsa genjwt faker up down reload php phplog test sonarstart sonarlog sonarrun

MAKEPATH := $(abspath $(lastword $(MAKEFILE_LIST)))
PWD := $(dir $(MAKEPATH))
CONTAINERS := $(shell docker ps -a -q -f "name=slim4api*")
SONARQUBE_URL := "sonarqube:9000"
SONAR_NET := "slim4api_sonarnet"

vendor:
		docker-compose -f docker-compose-nginx.yml exec php-fpm sh -c "composer install"

coverage:
		docker-compose -f docker-compose-nginx.yml exec php-fpm sh -c "./vendor/bin/phpunit --coverage-text --coverage-html coverage"

genrsa:
		openssl req -x509 -nodes -newkey rsa:4096 -keyout Docker/key.pem -out Docker/cert.pem -days 3650 -subj '/CN=phoebe'

genjwt:
		openssl genrsa -out jwtkey.pem 2048
		openssl rsa -in jwtkey.pem -outform PEM -pubout -out jwtpublic.pem

faker:
		docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_customers.php
		docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_users.php
up:
		docker-compose -f docker-compose-nginx.yml up -d --build

down:
		docker-compose -f docker-compose-nginx.yml docker-compose-nginx.yml down -v

reload:
		docker-compose -f docker-compose-nginx.yml exec my_app nginx -s reload

php:
		docker-compose -f docker-compose-nginx.yml exec php-fpm bash

phplog:
		docker-compose -f docker-compose-nginx.yml logs -f php-fpm
test:
		docker-compose -f docker-compose-nginx.yml exec php-fpm composer test

sonarstart:
		docker-compose -f docker-compose-sonarsvr.yaml up -d

sonarlog:
		docker-compose -f docker-compose-sonarsvr.yaml logs -f

sonarrun:
		docker run --rm \
			-e SONAR_HOST_URL=http://${SONARQUBE_URL} \
            -v "${PWD}:/usr/src" \
            --network="${SONAR_NET}" \
            sonarsource/sonar-scanner-cli