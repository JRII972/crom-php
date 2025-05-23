<?php

declare(strict_types=1);

namespace App\Utils;

use InvalidArgumentException;
use RuntimeException;
use GdImage;
use JsonSerializable;

// Global variables
const DEFAULT_STORAGE_PATH = __DIR__ . '/../../../public';
const RELATIVE_IMAGE_PATH = '/data/images';
const DEFAULT_IMAGE_STORAGE_PATH = DEFAULT_STORAGE_PATH . RELATIVE_IMAGE_PATH;
const UPLOAD_SUBFOLDER = '/uploads';

/**
 * Class to manage image operations including loading, saving, copying, renaming, cropping, and resizing.
 */
class Image implements JsonSerializable
{
    private string $filePath;
    private ?string $name;
    private ?string $imageAlt;
    private ?GdImage $imageResource = null;
    private string $format;
    private array $supportedFormats = ['jpeg', 'png', 'gif', 'bmp', 'wbmp', 'gd2', 'webp', 'avif'];

    /**
     * Constructor for the Image class.
     *
     * @param string|array $source Image source (local file path, URL, data URL, or $_FILES array)
     * @param string|null $name Optional name for the image; defaults to source filename if not provided
     * @param string|null $subdirectory Optional subdirectory within storage path
     * @param string|null $imageAlt Optional alt text for the image; defaults to name if not provided
     * @param bool $isUpload Indicates if the source is an upload (default: true)
     * @throws InvalidArgumentException If the source is invalid or unsupported
     * @throws RuntimeException If image processing or file operations fail
     */
    public function __construct(
        string|array $source,
        ?string $name = null,
        ?string $subdirectory = null,
        ?string $imageAlt = null,
        bool $isUpload = true
    ) {
        $isLocalFile = false;
        
        if (!realpath(DEFAULT_IMAGE_STORAGE_PATH)){
            if (!mkdir(DEFAULT_IMAGE_STORAGE_PATH, 0755, true)){
                throw new RuntimeException('Dossier Images inaccessible');
            }
        }

        // Set storage path
        $storagePath = realpath(rtrim(DEFAULT_IMAGE_STORAGE_PATH, '/'));

        // TODO: Add support of URL 
        if (is_string($source)) {
            if (!file_exists($storagePath . $source) || !is_readable($storagePath.$source)){
                //TODO: make a new type of execption
                throw new RuntimeException('Fichier non trouver ' . $source);
            }
            $sourcePath = $storagePath . $source;
            $isUpload = false; //Prevent double '/upload'
            $isLocalFile = true;
            
        } else {

            // Handle different source types
            $sourcePath = null;
            $tempFile = null;

            if (is_array($source) && isset($source['tmp_name'], $source['name'], $source['error'])) {
                // Handle form upload ($_FILES)
                if ($source['error'] !== UPLOAD_ERR_OK) {
                    throw new InvalidArgumentException('Upload error: ' . $this->getUploadErrorMessage($source['error']));
                }
                if (!is_uploaded_file($source['tmp_name'])) {
                    throw new InvalidArgumentException('Invalid uploaded file');
                }
                $sourcePath = $source['tmp_name'];
            } elseif (filter_var($source, FILTER_VALIDATE_URL)) {
                // Handle URL
                $imageData = @file_get_contents($source);
                if ($imageData === false) {
                    throw new InvalidArgumentException('Failed to fetch image from URL: ' . $source);
                }
                $tempFile = tempnam(sys_get_temp_dir(), 'img');
                file_put_contents($tempFile, $imageData);
                $sourcePath = $tempFile;
            } elseif (strpos($source, 'data:image/') === 0) {
                // Handle data URL (blob)
                $data = explode(',', $source);
                if (count($data) !== 2 || !str_contains($data[0], 'base64')) {
                    throw new InvalidArgumentException('Invalid data URL format');
                }
                $imageData = base64_decode($data[1]);
                if ($imageData === false) {
                    throw new InvalidArgumentException('Failed to decode data URL');
                }
                $tempFile = tempnam(sys_get_temp_dir(), 'img');
                file_put_contents($tempFile, $imageData);
                $sourcePath = $tempFile;
            } 
        }


        // Get image info
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            if (isset($tempFile)) {
                unlink($tempFile);
            }
            throw new InvalidArgumentException('Invalid or unsupported image format');
        }

        // Extract extension and validate
        $this->format = image_type_to_extension($imageInfo[2], false);
        if (!in_array(strtolower($this->format), $this->supportedFormats)) {
            if (isset($tempFile)) {
                unlink($tempFile);
            }
            throw new InvalidArgumentException('Unsupported image format: ' . $this->format);
        }

        // Set name
        $this->name = $name ? $name . image_type_to_extension($imageInfo[2]) : (is_array($source) ? $source['name'] : basename($source));
        if (empty($this->name)) {
            $this->name = 'image_' . uniqid() . '.' . $this->format;
        }

