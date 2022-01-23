#!/bin/bash -ex
# Installs a development environment which mimics production.
# For best results, check out the master branch before executing this script.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Create environment specific files.
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.lando.yml > .lando.yml
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.env > .env

# Rebuild Lando.
lando rebuild -y

# Install dependencies.
lando composer install

# Generate the configuration files.
lando robo dev:setup

# Restore the database.
lando drush sql-drop -y
zcat userfiles/dump.sql.gz | lando drush sql:cli

# Hardlink the files folder. Remove the existing destination since an empty
# folder might be scaffolded here.
chmod u+w -R web/sites/default 2> /dev/null
rm -rf web/sites/default/files
cp -al userfiles/files web/sites/default/

# Create temporary files folder and private files folder.
mkdir -p tmp/
mkdir -p private/

# Clear the cache.
lando drush cr

# Index content in the search engine.
lando drush sapi-i

# Show project info.
lando info
