<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$allVars = $db->getAllVariantsByRef();
$unknowns = $db->getUnknownRefs();
if (count($unknowns) > 0) {
    foreach ($unknowns as $uref) {
        $allVars[$uref] = array(
            'family' => 'Unknown',
            'model' => 'Model',
            'variant' => '',
        );
    }
}

header('Content-Type: application/rss+xml');

?>
<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>TCL OTA</title>
    <link>https://tclota.birth-online.de/timeline.php</link>
    <atom:link href="https://tclota.birth-online.de/rss.php" rel="self" type="application/rss+xml" />
    <description>TCL OTA updates</description>
<?php

/*
 * NOTE: it would be nice to have a filter for device id to be able to subscribe only to specific model/variant
 */

$allfiles = $db->getAllFiles($db::FULL_ONLY);
foreach ($allfiles as $file) {
    $updates = $db->getAllUpdatesForFile($file['sha1']);
    $validRefs = array();
    $validDevs = array();
    $firstSeen = new DateTime();
    $firstSeen->setTimezone(new DateTimeZone('CET'));
    foreach ($updates as $u) {
        $dev = $allVars[$u['curef']];
        $validRefs[] = $u['curef'];
        $validDevs[] = $dev['family'] . ' ' . $dev['model'];
        $firstSeenDate = new DateTime($u['seenDate']);
        $firstSeenDate->setTimezone(new DateTimeZone('CET'));
        if ($firstSeenDate < $firstSeen) {
            $firstSeen = $firstSeenDate;
        }
    }
    $validDevs = array_unique($validDevs);
    sort($validDevs);
    $device = $allVars[$updates[0]['curef']];
    $date = new DateTime($file['published_first']);
    $date->setTimezone(new DateTimeZone('CET'));
    $dateLast = new DateTime($file['published_last']);
    $dateLast->setTimezone(new DateTimeZone('CET'));
    echo '<item>' . PHP_EOL;
    echo '  <title>' . $file['tv'] . '</title>' . PHP_EOL;
    echo '  <link>https://tclota.birth-online.de/timeline.php</link>' . PHP_EOL;
    echo '  <description><![CDATA[';
    if ($file['fv']) {
        echo '(OTA from ' . $file['fv'] . ')';
    }
    echo $date->format('Y-m-d') . ' ' . $date->format('H:i.s') . ' CET<br />';
    echo 'Devices: ' . implode(', ', $validDevs) . '<br />';
    echo 'Last released: ' . $dateLast->format('Y-m-d H:i.s') . ' (first seen in the wild: ' . $firstSeen->format('Y-m-d H:i.s') . ')<br/>';
    echo 'Valid for (order of release): ' . implode(', ', $validRefs);
    echo ']]></description>' . PHP_EOL;
    echo '</item>' . PHP_EOL;
}

?>
  </channel>
</rss>
