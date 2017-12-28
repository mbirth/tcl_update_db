TCL Update DB
=============

This is going to be a web service to collect information about new updates from the
[tcl_ota_check](https://github.com/mbirth/tcl_ota_check) tools.

Also it will display a table of all (found) updates and the date when they were discovered.


Installation
============

Clone the repository to a folder on your webserver. To download dependencies, install `npm` and
run:

```
npm install
```

Create the database by running:

```
bin/initdb.sh
```

Put XML files to import into the `data/` directory and import them with:

```
bin/parse_files.php
```
