#!/bin/sh
MYDIR=$(dirname "$(readlink -f "$0")")
sqlite3 "${MYDIR}/../otadb.db3" < "${MYDIR}/../sql/dbschema.sql"
sqlite3 "${MYDIR}/../otadb.db3" < "${MYDIR}/../sql/basedata.sql"
