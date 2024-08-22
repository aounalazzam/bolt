<?php

namespace Bolt\Utils;

use Bolt\Utils\ServerErrorException;

class FileUpload
{
    private static string $uploadDir = __DIR__ . '/uploads';
    private static array $allowedExtensions = [];
    private static int $maxFileSize = 10485760;

    private static array $mimeTypes;

    public static function getMimeTypes()
    {
        if (count(self::$mimeTypes) > 0) {
            self::$mimeTypes = [
                // Images
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'webp' => 'image/webp',
                'tiff' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'heic' => 'image/heic',

                // Audio
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
                'flac' => 'audio/flac',
                'aac' => 'audio/aac',
                'midi' => 'audio/midi',
                'weba' => 'audio/webm',

                // Video
                'mp4' => 'video/mp4',
                'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo',
                'wmv' => 'video/x-ms-wmv',
                'flv' => 'video/x-flv',
                'mkv' => 'video/x-matroska',
                'webm' => 'video/webm',
                'ogv' => 'video/ogg',
                '3gp' => 'video/3gpp',

                // Text
                'txt' => 'text/plain',
                'html' => 'text/html',
                'css' => 'text/css',
                'csv' => 'text/csv',
                'ics' => 'text/calendar',
                'xml' => 'application/xml',
                'json' => 'application/json',

                // Application
                'pdf' => 'application/pdf',
                'zip' => 'application/zip',
                'tar' => 'application/x-tar',
                'gz' => 'application/gzip',
                'rar' => 'application/x-rar-compressed',
                '7z' => 'application/x-7z-compressed',
                'jar' => 'application/java-archive',
                'exe' => 'application/x-msdownload',
                'swf' => 'application/x-shockwave-flash',
                'rtf' => 'application/rtf',

                // Microsoft Office
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                // Open Document Formats
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                'odp' => 'application/vnd.oasis.opendocument.presentation',

                // Fonts
                'ttf' => 'font/ttf',
                'otf' => 'font/otf',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',

                // Archives
                'iso' => 'application/x-iso9660-image',
                'dmg' => 'application/x-apple-diskimage',

                // Misc
                'epub' => 'application/epub+zip',
                'apk' => 'application/vnd.android.package-archive',
                'crx' => 'application/x-chrome-extension',
                'deb' => 'application/vnd.debian.binary-package',
                'rpm' => 'application/x-rpm',

                // Scripts
                'js' => 'application/javascript',
                'php' => 'application/x-httpd-php',
                'sh' => 'application/x-sh',
                'py' => 'application/x-python',
            ];
        }

        return self::$mimeTypes;
    }

    public static function setUploadDir(string $uploadDirPath)
    {
        self::$uploadDir = $uploadDirPath;
    }

    public static function setMaxFileSize(int $maxFileSize)
    {
        self::$maxFileSize = $maxFileSize;
    }

    public static function upload(array $file, array $allowedExtensions = [], int $maxFileSize = 10485760): array
    {
        self::$allowedExtensions = $allowedExtensions;
        self::$maxFileSize = $maxFileSize;

        if (!is_dir(self::$uploadDir)) {
            mkdir(self::$uploadDir, 0777, true);
        }

        self::validateFile($file);

        $newFileName =  self::generateFileName($file['name']);

        if (!move_uploaded_file($file['tmp_name'], self::$uploadDir . $newFileName)) {
            throw ServerErrorException::InternalServerError("Failed to move uploaded file.");
        }

        return [
            'original_name' => $file['name'],
            'new_name' => $newFileName,
            'path' => self::$uploadDir . $newFileName,
            'size' => $file['size'],
            'mime_type' => mime_content_type(self::$uploadDir . $newFileName)
        ];
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

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::getAllowedMimeTypes(self::$allowedExtensions))) {
            throw ServerErrorException::BadRequest("Invalid file MIME type.");
        }
    }

    private static function generateFileName(string $originalName): string
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('', true) . '.' . $ext;
    }

    private static function getAllowedMimeTypes(array $extensions): array
    {
        return array_intersect_key(self::getMimeTypes(), array_flip($extensions));
    }
}
