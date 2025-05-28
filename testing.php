<?php
$imagePath = '/var/www/html/src/App/Utils/../../../public/data/images/Numenera.jpg';
require_once __DIR__ . '/vendor/autoload.php';
// Check if the file exists
if (!file_exists($imagePath)) {
    die("Error: Image file does not exist at $imagePath\n");
}

// Create image resource from JPEG
$image = imagecreatefromjpeg($imagePath);

if ($image === false) {
    die("Error: Failed to create image from $imagePath\n");
} else {
    echo "Success: Image loaded from $imagePath\n";
    // Optionally, you can perform operations on $image here
    // Free the memory
    imagedestroy($image);
}
?>
