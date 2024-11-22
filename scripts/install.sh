#!/bin/bash -ex
# Installs a development environment which mimics production.
# For best results, check out the master branch before executing this script.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

# Create environment specific files.
cp resources/ddev/settings.php web/sites/default/
cp resources/ddev/behat.yml .

# Start the development environment.
ddev start -y

# Install dependencies.
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

# Clear the cache.
ddev drush cr

# Index content in the search engine.
ddev drush sapi-i

# Show project info.
ddev describe
