<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_xml = file_get_contents('php://input', false, NULL, -1, 8192);   // read max 8 KiB
    echo "Input length is " . strlen($input_xml) . " Bytes." . PHP_EOL;
    echo $input_xml . PHP_EOL;
    // TODO: Check if it's XML
    //       If so: Store a copy for re-parsing (or to print out and hang up on a wall)
    //       Then parse XML into database
    exit;
}

echo "Here is the normal page.";


// TODO: Show statistics from database
