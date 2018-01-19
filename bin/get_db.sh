#!/bin/sh
MYDIR=$(dirname "$(readlink -f "$0")")
. "${MYDIR}/../config.ini"
rsync -av "${SYNC_REMOTE_DIR}/otadb.db3" "${MYDIR}/../"
