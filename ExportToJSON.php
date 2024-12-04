<?php
$xmlFile = 'XML/eksamid.xml';
$xml = new DOMDocument();
$xml->load($xmlFile);

function xmlToArray($xmlNode) {
    $result = [];
    if ($xmlNode->hasAttributes()) {
        foreach ($xmlNode->attributes as $attr) {
            $result['@attributes'][$attr->name] = $attr->value;
        }
    }
    foreach ($xmlNode->childNodes as $node) {
        if ($node->nodeType == XML_TEXT_NODE) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                return $text;
            }
        } else {
            $result[$node->nodeName][] = xmlToArray($node);
        }
    }
    return $result;
}

$xmlArray = xmlToArray($xml->documentElement);

$jsonData = json_encode($xmlArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$jsonFile = 'eksamid.json';
file_put_contents($jsonFile, $jsonData);

if (file_exists($jsonFile)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . basename($jsonFile) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($jsonFile));
    readfile($jsonFile);
    exit;
} else {
    echo "JSON-fail ei liednud.";
}
?>
