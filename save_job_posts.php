<?php 
$url = 'https://eurostaffs.org/api/upload_scrapped_job_post';
$data = [];
$data['api_key'] = '@Poss123!@#';

$files = glob('category_job_data/*.json');
foreach ($files as $file) {
    $jsonContent = file_get_contents($file);
    $job = json_decode($jsonContent, true);
    foreach ($job as $key => $jobPost) {
        $data[$key] = $jobPost;
    }
    call_curl($url, $data);
    $sec = rand(1, 5);
    sleep($sec);
}

function call_curl($url, $data) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);
    // Create log file if it doesn't exist and save server response there
    $logFile = 'server_responses.log';
    if (!file_exists($logFile)) {   
        file_put_contents($logFile, '');
    }
    file_put_contents($logFile, $response . PHP_EOL, FILE_APPEND);

    // Check for errors
    if ($response === false) {
        echo 'Curl error: ';
    } else {
        echo 'Response: ' . $response.'<br>';
    }

    // Close cURL session
    curl_close($ch);
}