        // Set alt text
        $this->imageAlt = $imageAlt ?? $name;

        if ($isLocalFile){
            $this->filePath = $source;
        } else {

            // Set relative file path
            $relativePath = ($isUpload && $subdirectory === null) ? UPLOAD_SUBFOLDER : '';
            if ($subdirectory) {
                $relativePath .= '/' . trim($subdirectory, '/') . '/';
            }
            
            // Ensure storage path exists and is writable
            if (!is_dir($storagePath . $relativePath) && !mkdir($storagePath . $relativePath, 0755, true)) {
                throw new RuntimeException('Cannot create storage directory: ' . $storagePath . $relativePath);
            }
            if (!is_writable($storagePath . $relativePath)) {
                throw new RuntimeException('Storage directory is not writable: ' . $storagePath . $relativePath);
            }
            
            // Ensure unique filename
            $this->filePath = $relativePath . $this->name;
            $counter = 1;
            $baseName = pathinfo($this->name, PATHINFO_FILENAME);
            if ( file_exists($storagePath . $this->filePath) ){ //TODO: Retirer le if il ne sert a rien
                while (file_exists($storagePath . $this->filePath)) {
                    $this->filePath = $relativePath . $baseName . '_' . $counter . '.' . $this->format;
                    $counter++;
                }
            }


            // Store the image if not already local
            if (isset($tempFile)) {
                if (!copy($sourcePath, $storagePath . $this->filePath)) {
                    unlink($tempFile);
                    throw new RuntimeException('Failed to store image at: ' . $storagePath . $this->filePath);
                }
                unlink($tempFile);
            } elseif (is_array($source)) {
                if (!move_uploaded_file($sourcePath, $storagePath . $this->filePath)) {
                    throw new RuntimeException('Failed to move uploaded image to: ' . $storagePath . $this->filePath);
                }
            } elseif ($source !== $storagePath . $this->filePath) {
                if (!copy($source, $storagePath . $this->filePath)) {
                    throw new RuntimeException('Failed to copy image to: ' . $storagePath . $this->filePath);
                }
            }
        }

