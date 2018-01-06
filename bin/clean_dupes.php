#!/usr/bin/env php
<?php

$bkup_dir = __DIR__ . '/../data/';

$ansi_csi = chr(27) . '[';
$ansi_updel = $ansi_csi . 'F' . $ansi_csi . 'K';

echo 'Reading file list ...';
$file_list = glob($bkup_dir . '*.xml');

$num_total = count($file_list);
$num_deleted = 0;

echo 'found ' . $num_total . ' files.' . PHP_EOL;

echo 'Searching for duplicates ...' . PHP_EOL . PHP_EOL;

$hashes = array();
foreach ($file_list as $i => $file) {
    echo $ansi_updel . ($i+1) . '/' . count($file_list) . PHP_EOL;
    $filename = basename($file);
    $file_hash = sha1_file($file);

    if (isset($hashes[$file_hash])) {
        $old_file = $hashes[$file_hash];
        if (md5_file($file) == md5_file($bkup_dir . $old_file)) {
            echo $ansi_updel . 'Duplicate file: ' . $filename . ' (first: ' . $old_file . ')' . PHP_EOL . PHP_EOL;
            unlink($file);
            $num_deleted++;
            continue;
        }
        echo $ansi_updel . 'Possible SHA1 collision?' . PHP_EOL . PHP_EOL;
    }

    $hashes[$file_hash] = $filename;
    flush();
}

echo count($file_list) . ' files processed. ' . $num_deleted . ' deleted. ' . ($num_total - $num_deleted) . ' left.'  . PHP_EOL;

