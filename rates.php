<?php
include_once 'config.php';

function fetchAndSaveExchangeRates() {
    // Load Place_XML
    $placeXml = simplexml_load_file(Place_XML);

    

    // Replace 'YOUR_ACCESS_KEY' with your actual currencylayer API access key
    $base_currency = BASE_CURRENCY;
    $currencies = 'AUD,BRL,CAD,CHF,CNY,DKK,EUR,GBP,HKD,HUF,INR,JPY,MXN,MYR,NOK,NZD,PHP,RUB,SEK,SGD,THB,TRY,USD,ZAR';
    $api_url = API_URL . "?access_key=" . ACCESS_KEY . "&currencies=$currencies&source=" . BASE_CURRENCY . "&format=1";

    // Replace 'BASE_VALUE' with the desired base value
    $base_value = 1;

    // Fetch data from the API
    $response = file_get_contents($api_url);

    if ($response === false) {
        echo "Failed to fetch data from the API.";
        return;
    }

    $data = json_decode($response, true);

    if (!$data || !isset($data['quotes'])) {
        echo "Error decoding API response.";
        return;
    }

    // Get the current date and time
    $currentDateTime = date('Y-m-d H:i:s');

    // Create a new DOMDocument object
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true; // Enable formatting

    // Create the root <rates> element
    $rates = $dom->createElement('rates');
    $dom->appendChild($rates);

    // Set attributes for the root element
    $rates->setAttribute('ts', $currentDateTime);
    $rates->setAttribute('base', $base_currency);

    // Function to add currency elements to the XML
    foreach ($data['quotes'] as $currency => $rate) {
        // Remove 'GBP' prefix from currency code if it exists
        $currency = str_replace($base_currency, '', $currency);

        // Create a new <currency> element
        $currencyElement = $dom->createElement('currency');
        $rates->appendChild($currencyElement);

        // Add a <code> element with the currency code
        $codeElement = $dom->createElement('code', $currency);
        $currencyElement->appendChild($codeElement);

        // Add a 'rate' attribute with the adjusted exchange rate
        $currencyElement->setAttribute('rate', $rate);

        // Add name and loc if available from Place_XML
        $currencyInfo = $placeXml->xpath("//ISO_4217/CcyTbl/CcyNtry[Ccy = '$currency']");
        if (!empty($currencyInfo)) {
            $nameElement = $dom->createElement('name', (string) $currencyInfo[0]->CcyNm);
            $currencyElement->appendChild($nameElement);

            $locations = [];
            foreach ($currencyInfo as $info) {
                $locations[] = (string) $info->CtryNm;
            }
            $locElement = $dom->createElement('loc', implode(', ', $locations));
            $currencyElement->appendChild($locElement);
        }

        // Add 'live' attribute
        $currencyElement->setAttribute('live', '1');
    }

    // Add GBP manually with a rate of 1.0
    $currencyElementGBP = $dom->createElement('currency');
    $rates->appendChild($currencyElementGBP);

    $codeElementGBP = $dom->createElement('code', 'GBP');
    $currencyElementGBP->appendChild($codeElementGBP);

    $currencyElementGBP->setAttribute('rate', '1.0');
    $currencyElementGBP->setAttribute('live', '1');

    $nameElementGBP = $dom->createElement('name', 'Pound Sterling');
    $currencyElementGBP->appendChild($nameElementGBP);

    $locElementGBP = $dom->createElement('loc', 'UNITED KINGDOM OF GREAT BRITAIN AND NORTHERN IRELAND (THE), ISLE OF MAN, JERSEY, GUERNSEY');
    $currencyElementGBP->appendChild($locElementGBP);

    // Save the XML to a file named rates.xml
    $dom->save(Rate_XML);
}
// Call the function to fetch and save exchange rates
fetchAndSaveExchangeRates();
?>
