<?php
// Загружаем XML из файла
$xmlFile = 'XML/eksamid.xml'; // Замените на путь к вашему XML файлу
$xml = new DOMDocument();
$xml->load($xmlFile);

// Конвертируем XML в массив
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

// Конвертируем массив в JSON
$jsonData = json_encode($xmlArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Сохраняем JSON в файл
$jsonFile = 'eksamid.json';
file_put_contents($jsonFile, $jsonData);

// Добавляем имена в JSON
$jsonContent = json_decode(file_get_contents($jsonFile), true);

file_put_contents($jsonFile, json_encode($jsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "JSON-faili loomine ja värskendamine õnnestus: $jsonFile";
?>
