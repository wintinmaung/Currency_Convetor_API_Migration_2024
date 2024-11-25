<?php
// Function to perform currency conversion
function convertCurrency($from, $to, $amount, $format) {
    // Load exchange rates from XML file
    $ratesXml = simplexml_load_file(Rate_XML);
    

    // Check if the XML file is loaded successfully
    if ($ratesXml) {
        // Search for the conversion rates in the XML data
        $fromRate = null;
        $toRate = null;

        foreach ($ratesXml->currency as $currency) {
            $code = (string) $currency->code;
            $rate = (float) $currency['rate'];
            if ($code === $from) {
                $fromRate = $rate;
            }
            if ($code === $to) {
                $toRate = $rate;
            }
            // Break the loop if both rates and currency names, place are found
            if ($fromRate !== null && $toRate !== null) {
                break;
            }
        }
        
        // If the conversion rates are found, perform the currency conversion
            if ($fromRate !== null && $toRate !== null) {
                $convertedAmount = $amount / $fromRate * $toRate;
                $date = date('d M Y H:i:s');

                              
                // Get details of source currency
                    $fromCurr = $ratesXml->xpath("//currency[code = '$from']");
                    foreach ($fromCurr as $currency) {
                        $fromName = (string) $currency->name;
                        $fromCountry = (string) $currency->loc;
                        
                    }

                    // Get details of target currency
                    $toCurr = $ratesXml->xpath("//currency[code = '$to']");
                    foreach ($toCurr as $currency) {
                        $toName = (string) $currency->name;
                        $toCountry = (string) $currency->loc;
                    }               
                

                // Prepare conversion result structure
                $result = [
                    'at' => $date,
                    'from' => [
                        'code' => $from,
                        'rate' => $fromRate,
                        'curr' => $fromName,
                        'loc' => $fromCountry, // Use places for "from" currency
                        'amnt' => $amount,
                    ],
                    
                    'to' => [
                        'code' => $to,
                        'rate' => $toRate,
                        'curr' => $toName,
                        'loc' => $toCountry, // Use places for "to" currency
                        'amnt' => $convertedAmount,
                    ],
                    
                    ];

                
                                
                // Return the result based on the specified format

                        // Convert array to XML
                    $xml = new SimpleXMLElement('<conv></conv>');

                    // Check if $result is an array or object
                    if (is_array($result) || is_object($result)) {
                        foreach ($result as $key => $value) {
                            if ($key === 'from' || $key === 'to') {
                                $child = $xml->addChild($key);
                                foreach ($value as $subKey => $subValue) {
                                    // Check if $subValue is an array or object
                                    if (is_array($subValue) || is_object($subValue)) {
                                        $subChild = $child->addChild($subKey);
                                        foreach ($subValue as $subSubKey => $subSubValue) {
                                            if ($subSubKey === 'loc') {
                                                // Convert location data to XML format
                                                foreach ($subSubValue as $location) {
                                                    $locChild = $subChild->addChild('loc');
                                                    foreach ($location as $locKey => $locValue) {
                                                        $locChild->addChild($locKey, htmlspecialchars($locValue));
                                                    }
                                                }
                                            } else {
                                                if (is_string($subSubValue))
                                                $subChild->addChild($subSubKey, htmlspecialchars($subSubValue));
                                            
                                            }
                                        }
                                    } else {
                                        $child->addChild($subKey, htmlspecialchars($subValue));
                                    }
                                }
                            } else {
                                $xml->addChild($key, htmlspecialchars($value));
                            }
                        }
                    }

                if ($format === 'json') {
                        // Return the result as JSON if format is specified as 'json'
                        return json_encode($result);
                    } else {
                        // Otherwise, return the result as XML
                                    
                        return $xml->asXML();
                    }
                } else {
                    // Handle error if the XML file cannot be loaded
                    if ($format === 'json') {
                        return generateError(ERROR_SERVICE_ERROR, ERROR_MSG_SERVICE_ERROR, 'json');
                    } else {
                        return generateError(ERROR_SERVICE_ERROR, ERROR_MSG_SERVICE_ERROR, 'xml');
                    }
                }
            } else {
                // Return error message if conversion rates are not found
                if ($format === 'json') {
                    return generateError(ERROR_PARAMETER_NOT_RECOGNIZED, ERROR_MSG_PARAMETER_NOT_RECOGNIZED, 'json');
                } else {
                    return generateError(ERROR_PARAMETER_NOT_RECOGNIZED, ERROR_MSG_PARAMETER_NOT_RECOGNIZED, 'xml');
                }
            }
}

// Handling Errors whether XML or JSON
function generateError ($errorCode, $errorMessage, $format) {
    $errorArray = array(
        "conv" => array (
            "error"  => [
                'code' => $errorCode,
                'message' => $errorMessage
            ]
        )
    );

        if ($format === 'json') {
            header('Content-Type: application/json');
            echo json_encode($errorArray);
        } else {
            $errorXml = new SimpleXMLElement('<conv></conv>');
            $errorElement = $errorXml->addChild('error');
            $errorElement->addChild('code', $errorCode);
            $errorElement->addChild('msg', $errorMessage);
            
            // Set content type to XML
            header('Content-Type: text/xml');
            
            // Output the XML
            echo $errorXml->asXML();
        }
}
?>