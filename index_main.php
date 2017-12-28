<!DOCTYPE html>
<html>
<head>
  <title>BlackBerry/TCL Firmware List</title>
  <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.css"/>
  <link rel="stylesheet" href="assets/material-icons.css"/>
  <link rel="stylesheet" href="assets/style.css"/>
</head>
<body class="mdc-typography">
  <header class="mdc-toolbar mdc-toolbar--fixed">
    <div class="mdc-toolbar__row">
      <section class="mdc-toolbar__section mdc-toolbar__section--align-start">
        <span class="mdc-toolbar__title">BlackBerry/TCL Firmware List</span>
      </section>
    </div>
  </header>
  <main>
    <div class="mdc-toolbar-fixed-adjust"></div>
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
            echo '<tr><td class="ref">';
            if (mb_strlen($name) > 0) {
                echo '<abbr title="' . $name . '">' . $ref . '</abbr>';
            } else {
                echo $ref;
            }
            echo '</td>';
            $refVersions = $db->getAllVersionsForRef($ref);
            $allOta      = $db->getAllVersionsForRef($ref, $db::OTA_ONLY);
            foreach ($allVersions as $v) {
                if (in_array($v, $refVersions, true)) {
                    if (in_array($v, $allOta)) {
                        echo '<td>' . $v . '</td>';
                    } else {
                        echo '<td class="fullonly mdc-theme--secondary-dark">' . $v . '</td>';
                    }
                } else {
                    echo '<td class="empty">- - -</td>';
                }
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '</tbody></table>';
    }
}
?>
  </main>
  <script src="node_modules/material-components-web/dist/material-components-web.js"></script>
  <script>window.mdc.autoInit()</script>
</body>
</html>
