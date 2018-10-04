<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>BlackBerry Hub Supported Apps</title>
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

$appsList = [
    'com.google.android.apps.messaging' => 'Android Messages',
    'com.google.android.dialer' => 'Android Phone',
    'com.bbm.enterprise' => 'BBM Enterprise',
    'com.bbm' => 'BlackBerry Messenger',
    'com.contapps.android' => 'Contacts+',
    'com.discord' => 'Discord',
    'com.facebook.katana' => 'Facebook',
    'com.facebook.orca' => 'Facebook Messenger',
    'com.facebook.lite' => 'Facebook Lite',
    'com.facebook.mlite' => 'Facebook Messenger Lite',
    'com.google.android.talk' => 'Google Hangouts',
    'com.groupme.android' => 'GroupMe',
    'com.instagram.android' => 'Instagram',
    'kik.android' => 'Kik',
    'com.kakao.talk' => 'KakaoTalk',
    'jp.naver.line.android' => 'LINE',
    'com.linecorp.linelite' => 'LINE Lite',
    'com.linkedin.android' => 'LinkedIn',
    'com.microsoft.teams' => 'Microsoft Teams',
    'com.pinterest' => 'Pinterest',
    'com.tencent.mobileqq' => 'QQ',
    'com.tencent.mobileqqi' => 'QQ International',
    'com.tencent.qqlite' => 'QQ Lite',
    'com.qzone' => 'QQ 空间',
    'com.reddit.frontpage' => 'Reddit',
    'com.remind101' => 'Remind',
    'org.thoughtcrime.securesms' => 'Signal',
    'com.skype.raider' => 'Skype',
    'com.skype.rover' => 'Skype (CN)',
    'com.skype.m2' => 'Skype Lite',
    'com.microsoft.office.lync15' => 'Skype for Business',
    'com.Slack' => 'Slack',
    'org.telegram.messenger' => 'Telegram',
    'com.enflick.android.TextNow' => 'TextNow',
    'com.tumblr' => 'Tumblr',
    'com.twitter.android' => 'Twitter',
    'com.viber.voip' => 'Viber Messenger',
    'com.tencent.mm' => 'WeChat',
    'com.whatsapp' => 'WhatsApp Messenger',
    'com.whatsapp.w4b' => 'WhatsApp Business',
    'com.xing.android' => 'XING',
    'com.yahoo.mobile.client.android.mail' => 'Yahoo Mail',
    'com.zing.zalo' => 'Zalo',
];

natsort($appsList);

?>
  <header class="mdc-toolbar mdc-toolbar--fixed">
    <div class="mdc-toolbar__row">
      <section class="mdc-toolbar__section mdc-toolbar__section--shrink-to-fit mdc-toolbar__section--align-start">
        <button class="material-icons mdc-toolbar__menu-icon">menu</button>
        <span class="mdc-toolbar__title">BlackBerry Hub Supported Apps (October 2018)</span>
      </section>
    </div>
  </header>

  <?php include 'menu.php'; ?>

  <main>
    <div class="mdc-toolbar-fixed-adjust"></div>
    <div class="app-main">
<?php

foreach ($appsList as $id => $name) {
    $appIcon = 'assets/app_icons/no_icon.png';
    if (file_exists('assets/app_icons/' . $id . '.png')) {
        $appIcon = 'assets/app_icons/' . $id . '.png';
    }

    echo '<div class="mdc-card app-card">';
    echo '<div class="mdc-typography--body1">';
    echo '<div class="app-icon"><a href="https://play.google.com/store/apps/details?id=' . rawurlencode($id) . '"><img src="' . $appIcon . '" width="128" height="128" /></a></div>';
    echo '<div class="app-name"><a href="https://play.google.com/store/apps/details?id=' . rawurlencode($id) . '">' . $name . '</a></div>';
    echo '</div></div>';
}

?>
  </div>
  </main>
</body>
</html>
