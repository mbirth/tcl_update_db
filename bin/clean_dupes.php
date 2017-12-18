#!/usr/bin/env php
<?php

$bkup_dir = __DIR__ . '/../data/';

$file_list = glob($bkup_dir . '*.xml');

$hashes = array();
foreach ($file_list as $file) {
    $filename = basename($file);
    $file_hash = sha1_file($file);

    if (isset($hashes[$file_hash])) {
        $old_file = $hashes[$file_hash];
        if (md5_file($file) == md5_file($bkup_dir . $old_file)) {
            echo 'Duplicate file: ' . $filename . ' (first: ' . $old_file . ')' . PHP_EOL;
            unlink($file);
            continue;
        }
        echo 'Possible SHA1 collision?' . PHP_EOL;
    }

    $hashes[$file_hash] = $filename;
}
