#!/bin/bash -e
# Executes a Drupal database update. Intended to test the deployment of an
# update. The latest backup will be restored before the update is executed.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Install any new or updated dependencies.
lando composer install

# Regenerate the configuration files since they might have changed.
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

# Perform updates.
lando drush deploy --yes

# Clear the cache.
lando drush cr

# Index content in the search engine.
lando drush sapi-i
