<?php
function arrayToXml($data, &$xmlData, $rootElement = null) {
    // Если указан корневой элемент, создаем его
    if ($rootElement !== null) {
        $xmlData->startElement($rootElement);
    }

    // Проходим по массиву и добавляем элементы в XML
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Если значение - это массив, рекурсивно вызываем функцию
            $subnode = $xmlData->addChild($key);
            arrayToXml($value, $subnode);
        } else {
            // Иначе добавляем элемент с соответствующим значением
            $xmlData->addChild($key, htmlspecialchars($value));
        }
    }

    // Закрываем корневой элемент, если он был задан
    if ($rootElement !== null) {
        $xmlData->endElement();
    }
}

// Путь к JSON файлу
$jsonFile = 'eksamid.json'; // Укажите путь к вашему JSON файлу

// Проверка существования файла JSON
if (file_exists($jsonFile)) {
    // Чтение JSON данных из файла
    $jsonData = file_get_contents($jsonFile);

    // Декодируем JSON в массив
    $dataArray = json_decode($jsonData, true);

    if ($dataArray !== null) {
        // Создаем новый объект XML
        $xmlData = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><eksamid></eksamid>');

        // Преобразуем массив в XML
        arrayToXml($dataArray, $xmlData);

        // Сохранение XML в файл
        $xmlData->asXML('output.xml'); // Укажите путь для сохранения XML файла

        echo "XML genereeriti edukalt ja salvestati faili output.xml.";
    } else {
        echo "Viga: vale JSON-vorming.";
    }
} else {
    echo "Viga: JSON-faili ei leitud.";
}

?>
