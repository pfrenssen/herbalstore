#!/bin/bash

if [ "$#" -ne 1 ]; then
  echo xdebug.mode = off > /usr/local/etc/php/conf.d/zzz-lando-xdebug.ini
  /etc/init.d/apache2 reload
  echo "Xdebug has been disabled. To enable: 'lando xdebug <mode>'."
  echo "Valid modes: https://xdebug.org/docs/all_settings#mode."
else
  mode="$1"
  echo xdebug.mode = "$mode" > /usr/local/etc/php/conf.d/zzz-lando-xdebug.ini
  /etc/init.d/apache2 reload
  echo "Xdebug is loaded in "$mode" mode."
fi
