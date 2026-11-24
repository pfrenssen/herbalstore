#!/bin/bash -ex
# Downloads the latest backup from the backup server.

mkdir -p userfiles
rsync -az --delete arch-beelink.lan:/srv/workspace/backups/zonnedauw/daily.0/ userfiles
