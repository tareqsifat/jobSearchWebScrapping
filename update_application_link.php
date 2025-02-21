<?php

// Load JSON data (assuming it's coming from an API or file)
$jobData = json_decode(file_get_contents('Categorized-jobs.json'), true);

// Directory containing JSON files
$directory = 'category_job_data'; // Change this to your actual JSON file directory

// Scan the directory for JSON files
$jsonFiles = glob($directory . '/*.json');

foreach ($jsonFiles as $filePath) {
    $fileName = basename($filePath, ".json"); // Extract filename without extension
    
    // Try to match filename with job title
    foreach ($jobData as $index => $job) {
        $sluggedTitle = str_replace(' ', '-', $job['title']);
        $sluggedTitle = preg_replace('/[^A-Za-z0-9\-]/', '', $sluggedTitle);
        $fileName = preg_replace('/[^A-Za-z0-9\-]/', '', $fileName);
        $sluggedTitleLength = strlen($sluggedTitle);
        $fileNamePart = substr($fileName, 0, $sluggedTitleLength);
        
        if (stripos($sluggedTitle, $fileNamePart) !== false) {
            // Load existing JSON file
            $fileContent = json_decode(file_get_contents($filePath), true);
            
            // Update the application_link field
            $fileContent['application_link'] = $job['href'];
            
            // Save updated JSON back to the file
            file_put_contents($filePath, json_encode($fileContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            echo "Updated: $filePath" . PHP_EOL;
            break; // Move to the next file
        }
        // if (stripos($sluggedTitle,$fileName) !== false) {
        //     // Load existing JSON file
        //     $fileContent = json_decode(file_get_contents($filePath), true);
            
        //     // Update the application_link field
        //     $fileContent['application_link'] = $job['href'];
            
        //     // Save updated JSON back to the file
        //     file_put_contents($filePath, json_encode($fileContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
        //     echo "Updated: $filePath" . PHP_EOL;
        //     break; // Move to the next file
        // }
    }
}

echo "Process completed!";
?>
