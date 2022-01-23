#!/bin/bash -e
# Executes a Drupal database update. Intended to test the deployment of an
# update. The latest backup will be restored before the update is executed.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Create environment specific files.
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.lando.yml > .lando.yml
LANDO_PROJECT_NAME=herbalstore envsubst < resources/lando/.env > .env

# Rebuild Lando.
lando rebuild -y

# Install any new or updated dependencies.
lando composer install

# Regenerate the configuration files since they might have changed.
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

# Perform updates.
lando drush deploy --yes

# Clear the cache.
lando drush cr

# Index content in the search engine.
lando drush sapi-i
