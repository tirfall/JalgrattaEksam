<?php

// Load XML data
$xml = new DOMDocument;
$xml->load('XML/eksamid.xml'); // Replace with the path to your XML file

// Get search and filter inputs
$searchName = isset($_POST['name']) ? $_POST['name'] : '';
$filterDate = isset($_POST['date']) ? $_POST['date'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id'; // Default sort by ID
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Default sort order

// Filter the XML based on search and filter criteria
$filteredXml = new DOMDocument;
$filteredXml->appendChild($filteredXml->createElement('eksamid'));

foreach ($xml->getElementsByTagName('eksam') as $eksam) {
    $examinerName = $eksam->getElementsByTagName('eksamineerijanimi')->item(0)->nodeValue;
    $studentName = $eksam->getElementsByTagName('opilanenimi')->item(0)->nodeValue;
    $examDate = $eksam->getElementsByTagName('eksamiaeg')->item(0)->nodeValue;

    // Check if the exam matches the search and filter criteria
    if ((empty($searchName) || stripos($examinerName, $searchName) !== false || stripos($studentName, $searchName) !== false) &&
        (empty($filterDate) || strpos($examDate, $filterDate) !== false)) {
        $filteredXml->documentElement->appendChild($filteredXml->importNode($eksam, true));
    }
}

// Sort the filtered XML
$xpath = new DOMXPath($filteredXml);
$sortedXml = new DOMDocument;
$sortedXml->appendChild($sortedXml->createElement('eksamid'));

$nodes = $xpath->query('/eksamid/eksam');
$sortedArray = [];

foreach ($nodes as $node) {
    $sortedArray[] = $node;
}

// Custom sorting function
usort($sortedArray, function ($a, $b) use ($sortColumn, $sortOrder) {
    $aValue = $a->getElementsByTagName($sortColumn)->item(0)->nodeValue;
    $bValue = $b->getElementsByTagName($sortColumn)->item(0)->nodeValue;

    if ($aValue == $bValue) {
        return 0; // Equal
    }

    if ($sortOrder === 'asc') {
        return ($aValue < $bValue) ? -1 : 1; // Ascending order
    } else {
        return ($aValue > $bValue) ? -1 : 1; // Descending order
    }
});

// Append sorted nodes to the new XML document
foreach ($sortedArray as $node) {
    $sortedXml->documentElement->appendChild($sortedXml->importNode($node, true));
}

// Load XSLT
$xslt = new DOMDocument;
$xslt->load('XML/eksamid.xslt'); // Replace with the path to your XSL file

// Configure the transformer
$proc = new XSLTProcessor;
$proc->importStylesheet($xslt); // Attach the XSL stylesheet

// Transform the sorted XML
$html = $proc->transformToXML($sortedXml);

// Output the result
header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Details</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $("#date").datepicker({
                dateFormat: 'yy-mm-dd' // Adjust the format as needed
            });
        });
    </script>
</head>
<body>
<h1>Exam Details</h1>

<form method="POST">
    <label for="name">Search by Name:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="Enter name...">

    <label for="date">Filter by Date:</label>
    <input type="text" id="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>" placeholder="Select date...">

    <button type="submit">Search</button>
</form>

<div>
    <table>
        <thead>
        <tr>
            <th><a href="?sort=eksamineerijanimi&order=<?php echo $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Examiner Name</a></th>
            <th><a href="?sort=opilanenimi&order=<?php echo $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Student Name</a></th>
            <th><a href="?sort=eksamiaeg&order=<?php echo $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Exam Date</a></th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Display the transformed HTML
        if ($html) {
            echo $html;
        } else {
            echo "<p>No results found.</p>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>