<?php
// loading xml files
define('Rate_XML', 'data/rates.xml');
define('Place_XML', 'data/list-one.xml');

// Define the required parameters
define ('Require_Parameters', array('from', 'to', 'amnt'));

// define('Place_XML', 'https://www.six-group.com/dam/download/financial-information/data-center/iso-currrency/lists/list-one.xml');

// CurrencyLayer API configuration
define('API_URL', "http://apilayer.net/api/live");
define('ACCESS_KEY','d8d246f008022e90cef273c75bccc89c');
define('BASE_CURRENCY', 'GBP');

// Define error messages as extact constants of currency and parameter

define('ERROR_MISSING_PARAMETER', '1000');
define('ERROR_PARAMETER_NOT_RECOGNIZED', '1100');
define('ERROR_CURRENCY_TYPE_NOT_RECOGNIZED', '1200');
define('ERROR_INVALID_CURRENCY_AMOUNT', '1300');
define('ERROR_INVALID_FORMAT', '1400');
define('ERROR_SERVICE_ERROR', '1500');


define('ERROR_MSG_MISSING_PARAMETERS', 'Required parameter is missing.');
define('ERROR_MSG_PARAMETER_NOT_RECOGNIZED', 'Parameter not recognized.');
define('ERROR_MSG_CURRENCY_TYPE_NOT_RECOGNIZED', 'Currency type not recognized.');
define('ERROR_MSG_CURRENCY_AMOUNT_NOT_DECIMAL', 'Currency amount must be a decimal number.');
define('ERROR_MSG_FORMAT_NOT_XML_OR_JSON', 'Format must be XML or JSON.');
define('ERROR_MSG_SERVICE_ERROR', 'Error in service.');


// Define error messages as extact constants of actions
define('CODE_ACTION_NOT_RECOGNIZED', '2000');
define('CODE_CURR_CODE_MISSING', '2100');
define('CODE_CURR_CODE_NOT_FOUND', '2200');
define('CODE_NO_RATED_LIST', '2300');
define('CODE_CANNOT_UPDATE_CURR', '2400');
define('CODE_ERROR_IN_SERVICE', '2500');


define('MSG_ACTION_NOT_RECOGNIZED','Action not recognized or is missing');
define('MSG_CURR_CODE_MISSING','Currency code in wrong format or is missing');
define('MSG_CURR_CODE_NOT_FOUND','Currency code not found');
define('MSG_NO_RATED_LIST','No rate listed for this currency');
define('MSG_CANNOT_UPDATE_CURR','Cannot update base currency');
define('MSG_ERROR_IN_SERVICE','Error in service');

define('ERROR_UPDATE_TEMPLATE', 
'<?xml version="1.0" encoding="UTF-8"?>
    <error>
        <code>{error_code}</code>
        <msg>{error_msg}</msg>
    </error>');


define('XML_ACTION_RESPOND_TEMPLATE', 
'<?xml version="1.0" encoding="UTF-8"?>
    <action type="%s">
        <at>%s</at>
        <rate>%s</rate>
        <curr>
            <code>%s</code>
            <name>%s</name>
            <loc>%s</loc>
        </curr>
    </action>');

define('XML_PUT_ACTION_RESPOND_TEMPLATE', 
'<?xml version="1.0" encoding="UTF-8"?>
    <action type="put">
        <at>%s</at>
        <rate>%s</rate>
        <old_rate>%s</old_rate>
        <curr>
            <code>%s</code>
            <name>%s</name>
            <loc>%s</loc>
        </curr>
    </action>');

define('XML_DEL_ACTION_RESPOND_TEMPLATE', 
'<?xml version="1.0" encoding="UTF-8"?>
    <action type="del">
        <at>%s</at>
        <code>%s</code>
    </action>');
?>