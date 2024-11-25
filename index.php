<?php
include 'config.php';
require 'Function_index.php';
require_once 'updatexml.php';

// Check if the required parameters are set in the request
if (isset($_GET['from'], $_GET['to'], $_GET['amnt'])) {
    $fromCurrency = $_GET['from'];
    $toCurrency = $_GET['to'];
    $amount = floatval($_GET['amnt']);
    $format = isset($_GET['format']) ? $_GET['format'] : 'xml';
    //Default format is XML

    
    // checking if xml file is loaded successfully and extract vilid currency coeds from XML data
    $currxml = simplexml_load_file(Rate_XML);

    if($currxml){
                    $validcurr =[];
                    foreach ($currxml->currency as $currency) {
                        $validcurr[] = (string) $currency->code;
                    }

                    //checking if the provided from currency is recongnized
                    if(!in_array($fromCurrency, $validcurr) || !in_array($toCurrency, $validcurr)) {
                       // Return error message if the currency type is not recognized
                        if ($format === 'json') {
                            return generateError(ERROR_CURRENCY_TYPE_NOT_RECOGNIZED, ERROR_MSG_CURRENCY_TYPE_NOT_RECOGNIZED, 'json');
                        } else {
                            return generateError(ERROR_CURRENCY_TYPE_NOT_RECOGNIZED, ERROR_MSG_CURRENCY_TYPE_NOT_RECOGNIZED, 'xml');
                        }
                    }
                } else {
                    // Error of fail to load xml file
                    // Handle error if the XML file cannot be loaded
                    if ($format === 'json') {
                        return generateError(ERROR_SERVICE_ERROR, ERROR_MSG_SERVICE_ERROR, 'json');
                    } else {
                        return generateError(ERROR_SERVICE_ERROR, ERROR_MSG_SERVICE_ERROR, 'xml');
                    }
                }

        if (!is_numeric($amount) || floatval($amount) <= 0 || $amount != number_format($amount,2,'.', '')) {
            // Return error message if the currency amount is invalid
            if ($format === 'json') {
                return generateError(ERROR_INVALID_CURRENCY_AMOUNT, ERROR_MSG_CURRENCY_AMOUNT_NOT_DECIMAL, 'json');
            } else {
                return generateError(ERROR_INVALID_CURRENCY_AMOUNT, ERROR_MSG_CURRENCY_AMOUNT_NOT_DECIMAL, 'xml');
            }
        }    

    // Perform currency conversion
    $conversionResult = convertCurrency($fromCurrency, $toCurrency, $amount, $format);


    // Return response based on format
    if ($format === 'json') {
        // If format is 'json', set Content-Type header and echo JSON result
        header('Content-Type: application/json');
        echo $conversionResult;
    } elseif ($format === 'xml' || '') {
        // If format is not 'json', set Content-Type header and echo XML result
        header('Content-Type: application/xml');
        echo $conversionResult;
    } else {
         if ($format === 'json') {
                // Return the result as JSON if format is specified as 'json'
                return  generateError(ERROR_INVALID_FORMAT, ERROR_MSG_FORMAT_NOT_XML_OR_JSON, 'json');
            } else {
                // Otherwise, return the result as XML
                return generateError(ERROR_INVALID_FORMAT, ERROR_MSG_FORMAT_NOT_XML_OR_JSON,'xml');
            }
    }
} else {
    // Check if any required parameters are missing
    $missingParams = array_diff(Require_Parameters, array_keys($_GET));
    
    // Check if any parameters are misspelled
    $recognizedParams = array('from', 'to', 'amnt', 'format');
    $unrecognizedParams = array_diff(array_keys($_GET), $recognizedParams);
    
    // Return error message for misspelled parameters
    if (!empty($unrecognizedParams)) {
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            echo generateError(ERROR_PARAMETER_NOT_RECOGNIZED, ERROR_MSG_PARAMETER_NOT_RECOGNIZED, 'json');
        } else {
            echo generateError(ERROR_PARAMETER_NOT_RECOGNIZED, ERROR_MSG_PARAMETER_NOT_RECOGNIZED, 'xml');
        }
    } else {
        // Return error message for missing parameters
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            echo generateError(ERROR_MISSING_PARAMETER, ERROR_MSG_MISSING_PARAMETERS, 'json');
        } else {
            echo generateError(ERROR_MISSING_PARAMETER, ERROR_MSG_MISSING_PARAMETERS, 'xml');
        }   
    }
}
fetchAndUpdateIfNeeded();
?>