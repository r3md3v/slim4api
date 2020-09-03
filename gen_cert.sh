#!/usr/bin/env bash

openssl req -x509 -nodes -newkey rsa:4096 -keyout Docker/key.pem -out Docker/cert.pem -days 3650 -subj '/CN=phoebe'
