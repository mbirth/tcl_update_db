<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>BlackBerry/TCL Firmware List</title>
  <meta name="viewport" content="width=device-width, initial-scale=0.8"/>
  <meta name="theme-color" content="#1b5e20"/>
  <link rel="alternate" type="application/rss+xml" title="BlackBerry/TCL Firmware Timeline Feed" href="rss.php"/>
  <link rel="stylesheet" href="node_modules/material-components-web/dist/material-components-web.css"/>
  <link rel="stylesheet" href="assets/material-icons.css"/>
  <link rel="stylesheet" href="assets/style.css"/>
  <script type="text/javascript" src="node_modules/material-components-web/dist/material-components-web.js"></script>
  <script type="text/javascript" src="assets/main.js"></script>
  <script type="text/javascript" src="assets/menu.js"></script>
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
        <button class="material-icons mdc-toolbar__menu-icon">menu</button>
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

  <?php include 'menu.php'; ?>

  <main>
    <div class="mdc-toolbar-fixed-adjust"></div>
<?php

foreach ($allVars as $family => $models) {
    echo '<div id="family-' . strtolower($family) . '" class="panel" role="tabpanel">';

    echo '  <div class="mdc-card info-card">';
    echo '    <div class="mdc-typography--body1">';
    echo '      <div class="title"><span class="material-icons">info</span> How to find your CU Reference (PRD) number</div>';
    echo '      <div>Open your phone dialer and enter this code: <tt>*#837837#</tt> (<tt>*#TESTER#</tt>).</div>';
    echo '    </div>';
    echo '  </div>';

    foreach ($models as $model => $variants) {
        echo '<h2>' . $family . ' ' . $model . '</h2>' . PHP_EOL;
        $allVersions = $db->getAllVersionsForModel($model);
        echo '<table><tbody>';
        foreach ($variants as $ref => $name) {
            echo '<tr data-ref="' . $ref . '"><th class="ref">';
            if (mb_strlen($name) > 0) {
                echo '<abbr title="' . $name . '">' . $ref . '</abbr>';
            } else {
                echo $ref;
            }
            echo '</th>';
            $refVersions = $db->getAllVersionsForRef($ref);
            $allOta      = $db->getAllVersionsForRef($ref, $db::OTA_ONLY);
            $allFull     = $db->getAllVersionsForRef($ref, $db::FULL_ONLY);
            foreach ($allVersions as $v) {
                if (in_array($v, $refVersions, true)) {
                    $moreClasses = '';
                    if (!in_array($v, $allOta) && !in_array($v, $allFull)) {
                        $moreClasses = ' nofiles';
                    } elseif (!in_array($v, $allOta)) {
                        $moreClasses = ' fullonly mdc-theme--secondary-dark';
                    } elseif (!in_array($v, $allFull)) {
                        $moreClasses = ' otaonly mdc-theme--primary-dark';
                    }
                    echo '<td class="version' . $moreClasses . '">' . $v . '</td>';
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
    <div id="tooltip" class="mdc-card">
      <section class="tooltip-card__primary">
        <h1 id="tooltip-title" class="tooltip-card__title mdc-typography--title">Title</h1>
      </section>
      <section id ="tooltip-text" class="tooltip-card__supporting-text">
        Contents here.
      </section>
    </div>

    <div class="mdc-snackbar" aria-live="assertive" aria-atomic="true" aria-hidden="true">
      <div class="mdc-snackbar__text"></div>
      <div class="mdc-snackbar__action-wrapper">
        <button type="button" class="mdc-snackbar__action-button"></button>
      </div>
    </div>

  </main>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(["disableCookies"]);
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//analytics.birth-online.de/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '4']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//analytics.birth-online.de/piwik.php?idsite=4&rec=1" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
</body>
</html>
