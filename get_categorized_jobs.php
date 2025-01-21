<?php
$jsonFile = 'Categorized-jobs.json'; // JSON file to save the extracted data
$folder = 'category_job_lists';
$allLinks = []; // This will hold all links from all files

try {
    $files = glob("$folder/*.html");
    foreach ($files as $htmlFile) {
        $links = htmlToJson($htmlFile); // Process each HTML file and get its links
        $allLinks = array_merge($allLinks, $links); // Merge into a single array
    }
    saveJson($jsonFile, $allLinks); // Save the combined links to the JSON file
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

function htmlToJson(string $htmlFile) {
    if (!file_exists($htmlFile)) {
        throw new Exception("File $htmlFile does not exist.");
    }

    $htmlContent = file_get_contents($htmlFile);
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
    $dom->loadHTML($htmlContent);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Query both featured jobs and sout-jobs-wrapper
    $nodes = $xpath->query('//div[contains(@class, "col-sm-12 col-md-6 featured-job")] | //div[contains(@class, "sout-jobs-wrapper")]');
    $links = [];

    foreach ($nodes as $node) {
        $anchors = $node->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $href = $anchor->getAttribute('href');
            if ($href) {
                $href = "https://jobs.bdjobs.com/" . $href; // Add base URL
            }
            $title = $anchor->textContent ?: null;
            if ($href && $title) {
                $title = preg_replace('/\s+/', ' ', trim($title)); // Clean the title
                $links[] = [
                    'href' => $href,
                    'title' => $title,
                ];
            }
        }
    }
    return $links; // Return only the links for this file
}

function saveJson($jsonFile, $links) {
    if (file_put_contents($jsonFile, json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
        echo "Links saved to $jsonFile\n";
    } else {
        throw new Exception('Failed to save links to JSON file.');
    }
}
