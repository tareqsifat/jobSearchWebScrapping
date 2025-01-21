<?php

// Specify the HTML file to read
$htmlFile = 'home_page_industrial.html'; // Replace with your actual file name
$jsonFile = 'industrial_category.json'; // JSON file to save the extracted data

try {
    // Check if the HTML file exists
    if (!file_exists($htmlFile)) {
        throw new Exception("File $htmlFile does not exist.");
    }

    // Load the HTML content
    $htmlContent = file_get_contents($htmlFile);

    // Use DOMDocument to parse the HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
    $dom->loadHTML($htmlContent);
    libxml_clear_errors();

    // Find the target div
    $xpath = new DOMXPath($dom);
    $divs = $xpath->query('//div[contains(@class, "category-list") and contains(@class, "padding-mobile") and contains(@class, "industrial") and contains(@class, "active")]');

    $links = [];

    foreach ($divs as $div) {
        // Find all <a> elements inside this div
        $anchors = $div->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $href = $anchor->getAttribute('href') ?: null;
            $title = $anchor->getAttribute('title') ?: null;

            if ($href && $title) {
                echo "https:$href\n";
                $links[] = [
                    'href' => "https:$href",
                    'title' => $title,
                ];
            }
        }
    }

    // Save the extracted links to a JSON file
    if (file_put_contents($jsonFile, json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
        echo "Links saved to $jsonFile\n";
    } else {
        throw new Exception('Failed to save links to JSON file.');
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
