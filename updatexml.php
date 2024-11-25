<?php
include_once 'config.php';

function fetchAndUpdateIfNeeded() {
    // Load the XML file
    $xml = simplexml_load_file(Rate_XML);

    // Extract the timestamp from the XML
    $timestampString = (string) $xml['ts']; // the timestamp is stored as <timestamp>...</timestamp> in the XML

    // Convert the timestamp string to a Unix timestamp
    $timestamp = strtotime($timestampString);

    // Calculate the time difference
    $currentTimestamp = time();
    $timeDifference = $currentTimestamp - $timestamp;

    // Check if the time difference is greater than 2 hours (7200 seconds)
    if ($timeDifference >= 7200) {
        // Update the timestamp in the XML to the current time
        $xml['ts'] = date("Y m d H:i:s");

        $xml->asXML(Rate_XML);
    } else {
        exit();
    }
}

?>
