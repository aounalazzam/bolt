<?php

namespace Bolt\Utils;

use Bolt\Utils\ServerErrorException;

class FileUpload
{
    private static string $uploadDir = './routes/uploads/';
    private static array $allowedExtensions = [];
    private static int $maxFileSize = 10485760;

    public static function setUploadDir(string $uploadDirPath)
    {
        self::$uploadDir = $uploadDirPath;
    }

    public static function setMaxFileSize(int $maxFileSize)
    {
        self::$maxFileSize = $maxFileSize;
    }

    public static function upload(array $file, array $allowedExtensions = [], int $maxFileSize = 10485760): string
    {
        if (!is_array($file)) {
            throw ServerErrorException::BadRequest("Expected file data to be an array.");
        }

        self::$allowedExtensions = $allowedExtensions;
        self::$maxFileSize = $maxFileSize;

        if (!is_dir(self::$uploadDir)) {
            mkdir(self::$uploadDir, 0777, true);
        }

        self::validateFile($file);

        $newFileName =  self::generateFileName($file['name']);

        if (!move_uploaded_file($file['tmp_name'], self::$uploadDir . '/' . $newFileName)) {
            throw ServerErrorException::InternalServerError("Failed to move uploaded file.");
        }

        return str_replace('.', '', self::$uploadDir) . '/' . $newFileName;
    }

    private static function validateFile(array $file): void
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw ServerErrorException::BadRequest("Invalid file upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw ServerErrorException::PayloadTooLarge("File is too large.");
            case UPLOAD_ERR_NO_FILE:
                throw ServerErrorException::BadRequest("No file was uploaded.");
            default:
                throw ServerErrorException::InternalServerError("Unknown file upload error.");
        }

        if ($file['size'] > self::$maxFileSize) {
            throw ServerErrorException::PayloadTooLarge("Exceeded file size limit.");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!empty(self::$allowedExtensions) && !in_array(strtolower($ext), self::$allowedExtensions)) {
            throw ServerErrorException::BadRequest("Invalid file extension.");
        }
    }

    private static function generateFileName(string $originalName): string
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('', true) . '.' . $ext;
    }
}
