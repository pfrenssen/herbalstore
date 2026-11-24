#!/bin/bash -ex
# Tests a commit on the CI server, which runs this script from the root of the
# checkout. The latest backup must be in userfiles/ (see download-backup.sh).
# Restores the backup, deploys the code and runs the tests. The containers are
# stopped on exit, whether the tests pass or fail.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd $SCRIPT_DIR/..

export DDEV_NONINTERACTIVE=true

trap 'ddev stop' EXIT

# Restore the backup, deploy the code and check the exported config.
./scripts/update.sh

# Run the tests and code checks.
ddev behat
ddev phpcs
