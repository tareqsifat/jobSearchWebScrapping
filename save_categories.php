<?php
$url = 'https://eurostaffs.org/api/upload_scrapped_category';
$data = [
    'api_key' => '@Poss123!@#'
];

$json = file_get_contents('categories.json');
$categories = json_decode($json, true);

foreach ($categories as $category) {
    $data['title'] = $category['title'];
    call_curl($url, $data); 
    echo 'Category saved: ' . $category['title'] . PHP_EOL;
}
// Initialize cURL session
function call_curl($url, $data) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
    }
    //  else {
    //     echo 'Response: ' . $response;
    // }

    // Close cURL session
    curl_close($ch);
}

// call_curl($url, $data);
?>