<?php

// Get the URL from query parameter or define it
// $url = isset($_GET['url']) ? $_GET['url'] : 'https://jobs.bdjobs.com/jobdetails.asp?id=1330023&fcatId=1&ln=1';

$jsonFilePath = 'Categorized-jobs.json'; // Adjust the path if necessary
$jsonData = file_get_contents($jsonFilePath);

// Decode JSON into an associative array
$data = json_decode($jsonData, true);

// Check if decoding was successful
if ($data === null) {
    die('Error decoding JSON');
}
$folderName = 'category_jobs';
// Loop through the array and print the href values
foreach ($data as $item) {
    $url =  $item['href'];

    try {
        $htmlContent = call_curl($url)['content'];
        // $htmlContent = file_get_contents('Accounting-finanace-jobs-in-Bangladesh-Bdjobscom.html');
        
        // $html_hook = '//div[contains(@class, "category-list") and contains(@class, "padding-mobile") and contains(@class, "functional") and contains(@class, "active")]';
        $html_hook = '//title';
    
        if(!empty($htmlContent)){
            $title = ReadGrabHtml($html_hook, $htmlContent);
    
            // Clean the title
            $cleanedTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $title); // Replace special characters with hyphens
            $cleanedTitle = preg_replace('/\s+/', '-', trim($cleanedTitle)); // Replace spaces with hyphens and trim
        
            // Create the filename
            $fileName = $cleanedTitle . '.html';
        
            // Save the content to the file
            $fullPath = $folderName . DIRECTORY_SEPARATOR . $fileName;
            if (file_put_contents($fullPath, $htmlContent) !== false) {
                echo "File saved successfully: $fullPath\n";
            }
            // $featured_hook = '//div[contains(@class, "col-sm-8") and contains(@class, "details")]';
    
            // $featured_jobs = ReadGrabHtml($featured_hook, $htmlContent);
            // foreach($featured_jobs as $jobs){
            //     $anchor = $jobs->getElementsByTagName('a');
            //     $href =  $anchor->getAttribute('href') ?: null;
            //     echo $href;
            // }
            // echo "$featured_jobs\n";
        }
    
    
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    $random_sleep = rand(10, 60);
    sleep($random_sleep);

    echo "Script resumed after $random_sleep seconds\n";    
}




function call_curl($url){
    // Initialize cURL
    $ch = curl_init();
        
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (if required)
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'); // Spoof user agent

    // Execute cURL request
    $htmlContent = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    // Close cURL session
    curl_close($ch);


    // Save the HTML content to a file
    if (isset($htmlContent)) {
        return [
            'status' => true,
            'content' => $htmlContent
        ];
    } else {
        return [
            'status' => true,
            'error' => new Exception('Failed to read html')
        ];

    }
}

function ReadGrabHtml($hook, $htmlContent){
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
    $dom->loadHTML($htmlContent);
    libxml_clear_errors();

    // Find the target div
    $xpath = new DOMXPath($dom);
    $divs = $xpath->query($hook);

    return ($divs->length > 0) ? $divs->item(0)->textContent : null;
}