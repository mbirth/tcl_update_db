<!DOCTYPE html>
<html>
<head>
  <title>BlackBerry/TCL Firmware List</title>
  <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.css"/>
  <link rel="stylesheet" href="assets/material-icons.css"/>
  <link rel="stylesheet" href="assets/style.css"/>
</head>
<body class="mdc-typography">
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

$families = array_keys($allVars);

?>
  <header class="mdc-toolbar mdc-toolbar--fixed">
    <div class="mdc-toolbar__row">
      <section class="mdc-toolbar__section mdc-toolbar__section--shrink-to-fit mdc-toolbar__section--align-start">
        <span class="mdc-toolbar__title">BlackBerry/TCL Firmware List</span>
      </section>
      <section class="mdc-toolbar__section mdc-toolbar__section--align-end" role="toolbar">
        <div>
          <nav id="tab-bar" class="mdc-tab-bar mdc-tab-bar--indicator-accent">
<?php

foreach ($families as $i => $family) {
    echo '<a class="mdc-tab' . (($i==0)?' mdc-tab--active':'') . '" href="#' . strtolower($family) . '" data-panel="family-' . strtolower($family) . '">' . $family . '</a>' . PHP_EOL;
}

?>
            <span class="mdc-tab-bar__indicator"></span>
          </nav>
        </div>
      </section>
    </div>
  </header>
  <main>
    <div class="mdc-toolbar-fixed-adjust"></div>
<?php

foreach ($allVars as $family => $models) {
    echo '<div id="family-' . strtolower($family) . '" class="panel" role="tabpanel">';
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
    echo '</div>';
}
?>
  </main>
  <script type="text/javascript" src="node_modules/material-components-web/dist/material-components-web.js"></script>
  <script type="text/javascript" src="assets/main.js"></script>
</body>
</html>
