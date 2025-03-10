<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// Google API Credentials
$client = new Client();
$client->setAuthConfig('bd-form-project-b3adb8711a59.json'); // Your Google Service Account JSON
$client->addScope(Drive::DRIVE_FILE);
$service = new Drive($client);

// Google Drive Folder ID (replace with your folder ID)
$folderId = '1qF1gqBcdqJNplWmqbafELrq5_O2uGcqa';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $property_id = $_POST["property_id"];

    // File Upload Handling
    if (!empty($_FILES["file"]["name"])) {
        $fileName = basename($_FILES["file"]["name"]);
        $filePath = $_FILES["file"]["tmp_name"];
        
        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName,
            'parents' => [$folderId]
        ]);
        
        $content = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);
        
        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart'
        ]);
        
        $fileUrl = "https://drive.google.com/file/d/" . $file->id;
    } else {
        $fileUrl = "No file uploaded";
    }

    // Save Form Data (Optional: Store in Google Sheets or Database)
    $response = [
        "status" => "success",
        "message" => "Form submitted successfully!",
        "file_url" => $fileUrl
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
