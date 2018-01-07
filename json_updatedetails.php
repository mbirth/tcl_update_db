<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$refs = $db->getAllRefs();
$vars = $db->getAllVariantsFlat();

$output = array();
foreach ($refs as $ref) {
    $updates = $db->getAllUpdates($ref);

    $versions = array();
    foreach ($updates as $update) {
        $fv = $update['fv'];
        $tv = $update['tv'];
        $update['note'] = json_decode($update['note'], true);
        $similar_refs = $db->getAllRefsForFile($update['file_sha1']);
        $update['applies_to'] = $similar_refs;
        if ($fv && !isset($versions[$fv])) {
            $versions[$fv] = array('OTA_FROM' => array(), 'OTA' => array(), 'FULL' => array());
        }
        if (!isset($versions[$tv])) {
            $versions[$tv] = array('OTA_FROM' => array(), 'OTA' => array(), 'FULL' => array());
        }
        if (!$update['fv']) {
            $versions[$tv]['FULL'][] = $update;
        } else {
            $versions[$fv]['OTA_FROM'][] = $update;
            $versions[$tv]['OTA'][] = $update;
        }
    }

    $output[$ref] = array(
        'curef' => $ref,
        'variant' => $vars[$ref],
        'versions' => $versions,
    );
}

header('Content-Type: text/json');

$output = json_encode($output, JSON_PRETTY_PRINT);
if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    ini_set('zlib.output_compression', 'Off');
    header('Content-Encoding: gzip');
    $output = gzencode($output);
}

echo $output;
