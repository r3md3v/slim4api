#!/usr/bin/env bash

openssl genrsa -out jwtkey.pem 2048
openssl rsa -in jwtkey.pem -outform PEM -pubout -out jwtpublic.pem
