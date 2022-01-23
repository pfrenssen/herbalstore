#!/bin/bash -ex
# Downloads the latest backup from the backup server.

mkdir -p userfiles
rsync -az --delete desktop.office.lan:/mnt/workspace/sites/zonnedauw/backups/daily.0/ userfiles
