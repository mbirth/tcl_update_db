#!/bin/sh
. "${MYDIR}/../config.ini"
rsync -av "${MYDIR}/../otadb.db3" "${SYNC_REMOTE_DIR}/"

