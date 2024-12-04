<?php

function jsonToXml($json, $rootElement = 'root', $xml = null) {
    // Decode the JSON string into a PHP array
    $array = json_decode($json, true);

    // Create a new XML element if not already created
    if ($xml === null) {
        $xml = new SimpleXMLElement("<$rootElement/>");
    }

    // Iterate through the array and build the XML structure
    foreach ($array as $key => $value) {
        // If the key is numeric, we can use a generic name
        if (is_numeric($key)) {
            $key = 'item' . $key; // Change numeric keys to item0, item1, etc.
        }

        // If the value is an array, we need to recurse
        if (is_array($value)) {
            jsonToXml(json_encode($value), $key, $xml->addChild($key));
        } else {
            // Otherwise, just add the value as a child element
            $xml->addChild($key, htmlspecialchars($value));
        }
    }

    return $xml->asXML();
}

// Example usage
$jsonData = '{"name": "John", "age": 30, "city": "New York", "hobbies": ["reading", "traveling"]}';
$xmlData = jsonToXml($jsonData, 'person');
header('Content-Type: application/xml');
echo $xmlData;
?>