        // Load image resource
        $this->loadImageResource($this->format);
    }

    /**
     * Constructor for the Image class witch catched error.
     *
     * @param string|array|null $source Image source (local file path, URL, data URL, or $_FILES array)
     * @param string|null $name Optional name for the image; defaults to source filename if not provided
     * @param string|null $subdirectory Optional subdirectory within storage path
     * @param string|null $imageAlt Optional alt text for the image; defaults to name if not provided
     * @param bool $isUpload Indicates if the source is an upload (default: true)
     * @throws InvalidArgumentException If the source is invalid or unsupported
     * @throws RuntimeException If image processing or file operations fail
     */
    public static function load(
        string|array|null $source,
        ?string $name = null,
        ?string $subdirectory = null,
        ?string $imageAlt = null,
        bool $isUpload = true
    ) {
        if (is_null($source)){
            return null;
        }
        try {
            return new self($source, $name, $subdirectory, $imageAlt, $isUpload);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
    /**
     * Loads the image resource from the file.
     *
     * @param string $extension Image extension
     * @throws RuntimeException If image loading fails
     */
    private function loadImageResource(string $extension): void
    {
        $fullPath = DEFAULT_IMAGE_STORAGE_PATH . $this->filePath;
        switch (strtolower($extension)) {
            case 'jpeg':
            case 'jpg':
                $this->imageResource = @imagecreatefromjpeg($fullPath);
                break;
            case 'png':
                $this->imageResource = @imagecreatefrompng($fullPath);
                break;
            case 'gif':
                $this->imageResource = @imagecreatefromgif($fullPath);
                break;
            case 'bmp':
                $this->imageResource = @imagecreatefrombmp($fullPath);
                break;
            case 'wbmp':
                $this->imageResource = @imagecreatefromwbmp($fullPath);
                break;
            case 'gd2':
                $this->imageResource = @imagecreatefromgd2($fullPath);
                break;
            case 'webp':
                $this->imageResource = @imagecreatefromwebp($fullPath);
                break;
            case 'avif':
                if (function_exists('imagecreatefromavif')) {
                    $this->imageResource = @imagecreatefromavif($fullPath);
                } else {
                    throw new RuntimeException('AVIF format is not supported by this PHP installation');
                }
                break;
            default:
                throw new RuntimeException('Unsupported image format: ' . $extension);
        }

        if ($this->imageResource === false) {
            throw new RuntimeException('Failed to load image resource from: ' . $fullPath);
        }
    }

    /**
     * Saves the image resource to the file.
     *
     * @param string $filePath Path to save the image
     * @param string $extension Image extension
     * @throws RuntimeException If saving fails
     */
    private function saveImageResource(string $filePath, string $extension): void
    {
        switch (strtolower($extension)) {
            case 'jpeg':
            case 'jpg':
                $result = imagejpeg($this->imageResource, $filePath, 90);
                break;
            case 'png':
                $result = imagepng($this->imageResource, $filePath, 9);
                break;
            case 'gif':
                $result = imagegif($this->imageResource, $filePath);
                break;
            case 'bmp':
                $result = imagebmp($this->imageResource, $filePath);
                break;
            case 'wbmp':
                $result = imagewbmp($this->imageResource, $filePath);
                break;
            case 'gd2':
                $result = imagegd2($this->imageResource, $filePath);
                break;
            case 'webp':
                $result = imagewebp($this->imageResource, $filePath, 90);
                break;
            case 'avif':
                if (function_exists('imageavif')) {
                    $result = imageavif($this->imageResource, $filePath, 90);
                } else {
                    throw new RuntimeException('AVIF format is not supported by this PHP installation');
                }
                break;
            default:
                throw new RuntimeException('Unsupported image format: ' . $extension);
        }

        if (!$result) {
            throw new RuntimeException('Failed to save image to: ' . $filePath);
        }
    }

    /**
     * Deletes the image file.
     *
     * @throws RuntimeException If deletion fails
     */
    public function delete(): void
    {
        $fullPath = DEFAULT_IMAGE_STORAGE_PATH . $this->filePath;
        if (file_exists($fullPath) && !unlink($fullPath)) {
            throw new RuntimeException('Failed to delete image file: ' . $fullPath);
        }
        if ($this->imageResource !== null) {
            imagedestroy($this->imageResource);
            $this->imageResource = null;
        }
    }

    /**
     * Creates a copy of the image with a new name.
     *
     * @param string $newName Name for the copied image
     * @param string|null $newAlt Optional new alt text; defaults to current alt text
     * @return Image New Image instance
     * @throws InvalidArgumentException If the new name is invalid
     * @throws RuntimeException If copying fails
     */
    public function copy(string $newName, ?string $newAlt = null): Image
    {
        if (empty($newName)) {
            throw new InvalidArgumentException('New name cannot be empty');
        }

        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        $newRelativePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . $newName;
        if (!str_ends_with(strtolower($newName), '.' . $extension)) {
            $newRelativePath .= '.' . $extension;
        }

        // Ensure unique filename
        $counter = 1;
        $baseName = pathinfo($newName, PATHINFO_FILENAME);
        $newFilePath = $newRelativePath;
        while (file_exists(DEFAULT_IMAGE_STORAGE_PATH . $newFilePath)) {
            $newFilePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        }

        if (!copy(DEFAULT_IMAGE_STORAGE_PATH . $this->filePath, DEFAULT_IMAGE_STORAGE_PATH . $newFilePath)) {
            throw new RuntimeException('Failed to create image copy at: ' . DEFAULT_IMAGE_STORAGE_PATH . $newFilePath);
        }

        return new Image(DEFAULT_IMAGE_STORAGE_PATH . $newFilePath, pathinfo($newFilePath, PATHINFO_BASENAME), null, $newAlt ?? $this->imageAlt, false);
    }

    /**
     * Renames the image file.
     *
     * @param string $newName New name for the image
     * @param string|null $newAlt Optional new alt text; defaults to new name
     * @throws InvalidArgumentException If the new name is invalid
     * @throws RuntimeException If renaming fails
     */
    public function rename(string $newName, ?string $newAlt = null): void
    {
        if (empty($newName)) {
            throw new InvalidArgumentException('New name cannot be empty');
        }

        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        $newRelativePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . $newName;
        if (!str_ends_with(strtolower($newName), '.' . $extension)) {
            $newRelativePath .= '.' . $extension;
        }

        // Ensure unique filename
        $counter = 1;
        $baseName = pathinfo($newName, PATHINFO_FILENAME);
        $newFilePath = $newRelativePath;
        while (file_exists(DEFAULT_IMAGE_STORAGE_PATH . $newFilePath)) {
            $newFilePath = pathinfo($this->filePath, PATHINFO_DIRNAME) . '/' . $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        }

        if (!rename(DEFAULT_IMAGE_STORAGE_PATH . $this->filePath, DEFAULT_IMAGE_STORAGE_PATH . $newFilePath)) {
            throw new RuntimeException('Failed to rename image to: ' . DEFAULT_IMAGE_STORAGE_PATH . $newFilePath);
        }

        $this->filePath = $newFilePath;
        $this->name = pathinfo($newFilePath, PATHINFO_BASENAME);
        $this->imageAlt = $newAlt ?? $this->name;
    }

    /**
     * Crops the image to the specified dimensions.
     *
     * @param int $x X-coordinate of the top-left corner
     * @param int $y Y-coordinate of the top-left corner
     * @param int $width Width of the crop
     * @param int $height Height of the crop
     * @throws InvalidArgumentException If crop parameters are invalid
     * @throws RuntimeException If cropping fails
     */
    public function crop(int $x, int $y, int $width, int $height): void
    {
        if ($x < 0 || $y < 0 || $width <= 0 || $height <= 0) {
            throw new InvalidArgumentException('Crop parameters must be positive');
        }

        $imageWidth = imagesx($this->imageResource);
        $imageHeight = imagesy($this->imageResource);

        if ($x + $width > $imageWidth || $y + $height > $imageHeight) {
            throw new InvalidArgumentException('Crop dimensions exceed image boundaries');
        }

        $newImage = imagecreatetruecolor($width, $height);
        if ($newImage === false) {
            throw new RuntimeException('Failed to create new image resource for cropping');
        }

        // Preserve transparency for PNG and GIF
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        if (strtolower($extension) === 'png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparent);
        } elseif (strtolower($extension) === 'gif') {
            $transparentIndex = imagecolortransparent($this->imageResource);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($this->imageResource, $transparentIndex);
                $transparentIndex = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($newImage, 0, 0, $transparentIndex);
                imagecolortransparent($newImage, $transparentIndex);
            }
        }

        if (!imagecopy($newImage, $this->imageResource, 0, 0, $x, $y, $width, $height)) {
            imagedestroy($newImage);
            throw new RuntimeException('Failed to crop image');
        }

        imagedestroy($this->imageResource);
        $this->imageResource = $newImage;

        // Save the cropped image
        $this->saveImageResource(DEFAULT_IMAGE_STORAGE_PATH . $this->filePath, $extension);
    }

    /**
     * Resizes the image to the specified dimensions.
     *
     * @param int $width New width
     * @param int $height New height
     * @param bool $maintainAspectRatio Maintain aspect ratio (default: true)
     * @throws InvalidArgumentException If dimensions are invalid
     * @throws RuntimeException If resizing fails
     */
    public function resize(int $width, int $height, bool $maintainAspectRatio = true): void
    {
        if ($width <= 0 || $height <= 0) {
            throw new InvalidArgumentException('Width and height must be positive');
        }

        $imageWidth = imagesx($this->imageResource);
        $imageHeight = imagesy($this->imageResource);

        if ($maintainAspectRatio) {
            $ratio = min($width / $imageWidth, $height / $imageHeight);
            $width = (int) ($imageWidth * $ratio);
            $height = (int) ($imageHeight * $ratio);
        }

        $newImage = imagecreatetruecolor($width, $height);
        if ($newImage === false) {
            throw new RuntimeException('Failed to create new image resource for resizing');
        }

        // Preserve transparency for PNG and GIF
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        if (strtolower($extension) === 'png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparent);
        } elseif (strtolower($extension) === 'gif') {
            $transparentIndex = imagecolortransparent($this->imageResource);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($this->imageResource, $transparentIndex);
                $transparentIndex = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($newImage, 0, 0, $transparentIndex);
                imagecolortransparent($newImage, $transparentIndex);
            }
        }

        if (!imagecopyresampled($newImage, $this->imageResource, 0, 0, 0, 0, $width, $height, $imageWidth, $imageHeight)) {
            imagedestroy($newImage);
            throw new RuntimeException('Failed to resize image');
        }

        imagedestroy($this->imageResource);
        $this->imageResource = $newImage;

        // Save the resized image
        $this->saveImageResource(DEFAULT_IMAGE_STORAGE_PATH . $this->filePath, $extension);
    }

    /**
     * Destructor to clean up image resources.
     */
    public function __destruct()
    {
        if ($this->imageResource !== null) {
            imagedestroy($this->imageResource);
            $this->imageResource = null;
        }
    }

    /**
     * Returns the image file path as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->filePath;
    }

    /**
     * Gets the current file path of the image (relative).
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Gets the full file path of the image.
     *
     * @return string
     */
    public function getFullFilePath(): string
    {
        return DEFAULT_IMAGE_STORAGE_PATH . $this->filePath;
    }

    /**
     * Gets the original name of the image.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the alt text of the image.
     *
     * @return string
     */
    public function getImageAlt(): string
    {
        if (is_null($this->imageAlt)){
            $this->imageAlt = $this->name;
        }
        return $this->imageAlt;
    }

    /**
     * Gets the format of the image.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns a human-readable message for upload errors.
     *
     * @param int $errorCode PHP upload error code
     * @return string
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Serializes the object to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'url' => RELATIVE_IMAGE_PATH . $this->getFilePath(),
            'imageAlt' => $this->getImageAlt(),
            'name' => $this->getName(),
            'format' => $this->getFormat(),
        ];
    }
}