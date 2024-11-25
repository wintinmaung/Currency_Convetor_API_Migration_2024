<?php
// Include config file
include  '../config.php';
include 'Action_function.php';

$xml = simplexml_load_file('../data/rates.xml');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currency_code = isset($_GET['cur']) ? strtoupper($_GET['cur']) : '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    
    if ($currency_code) {
        // Check if action is provided, if not, return an error message
        if (!$action) {
            $error_xml = handleError(CODE_ACTION_NOT_RECOGNIZED);
            header('Content-Type: application/xml');
            echo $error_xml;
            exit; // Stop further execution
        }


        if ($action === 'post') {
            // Fetch new rate from API
            $new_rate = fetchNewRateFromAPI($currency_code);
            $name = fetchCodeFromXML($currency_code);
            $loc = fetchplaceFromXML($currency_code);

            // Check if new_rate is not null (i.e., API request successful)
            if ($new_rate !== null) {
                

                // Check if the currency code already exists
                $currencyExists = false;
                // Iterate through existing currencies and check if any match the provided currency code
                foreach ($xml->currency as $currency) {
                    if ($currency->code == $currency_code) {
                        $currencyExists = true;
                        break;
                    }
                }

                // If the currency does not exist, add it
                if (!$currencyExists) {
                    $newCurrency = $xml->addChild('currency');
                    $newCurrency->addAttribute('live', '1');
                    $newCurrency->addChild('code', $currency_code);
                    $newCurrency->addAttribute('rate', $new_rate);
                    $newCurrency->addChild('name', $name);
                    $newCurrency->addChild('loc', $loc);
                }

                // Save the changes back to the XML file
                $xml->asXML('../data/rates.xml');
            } else {
                // Currency not found, generate error XML response
                $error_xml = handleError(CODE_CURR_CODE_NOT_FOUND);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
            if ($currency_code == BASE_CURRENCY) {
                // Attempting to update base currency, show error message
                $error_xml = handleError(CODE_CANNOT_UPDATE_CURR);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
            

            // Example response XML for the "post" action
            $response_xml = sprintf(XML_ACTION_RESPOND_TEMPLATE, $action, date("Y m d"), $new_rate, $currency_code, $name, $loc);
            header('Content-Type: application/xml');
            echo $response_xml;
            exit; // Stop further execution
        } elseif ($action === 'del') {
            // Process the form submission and delete currency from the XML file
            if ($currency_code == BASE_CURRENCY) {
                // Attempting to update base currency, show error message
                $error_xml = handleError(CODE_CANNOT_UPDATE_CURR);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
                 
            // Flag to check if currency was found and updated
            $currencyUpdated = false;
        
            // Iterate through currencies and update the live attribute
            foreach ($xml->currency as $currency) {
                if ($currency->code == $currency_code) {
                    if ($currency['live'] == '0') {
                        // Currency already set to live = 0, show message
                        $error_xml = handleError(CODE_NO_RATED_LIST);
                        header('Content-Type: application/xml');
                        echo $error_xml;
                        exit; // Stop further execution
                    }
                    // Set the value of the 'live' attribute to 0
                    $currency['live'] = '0';
                    $currencyUpdated = true;
                    break;
                }
            }
        
            // Save the changes back to the XML file if currency was updated
            if ($currencyUpdated) {
                $xml->asXML('../data/rates.xml');
        
                // Generate XML response for the "del" action
                $response_xml = sprintf(XML_DEL_ACTION_RESPOND_TEMPLATE, date("Y m d"), $currency_code);
                header('Content-Type: application/xml');
                echo $response_xml;
                exit; // Stop further execution
            } else {
                // Currency not found, generate error XML response
                $error_xml = handleError(CODE_CURR_CODE_NOT_FOUND);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
        } elseif ($action === 'put') {
            // Process the form submission and update currency rate in the XML file
            if ($currency_code == BASE_CURRENCY) {
                // Attempting to update base currency, show error message
                $error_xml = handleError(CODE_CANNOT_UPDATE_CURR);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
            // Fetch old rate from XML file
            $old_rate = fetchOldRateFromXML($currency_code);
            $new_rate = fetchNewRateFromAPI($currency_code);
            $name = fetchNameFromXML($currency_code);
            $loc = fetchLocFromXML($currency_code);

                    
            // Check if currency code exists
            $currencyExists = false;
            foreach ($xml->currency as $currency) {
                if ($currency->code == $currency_code) {
                    $currencyExists = true;
                    break;
                }
            }
            

            if (!$currencyExists) {
                // Currency code not found, show error message
                $error_xml = handleError(CODE_CURR_CODE_NOT_FOUND);
                header('Content-Type: application/xml');
                echo $error_xml;
                exit; // Stop further execution
            }
            
            // Extract the timestamp from the XML
            $timestampString = (string)$xml['ts']; //the timestamp is stored as <timestamp>...</timestamp> in the XML

            // Convert the timestamp string to a Unix timestamp
            $timestamp = strtotime($timestampString);

            // Calculate the time difference
            $currentTimestamp = time();

            $timeDifference = $currentTimestamp - $timestamp;
            
            // 2 hours in seconds
        
            if ($timeDifference <= 7200) {               
                // Assuming handleError function handles the error code correctly
                $error_xml = 'Currency Already Update in last 2 hours'; // Assuming CODE_ALREADY_UPDATED is defined
                $response_xml = sprintf(XML_PUT_ACTION_RESPOND_TEMPLATE, date("Y m d"), $old_rate,$error_xml, $currency_code, $name, $loc, );
                header('Content-Type: application/xml');
                echo $response_xml;               
                
                exit;
            // Stop further execution
             } 
             else {
                $currentDateTime = date('Y-m-d H:i:s');
                // Logic to update the XML file with the new rate for the specified currency code
                if ($new_rate === null) {
                    // Handle error if new rate cannot be fetched
                    $error_xml = handleError(CODE_CURR_CODE_NOT_FOUND);
                    header('Content-Type: application/xml');
                    echo $error_xml;
                    exit; // Stop further execution
                }


                //checking currency is existed or not
               
                
                                
                foreach ($xml->currency as $currency) {
                
                    if ($currency->code == $currency_code) {
                        // Currency already exists, update its rate
                        $currency['rate'] = $new_rate;
                        $currency['live'] = "1";
                        $currencyExists = true;
                        break;
                }
                }
                
                if(!$currencyExists) {
                    // Currency code not found, show error message
                    $error_xml = handleError(CODE_CURR_CODE_NOT_FOUND);
                    header('Content-Type: application/xml');
                    echo $error_xml;
                    exit; // Stop further execution
                } 
                $xml['ts'] = $currentDateTime;
                $xml->asXML('../data/rates.xml');

            }           
            // Generate XML response for the "put" action including old rate and new rate
            $response_xml = sprintf(XML_PUT_ACTION_RESPOND_TEMPLATE, date("Y m d"),  $new_rate, $old_rate, $currency_code, $name, $loc);
            header('Content-Type: application/xml');
            echo $response_xml;
            exit; // Stop further execution
        }    
    } else {
        $error_xml = handleError(CODE_CURR_CODE_MISSING);
        header('Content-Type: application/xml');
        echo $error_xml;
        exit; // Stop further execution
    } 
} else {
    $error_xml = handleError(CODE_ERROR_IN_SERVICE);
            header('Content-Type: application/xml');
            echo $error_xml;
            exit; // Stop further execution
}
?>