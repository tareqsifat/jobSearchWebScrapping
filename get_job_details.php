<?php


$allLinks = []; // This will hold all links from all files
$folder = 'category_jobs';
// $htmlFile = 'category_jobs/Accountant-Night-Shift-Care-Guide-Bdjobscom.html';
try {
    $files = glob("$folder/*.html");
    foreach ($files as $htmlFile) {
        $allLinks = [];
        echo "Processing $htmlFile\n";
        $links = htmlToJson($htmlFile); // Process each HTML file and get its links
        $allLinks = array_merge($allLinks, $links); // Merge into a single array
        $jsonFile = 'category_job_data/' . str_replace('.html', '.json', explode('/',$htmlFile)[1]); // Replace the extension with .json
        saveJson($jsonFile, $allLinks);
    }
     // Save the combined links to the JSON file
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

function htmlToJson(string $htmlFile) {
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
    $job_details =[];
    // Find the target div
    $xpath = new DOMXPath($dom);
    $divs = $xpath->query('//div[contains(@class, "csumheader")]');
    $companyNameNode = $xpath->query('//h2[@class="cname"]/text()')->item(0);
    $companyName = $companyNameNode ? $companyNameNode->nodeValue : null;

    $jobTitleNode = $xpath->query('//h2[@class="jtitle"]/text()')->item(0);
    $jobTitle = $jobTitleNode ? $jobTitleNode->nodeValue : null;

    $applicationDeadlineNode = $xpath->query('//span[@class="deadlinetxt"]/text()')->item(0);
    $application_deadline = $applicationDeadlineNode ? $applicationDeadlineNode->nodeValue : null;
    $summary = [];
    $summaryItems = $xpath->query('//div[@class="summery__crd"]//ul[@class="summery__items"]/li');

    foreach ($summaryItems as $item) {
        $text = trim($item->textContent);
        if (strpos($text, 'Vacancy:') !== false) {
            $summary['Vacancy'] = trim(str_replace('Vacancy:', '', $text));
        } elseif (strpos($text, 'Age:') !== false) {
            $summary['Age'] = trim(str_replace('Age:', '', $text));
        } elseif (strpos($text, 'Location:') !== false) {
            $summary['Location'] = trim(str_replace('Location:', '', $text));
        } elseif (strpos($text, 'Salary:') !== false) {
            $summary['Salary'] = trim(str_replace('Salary:', '', $text));
        } elseif (strpos($text, 'Experience:') !== false) {
            $summary['Experience'] = trim(str_replace('Experience:', '', $text));
        } elseif (strpos($text, 'Published:') !== false) {
            $summary['Published'] = trim(str_replace('Published:', '', $text));
        }
    }
    $job_content = [];
    $jobContentDivs = $xpath->query('//div[contains(@class, "jobcontent")]');
    foreach ($jobContentDivs as $div) {
        $classes = explode(' ', $div->getAttribute('class'));
        foreach ($classes as $class) {
            if ($class !== 'jobcontent') {
                $variableName = $class;
                break;
            }
        }
        if (!isset($variableName)) {
            $variableName = 'jobcontent';
        }
        $job_content[$variableName] = $dom->saveHTML($div);
    }





    // Add education details to job_details array
    $job_details = [
        'companyName' => $companyName,
        'jobTitle' => $jobTitle,
        'application_deadline' => $application_deadline,
    ];
    // Add the summary details as individual key-value pairs in the job_details array
    foreach ($summary as $key => $value) {
        $job_details[$key] = $value;
    }
    foreach ($job_content as $key => $value) {
        $job_details[$key] = $value;
    }


    return $job_details;

}


function saveJson($jsonFile, $job_details) {
    // Save the extracted links to a JSON file
    if (file_put_contents($jsonFile, json_encode($job_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
        echo "Links saved to $jsonFile\n";
    } else {
        throw new Exception('Failed to save links to JSON file.');
    }
}