<!DOCTYPE html>
<html>
<head>
    <title>BlackBerry/TCL Firmware List</title>
    <link rel="stylesheet" href="assets/style.css"/>
</head>
<body>
<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$allVars = $db->getAllVariants();
$unknowns = $db->getUnknownRefs();
if (count($unknowns) > 0) {
    $variants = array();
    foreach ($unknowns as $uref) {
        $variants[$uref] = '';
    }
    $allVars['Unknown'] = array(
        'Variants' => $variants,
    );
}

foreach ($allVars as $family => $models) {
    foreach ($models as $model => $variants) {
        echo '<h2>' . $family . ' ' . $model . '</h2>' . PHP_EOL;
        $allVersions = $db->getAllVersionsForModel($model);
        echo '<table><tbody>';
        foreach ($variants as $ref => $name) {
            echo '<tr><td>' . $ref . '</td>';
            $refVersions = $db->getAllVersionsForRef($ref);
            foreach ($allVersions as $v) {
                if (in_array($v, $refVersions, true)) {
                    echo '<td>' . $v . '</td>';
                } else {
                    echo '<td class="empty">------</td>';
                }
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '</tbody></table>';
    }
}
?>
</body>
</html>
