<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$refs = $db->getAllRefs();
$vars = $db->getAllVariantsFlat();

$output = array();
foreach ($refs as $ref) {
    $updates = $db->getAllUpdates($ref);

    $all_versions = array();
    $update_map = array();
    $latest_ota = '';
    foreach ($updates as $update) {
        $fv = $update['fv'];
        $tv = $update['tv'];
        $all_versions[] = $tv;
        if (!$fv) {
            // FULL Update - ignore, just collect possible old version
            continue;
        }
        $all_versions[] = $fv;
        if ($tv > $latest_ota) {
            $latest_ota = $tv;
        }
        if (!isset($update_map[$tv])) {
            $update_map[$tv] = array();
        }
        $update_map[$tv][] = $fv;
    }

    if ($latest_ota === '') {
        // Not a single OTA found
        continue;
    }

    $all_versions = array_unique($all_versions);
    $all_versions = array_filter($all_versions, function($e) {
        global $latest_ota;
        return ($e < $latest_ota);
    });

    $missing_froms = array_diff($all_versions, array($latest_ota), $update_map[$latest_ota]);
    rsort($missing_froms);

    if (count($missing_froms) > 0) {
        $output[$ref] = array(
            'curef' => $ref,
            'variant' => $vars[$ref],
            'num_missing' => count($missing_froms),
            'missing_froms' => $missing_froms,
            'latest_ota' => $latest_ota,
        );
    }
}

header('Content-Type: text/json');

$output = json_encode($output, JSON_PRETTY_PRINT);
if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    ini_set('zlib.output_compression', 'Off');
    header('Content-Encoding: gzip');
    $output = gzencode($output);
}

echo $output;
