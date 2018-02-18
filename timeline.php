<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>BlackBerry/TCL Firmware Timeline</title>
  <meta name="viewport" content="width=device-width, initial-scale=0.8"/>
  <meta name="theme-color" content="#1b5e20"/>
  <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.css"/>
  <link rel="stylesheet" href="assets/material-icons.css"/>
  <link rel="stylesheet" href="assets/style.css"/>
  <script type="text/javascript" src="node_modules/material-components-web/dist/material-components-web.js"></script>
  <script type="text/javascript" src="assets/menu.js"></script>
</head>
<body class="mdc-typography timeline">
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

?>
  <header class="mdc-toolbar mdc-toolbar--fixed">
    <div class="mdc-toolbar__row">
      <section class="mdc-toolbar__section mdc-toolbar__section--shrink-to-fit mdc-toolbar__section--align-start">
        <button class="material-icons mdc-toolbar__menu-icon">menu</button>
        <span class="mdc-toolbar__title">BlackBerry/TCL Firmware Timeline</span>
      </section>
    </div>
  </header>

  <?php include 'menu.php'; ?>

  <main>
    <div class="mdc-toolbar-fixed-adjust"></div>
<?php

$allfiles = $db->getAllFiles($db::FULL_ONLY);
foreach ($allfiles as $file) {
    $updates = $db->getAllUpdatesForFile($file['sha1']);
    $validRefs = array();
    $validDevs = array();
    foreach ($updates as $u) {
        $dev = $allVars[$u['curef']];
        $validRefs[] = $u['curef'];
        $validDevs[] = $dev['family'] . ' ' . $dev['model'];
    }
    $validDevs = array_unique($validDevs);
    sort($validDevs);
    $device = $allVars[$updates[0]['curef']];
    $date = new DateTime($file['published_first']);
    $date->setTimezone(new DateTimeZone('CET'));
    $dateLast = new DateTime($file['published_last']);
    $dateLast->setTimezone(new DateTimeZone('CET'));
    echo '<div class="mdc-card release-card">';
    echo '<div class="mdc-typography--body1">';
    echo '<div class="version">' . $file['tv'];
    if ($file['fv']) {
        echo '<span>(OTA from ' . $file['fv'] . ')</span>';
    }
    echo '</div>';
    echo '<div class="date"><span>' . $date->format('Y-m-d') . '</span> ' . $date->format('H:i.s') . ' CET</div>';
    echo '<div class="devices"><span>' . implode('</span> / <span>', $validDevs) . '</span></div>';
    echo '<div class="lastreleased">Last released: <span>' . $dateLast->format('Y-m-d H:i.s') . '</span></div>';
    echo '<div class="validfor">Valid for (order of release): <span>' . implode('</span>, <span>', $validRefs) . '</span></div>';
    #print_r($file);
    #print_r($updates);
    echo '</div></div>';
}

?>
  </main>
</body>
</html>
