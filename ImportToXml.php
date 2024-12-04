<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['jsonFile'])) {
    if ($_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {
        $jsonContent = file_get_contents($_FILES['jsonFile']['tmp_name']);

        function jsonToFormattedXml($array, $rootElement = 'root', $level = 0) {
            $indent = str_repeat("    ", $level);
            $xml = $level === 0 ? "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<$rootElement>\n" : "";

            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                if (is_array($value)) {
                    $xml .= "$indent    <$key>\n";
                    $xml .= jsonToFormattedXml($value, $rootElement, $level + 1);
                    $xml .= "$indent    </$key>\n";
                } else {
                    $xml .= "$indent    <$key>" . htmlspecialchars($value) . "</$key>\n";
                }
            }

            if ($level === 0) {
                $xml .= "</$rootElement>\n";
            }

            return $xml;
        }

        $arrayData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Kehtetu vorming JSON!";
            exit;
        }

        $xmlData = jsonToFormattedXml($arrayData);

        header('Content-Description: File Transfer');
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="converted.xml"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        echo $xmlData;
        exit;
    } else {
        echo "Faili allalaadimise viga!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON to XML teisenda</title>
</head>
<body>
<h1>Laadige XML-i teisendamiseks üles JSON-fail</h1>
<form method="POST" enctype="multipart/form-data">
    <label for="jsonFile">Valige JSON-fail:</label>
    <input type="file" name="jsonFile" id="jsonFile" accept="application/json" required>
    <button type="submit">Teisenda</button>
</form>
</body>
</html>
