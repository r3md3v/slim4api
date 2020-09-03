#!/usr/bin/env bash

docker-compose -f docker-compose-nginx.yml exec my_app nginx -s reload
