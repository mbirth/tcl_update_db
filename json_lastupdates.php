<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$refs = $db->getAllRefs();
$vars = $db->getAllVariantsFlat();

$output = array();
foreach ($refs as $ref) {
    $lastOta  = $db->getLatestUpdate($ref, $db::OTA_ONLY);
    $lastFull = $db->getLatestUpdate($ref, $db::FULL_ONLY);

    $output[$ref] = array(
        'curef' => $ref,
        'variant' => $vars[$ref],
        'last_ota' => $lastOta['tv'],
        'last_full' => $lastFull['tv'],
    );
}

header('Content-Type: text/json');

echo json_encode($output);
