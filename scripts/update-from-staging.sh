#!/bin/bash

git pull
./vendor/bin/composer install
./vendor/bin/robo dev:setup
./vendor/bin/drush sql:sync @staging @self --yes
./vendor/bin/drush rsync @staging:%files @self:%files --yes
./vendor/bin/drush cr
