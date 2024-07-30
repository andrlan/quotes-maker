<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quote = $_POST['quote'];
    $watermark = $_POST['watermark'];
    $fontSizeQuote = $_POST['fontSizeQuote'];
    $color = $_POST['color'];
    $background = $_FILES['background']['tmp_name'];
    $backgroundName = $_FILES['background']['name'];

    // Convert hex color to RGB
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");

    // Check if the file is an image
    $check = getimagesize($background);
    if ($check === false) {
        die("File is not an image.");
    }

    // Set the upload directory and move the uploaded file
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($backgroundName);
    if (!move_uploaded_file($background, $targetFile)) {
        die("Sorry, there was an error uploading your file.");
    }

    // Determine the image type and create image accordingly
    $imageType = exif_imagetype($targetFile);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($targetFile);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($targetFile);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($targetFile);
            break;
        default:
            die("Unsupported image type.");
    }

    if ($image === false) {
        die("Failed to create image from file.");
    }

    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    // Target dimensions
    $targetWidth = 1080;
    $targetHeight = 1920;

    // Calculate aspect ratio
    $imageAspectRatio = $imageWidth / $imageHeight;
    $targetAspectRatio = $targetWidth / $targetHeight;

    // Create the cropped image
    if ($imageAspectRatio > $targetAspectRatio) {
        // Image is wider than target aspect ratio, crop the width
        $newHeight = $imageHeight;
        $newWidth = $imageHeight * $targetAspectRatio;
        $src_x = ($imageWidth - $newWidth) / 2;
        $src_y = 0;
    } else {
        // Image is taller than target aspect ratio, crop the height
        $newWidth = $imageWidth;
        $newHeight = $imageWidth / $targetAspectRatio;
        $src_x = 0;
        $src_y = ($imageHeight - $newHeight) / 2;
    }

    $croppedImage = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled($croppedImage, $image, 0, 0, $src_x, $src_y, $targetWidth, $targetHeight, $newWidth, $newHeight);

    // Set text color to the selected color
    $textColor = imagecolorallocate($croppedImage, $r, $g, $b);

    // Set font paths
    $fontPathQuote = __DIR__ . '/Outfit-Medium.ttf'; // Path to Outfit-Medium.ttf
    $fontPathWatermark = __DIR__ . '/Outfit-ExtraLight.ttf'; // Path to Outfit-ExtraLight.ttf

    if (!file_exists($fontPathQuote) || !file_exists($fontPathWatermark)) {
        die("Font files not found.");
    }

    // Set font sizes
    $fontSizeWatermark = 20;

    // Function to wrap text
    function wrapText($fontSize, $angle, $fontPath, $text, $maxWidth) {
        $words = explode(' ', $text);
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $testLine = $line . ' ' . $word;
            $testBox = imagettfbbox($fontSize, $angle, $fontPath, $testLine);
            if ($testBox[2] > $maxWidth) {
                $lines[] = trim($line);
                $line = $word;
            } else {
                $line = $testLine;
            }
        }
        $lines[] = trim($line);
        return implode("\n", $lines);
    }

    // Add wrapped quote to image
    $wrappedQuote = wrapText($fontSizeQuote, 0, $fontPathQuote, $quote, $targetWidth - 100);

    // Calculate text box size
    $textBox = imagettfbbox($fontSizeQuote, 0, $fontPathQuote, $wrappedQuote);
    $textWidth = abs($textBox[4] - $textBox[0]);
    $textHeight = abs($textBox[5] - $textBox[1]);

    // Center the text
    $quoteX = ($targetWidth - $textWidth) / 2;
    $quoteY = ($targetHeight / 2) - ($textHeight / 2);

    // Add quote to image
    imagettftext($croppedImage, $fontSizeQuote, 0, $quoteX, $quoteY, $textColor, $fontPathQuote, $wrappedQuote);

    // Set watermark color to white
    $watermarkColor = imagecolorallocate($croppedImage, 255, 255, 255);

    // Add watermark to image
    $watermarkBox = imagettfbbox($fontSizeWatermark, 0, $fontPathWatermark, $watermark);
    $watermarkWidth = abs($watermarkBox[4] - $watermarkBox[0]);
    $watermarkX = ($targetWidth - $watermarkWidth) / 2;
    $watermarkY = $targetHeight - 50;
    imagettftext($croppedImage, $fontSizeWatermark, 0, $watermarkX, $watermarkY, $watermarkColor, $fontPathWatermark, $watermark);

    // Save the final image
    $outputFile = 'output/' . time() . '.png';
    imagepng($croppedImage, $outputFile);

    // Free memory
    imagedestroy($croppedImage);
    imagedestroy($image);

    // Redirect to index.php with the output image
    header('Location: index.php?output=' . urlencode($outputFile));
    exit;
} else {
    echo "Invalid request.";
}
?>
