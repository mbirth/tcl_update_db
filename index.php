<?php

require_once __DIR__ . '/lib/autoloader.php';

use \TclUpdates\GotuObject;
use \TclUpdates\SQLiteWriter;
use \TclUpdates\XmlParser;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_xml = file_get_contents('php://input', false, null, -1, 8192);   // read max 8 KiB
    if (strlen($input_xml) >= 8192) {
        // Max length, probably even longer, definitely no data we want
        http_response_code(413);   // "Payload too large"
        exit;
    }
    $xp = new XmlParser();
    $load_ok = $xp->loadXmlFromString($input_xml);
    if (!$load_ok) {
        // XML could not be parsed - invalid or no XML
        http_response_code(406);   // "Not acceptable"
        exit;
    }
    if (!$xp->validateGOTU()) {
        // No root node or root node isn't <GOTU>, so no update XML
        http_response_code(412);   // "Precondition failed"
        exit;
    }
    // ### At this point we can be relatively sure to have the XML we want
    echo "Input length is " . strlen($input_xml) . " Bytes." . PHP_EOL;
    #echo $input_xml . PHP_EOL;

    // Write backup copy (for maybe re-parsing later)
    $bkup_dir = __DIR__ . '/data/';
    if (!is_dir($bkup_dir)) {
        mkdir($bkup_dir);
    }
    $bkup_filename = $bkup_dir . sprintf('%f-%04x.xml', microtime(true), rand(0, 65535));
    file_put_contents($bkup_filename, $input_xml);

    // Parse XML into database
    $g = GotuObject::fromXmlParser($xp);
    if ($g->tv) {
        $sqlw = new SQLiteWriter();
        $result = $sqlw->addGotu($g);
        if ($result !== false) {
            $config = parse_ini_file(__DIR__ . '/config.ini');
            $type = 'FULL';
            if ($g->fv) {
                $type = 'OTA (from ' . $g->fv . ')';
            }
            $data = array(
                'content' => 'New ' . $type . ' update for ' . $g->curef . ' found: ' . $g->tv,
            );
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ),
            );
            $ctx = stream_context_create($options);
            $notify_ok = file_get_contents($config['NOTIFY_WEBHOOK'], false, $ctx);
        }
        // I don't care if we can use the data or not. Maybe we can use it later (backup copy).
    }

    exit;
}

require_once 'index_main.php';
