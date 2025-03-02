<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$uploadDir = __DIR__ . '/assets/images/news/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

echo "Upload directory: " . $uploadDir . "<br>";
echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_FILES);

    if (isset($_FILES['test_image'])) {
        $file = $_FILES['test_image'];
        $path = $uploadDir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $path)) {
            echo "File uploaded successfully to: " . $path;
        } else {
            echo "Upload failed!";
            echo "Error: " . error_get_last()['message'];
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_image">
    <button type="submit">Test Upload</button>
</form>

