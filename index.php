<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_xml = file_get_contents('php://input', false, NULL, -1, 8192);   // read max 8 KiB
    if (strlen($input_xml) >= 8192) {
        // Max length, probably even longer, definitely no XML
        http_response_code(413);   // "Payload too large"
        exit;
    }
    $dom = new DOMDocument();
    $load_ok = $dom->loadXML($input_xml, LIBXML_NOENT);
    if (!$load_ok || $dom->childNodes->length < 1) {
        // XML could not be parsed - invalid or no XML
        http_response_code(406);   // "Not acceptable"
        exit;
    }
    $root_node = $dom->childNodes->item(0);
    if ($root_node->nodeName != 'GOTU') {
        // Root node isn't <GOTU>, so no update XML
        http_response_code(412);   // "Precondition failed"
        exit;
    }
    // ### At this point we can be relatively sure to have the XML we want
    echo "Input length is " . strlen($input_xml) . " Bytes." . PHP_EOL;
    echo $input_xml . PHP_EOL;
    // TODO: Check if it's XML
    //       If so: Store a copy for re-parsing (or to print out and hang up on a wall)
    //       Then parse XML into database
    exit;
}

echo "Here is the normal page. " . $_SERVER['REQUEST_METHOD'];


// TODO: Show statistics from database
