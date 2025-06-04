<?php

declare(strict_types=1);

namespace App\Utils;

use InvalidArgumentException;
use RuntimeException;
use GdImage;
use JsonSerializable;

use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromgif;
use function imagecreatefrombmp;
use function imagecreatefromwbmp;
use function imagecreatefromgd2;
use function imagecreatefromwebp;
use function imagecreatefromavif;

// Global variables
const DEFAULT_STORAGE_PATH = __DIR__ . '/../../public';
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
    private string $format;
    private array $supportedFormats = ['jpeg', 'png', 'gif', 'bmp', 'wbmp', 'gd2', 'webp', 'avif'];

    /**
     * Constructor for the Image class.
     *
     * @param string|array|null $source Image source (local file path, URL, data URL, or $_FILES array)
     * @param string|null $name Optional name for the image; defaults to source filename if not provided
     * @param string|null $subdirectory Optional subdirectory within storage path
     * @param string|null $imageAlt Optional alt text for the image; defaults to name if not provided
     * @param bool $isUpload Indicates if the source is an upload (default: true)
     * @throws InvalidArgumentException If the source is invalid or unsupported
     * @throws RuntimeException If image processing or file operations fail
     */
    public function __construct(
        string|array|null $source,
        ?string $name = null,
        ?string $subdirectory = null,
        ?string $imageAlt = null,
        bool $isUpload = true
    ) {
        $isLocalFile = false;

        if ($source == null) {
            return null;
        } 
        
        if (!realpath(DEFAULT_IMAGE_STORAGE_PATH)){
            if (!mkdir(DEFAULT_IMAGE_STORAGE_PATH, 0755, true)){
                throw new RuntimeException('Dossier Images inaccessible');
            }
        }

        // Set storage path
        $storagePath = realpath(rtrim(DEFAULT_IMAGE_STORAGE_PATH, '/'));

        // TODO: Add support of URL 
        if (is_string($source)) {
            if (!file_exists(rtrim(DEFAULT_STORAGE_PATH, '/') . $source) || !is_readable(rtrim(DEFAULT_STORAGE_PATH, '/') . $source)){
                //TODO: make a new type of execption
                throw new RuntimeException('Fichier non trouver ' . $source);
            }
            $sourcePath = rtrim(DEFAULT_STORAGE_PATH, '/') . $source;
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
    ): ?self {
        if (is_null($source)){
            return null;
        }
        try {
            return new self($source, $name, $subdirectory, $imageAlt, $isUpload);
        } catch (\Throwable $th) {
            // Log l'erreur si besoin
            // error_log('Erreur lors du chargement de l\'image: ' . $th->getMessage());
            return null;
        }
    }


    /**
     * Deletes the image file.
     *
     * @throws RuntimeException If deletion fails
     */
    public function delete(): void
    {
        return;
        $fullPath = DEFAULT_STORAGE_PATH . $this->filePath;
        if (file_exists($fullPath) && !unlink($fullPath)) {
            throw new RuntimeException('Failed to delete image file: ' . $fullPath);
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
    public function jsonSerialize(): ?array
    {
        if (!$this->isValid()){
            return null;
        }

        return [
            'url' => $this->getFilePath(),
            'imageAlt' => $this->getImageAlt(),
            'name' => $this->getName(),
            'format' => $this->getFormat(),
        ];
    }

    /**
     * Vérifie si l'image est valide en s'assurant que toutes les propriétés nécessaires sont initialisées
     */
    public function isValid(): bool {
        // Vérifier que le chemin du fichier est défini et non vide
        if (empty($this->filePath)) {
            return false;
        }
        
        // Vérifier que le nom est défini et non vide
        if (empty($this->name)) {
            return false;
        }
        
        // Vérifier que le format est défini et supporté
        if (empty($this->format) || !in_array(strtolower($this->format), $this->supportedFormats)) {
            return false;
        }
        
        // Vérifier que le fichier existe physiquement
        $fullPath = rtrim(DEFAULT_STORAGE_PATH, '/') . $this->filePath;
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            return false;
        }
        
        // Vérifier que c'est bien un fichier image valide
        $imageInfo = @getimagesize($fullPath);
        if ($imageInfo === false) {
            return false;
        }
        
        return true;
    }
}