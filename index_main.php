<!DOCTYPE html>
<html>
<head>
    <title>BlackBerry/TCL Firmware List</title>
</head>
<body>
<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\SQLiteReader;

$db = new SQLiteReader();

$allVars = $db->getAllVariants();

foreach ($allVars as $family => $models) {
    foreach ($models as $model => $variants) {
        echo '<h2>' . $family . ' ' . $model . '</h2>' . PHP_EOL;
        echo '<table>';
        foreach ($variants as $ref => $name) {
            echo '<tr><td>' . $ref . '</td>' . '</tr>' . PHP_EOL;
        }
        echo '</table>';
    }
}
print_r($db->getAllUpdates('PRD-63117-011', $db::BOTH));

print_r($db->getLatestUpdate('PRD-63117-011', $db::BOTH));


print_r($db->getUnknownPrds());

?>
</body>
</html>
