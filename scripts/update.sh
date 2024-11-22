#!/bin/bash -e
# Executes a Drupal database update. Intended to test the deployment of an
# update. The latest backup will be restored before the update is executed.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Create environment specific files.
cp resources/ddev/settings.php web/sites/default/
cp resources/ddev/behat.yml .

# Start the development environment.
ddev start -y

# Install any new or updated dependencies.
ddev composer install

# Restore the database.
ddev drush sql-drop -y
ddev import-db --file=userfiles/dump.sql.gz

# Hardlink the files folder. Remove the existing destination since an empty
# folder might be scaffolded here.
chmod u+w -R web/sites/default 2> /dev/null
rm -rf web/sites/default/files
cp -al userfiles/files web/sites/default/

# Create temporary files folder and private files folder.
mkdir -p tmp/
mkdir -p private/

# Perform updates.
ddev drush deploy --yes

# Clear the cache.
ddev drush cr

# Index content in the search engine.
ddev drush sapi-i

# Check that config is fully exported.
ddev check-config
