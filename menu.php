<?php
$menu_items = array(
    array('index.php', 'view_agenda', 'Version Table'),
    array('timeline.php', 'view_list', 'Version Timeline'),
    array('rss.php', 'rss_feed', 'RSS Feed'),
    '---',
    array('https://github.com/mbirth/tcl_update_db', 'code', 'GitHub Source'),
);
$this_page = basename($_SERVER['SCRIPT_NAME']);
?>

<aside class="mdc-drawer mdc-drawer--temporary">
  <nav class="mdc-drawer__drawer">
    <header class="mdc-drawer__header">
      <div class="mdc-drawer__header-content mdc-theme--text-primary-on-primary mdc-theme--primary-bg">
        BlackBerry/TCL Firmwares
      </div>
    </header>
    <nav class="mdc-drawer__content mdc-list-group">
        <div class="mdc-list">
            <?php
            foreach ($menu_items as $mi) {
                if (is_array($mi)) {
                    if ($mi[0] == $this_page) {
                        echo '<a class="mdc-list-item  mdc-list-item--selected" href="#">' . PHP_EOL;
                    } else {
                        echo '<a class="mdc-list-item" href="' . $mi[0] . '">' . PHP_EOL;
                    }
                    echo '<i class="material-icons mdc-list-item__graphic" aria-hidden="true">' . $mi[1] . '</i>' . $mi[2] . PHP_EOL;
                    echo '</a>' . PHP_EOL;
                } elseif ($mi == '---') {
                    echo '</div>' . PHP_EOL;
                    echo '<hr class="mdc-list-divider"/>' . PHP_EOL;
                    echo '<div class="mdc-list">' . PHP_EOL;
                }
            }
            ?>
      </div>
    </nav>
  </nav>
</aside>
