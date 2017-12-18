#!/bin/sh
MYDIR=$(dirname "$(readlink -f "$0")")
. "${MYDIR}/../config.ini"
rsync -av "${MYDIR}/../data" "${SYNC_REMOTE_DIR}/"

