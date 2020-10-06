#!/usr/bin/env bash


isMysql=$(which mysql |wc -l)
if [[ $isMysql -eq 0 ]]; then
  echo installing mysql client
  sudo apt update
  sudo apt install -y mysql-client vim phpunit liburi-encode-perl
fi
composer self-update --stable
composer install
composer update
composer start
