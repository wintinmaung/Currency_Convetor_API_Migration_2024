<?php
$currencies = 'AUD,BRL,CAD,CHF,CNY,DKK,EUR,GBP,HKD,HUF,INR,JPY,MXN,MYR,NOK,NZD,PHP,RUB,SEK,SGD,THB,TRY,USD,ZAR';
// Fetching New_Rate from API
function fetchNewRateFromAPI($currency_code) {
    // Construct API URL with access key
    $api_url = API_URL . "?access_key=" . urlencode(ACCESS_KEY) . "&currencies=" . urlencode($currency_code) . "&source=" . urlencode(BASE_CURRENCY) . "&format=1";

    // Perform API request and parse response (assuming JSON response)
    $response = file_get_contents($api_url);

    // Decode JSON response into an associative array
    $data = json_decode($response, true);

    // Check if JSON decoding was successful and if the target currency pair exists in the response
    if ($data !== null && isset($data['quotes'][BASE_CURRENCY . $currency_code])) {
        // Return the exchange rate for the target currency pair
        return $data['quotes'][BASE_CURRENCY . $currency_code];
    }

    // Return null if the exchange rate is not found or if an error occurred
    return null;
}

// Fetching Old_rate from RATE_XML
function fetchOldRateFromXML($currency_code) {
    // Fetch the old rate from the XML file
    $xml = simplexml_load_file('../data/rates.xml');
    $currency = $xml->xpath("//currency[code='$currency_code']");
    
    if (!empty($currency)) {
        // If the currency is found, extract its rate attribute
        $old_rate = (float)$currency[0]['rate'];
        return $old_rate;
    } else {
        // If the currency is not found, return null
        return null;
    }
}

function fetchNameFromXML($currency_code) {
    $xml = simplexml_load_file('../data/rates.xml');
    $currency = $xml->xpath("//currency[code='$currency_code']");
    if (!empty($currency)) {
        // If the currency is found, extract its rate attribute
        $name = $xml->xpath("//currency[code = '$currency_code']/name");
        // Check if name and loc are fetched successfully
            if (!empty($name)) {
                $name = (string) $name[0];
            } else {
                // Set default values if name and loc are not found
                $name = 'Unknown';
            }
        return $name;
    } 
        
}

function fetchLocFromXML($currency_code) {
    $xml = simplexml_load_file('../data/rates.xml');
    $currency = $xml->xpath("//currency[code='$currency_code']");
    if (!empty($currency)) {
        // If the currency is found, extract its rate attribute
        $loc = $xml->xpath("//currency[code = '$currency_code']/loc");
        // Check if name and loc are fetched successfully
            if (!empty($loc)) {
                $loc = (string) $loc[0];
            } else {
                // Set default values if name and loc are not found
                $loc = 'Unknown';
            }
        return $loc;
    } 
        
}

function fetchcodeFromXML($currency_code) {
    $xmlplace = simplexml_load_file('../data/list-one.xml');
    $currency = $xmlplace->xpath("//ISO_4217/CcyTbl/CcyNtry[Ccy='".$currency_code."']");
    if (!empty($currency)) {
        // If the currency is found, extract its rate attribute
        $name = $xmlplace->xpath("//ISO_4217/CcyTbl/CcyNtry[Ccy='".$currency_code."']/CcyNm");
        // Check if name and loc are fetched successfully
            if (!empty($name)) {
                $name = (string) $name[0];
            } else {
                // Set default values if name and loc are not found
                $loc = 'Unknown';
            }
        return $name;
    } 
        
}


function fetchplaceFromXML($currency_code) {
    $xmlplace = simplexml_load_file('../data/list-one.xml');
    $currency = $xmlplace->xpath("//ISO_4217/CcyTbl/CcyNtry[Ccy='".$currency_code."']");
    if (!empty($currency)) {
        // If the currency is found, extract its rate attribute
        $loc = $xmlplace->xpath("//ISO_4217/CcyTbl/CcyNtry[Ccy='".$currency_code."']/CtryNm");

        // Check if name and loc are fetched successfully
            if (!empty($loc)) {
                $loc = (string) $loc[0];
            } else {
                // Set default values if name and loc are not found
                $loc = 'Unknown';
            }
        return $loc;
    } else {
        // If the currency is not found, return null
        return null;
    }
        
}



// Handling Error for action
function handleError($errorCode) {
    // Define error message templates or retrieve from constants
    // Ensure that CODE_ACTION_NOT_RECOGNIZED, CODE_CURR_CODE_MISSING, etc. are defined appropriately
    $errorMessages = [
        CODE_ACTION_NOT_RECOGNIZED => MSG_ACTION_NOT_RECOGNIZED,
        CODE_CURR_CODE_MISSING => MSG_CURR_CODE_MISSING,
        CODE_CURR_CODE_NOT_FOUND => MSG_CURR_CODE_NOT_FOUND,
        CODE_NO_RATED_LIST => MSG_NO_RATED_LIST,
        CODE_CANNOT_UPDATE_CURR => MSG_CANNOT_UPDATE_CURR,
        CODE_ERROR_IN_SERVICE => MSG_ERROR_IN_SERVICE,
    ];

    // Default error message for unknown error codes
    $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Unknown error";

    // Replace placeholders in the error template with actual error code and message
    $errorXML = str_replace(['{error_code}', '{error_msg}'], [$errorCode, $errorMessage], ERROR_UPDATE_TEMPLATE);
    return $errorXML;
}
?>