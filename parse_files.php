#!/usr/bin/env php
<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\GotuObject;
use \TclUpdates\XmlParser;
use \TclUpdates\SQLiteWriter;

$bkup_dir = __DIR__ . '/data/';

$file_list = glob($bkup_dir . '*.xml');
$sqlw = new SQLiteWriter();

foreach ($file_list as $file) {
    $filename = basename($file);
    $data = file_get_contents($file);
    $xp = new XmlParser();
    $load_ok = $xp->loadXmlFromString($data);
    if (!$load_ok) {
        echo 'Could not load ' . $filename . '!' . PHP_EOL;
        continue;
    }
    if (!$xp->validateGOTU()) {
        echo 'XML not valid in ' . $filename . '!' . PHP_EOL;
        continue;
    }
    echo 'Processing ' . $filename . PHP_EOL;
    $g = GotuObject::fromXmlParser($xp);
    print_r($g);
    if ($g->tv) {
        $sqlw->addGotu($g);
    }
}
