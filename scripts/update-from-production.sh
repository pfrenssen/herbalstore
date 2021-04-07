#!/bin/bash -e

# Get the latest code from the github repository.
git pull

# Install any new or updated dependencies.
lando composer install

# Regenerate the configuration files since they might have changed.
lando robo dev:setup

# Copy the database.
lando drush sql:sync @production @self --yes

# Copy the files and images.
lando drush rsync @production:%files @self:%files --yes

# Perform updates.
lando drush updatedb --yes --no-post-updates
lando drush config:import --yes
lando drush updatedb --yes

# Clear the cache.
lando drush cr

# Index content in the search engine.
lando drush sapi-i
