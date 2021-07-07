#!/bin/bash -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Create environment specific files.
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.lando.yml > .lando.yml
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.env > .env

# Start Lando.
lando start

# Ensure folders are writable.
chmod u+w -R web/sites/default/

# Generate the configuration files.
lando robo dev:setup

# Sync the database and files from the latest backup.
mkdir -p userfiles
rsync -az --delete desktop.office.lan:/mnt/workspace/sites/zonnedauw/backups/daily.0/ userfiles

# Restore the database.
zcat userfiles/dump.sql.gz | lando drush sql:cli

# Hardlink the files folder. Remove the existing destination since an empty
# folder might be scaffolded here.
rm -rf web/sites/default/files
cp -al userfiles/files web/sites/default/

# Clear the cache.
lando drush cr

# Index content in the search engine.
lando drush sapi-i
