#!/bin/bash -ex
# Downloads the latest backup from the backup server.

mkdir -p userfiles
rsync -az --delete arch-beelink.lan:/mnt/workspace/backups/zonnedauw/backups/daily.0/ userfiles
