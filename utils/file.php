<?php
include_once "./utils/exception.php";

/**
 * @throws ClientException
 * @throws InternalServerErrorException
 * @throws Exception
 */
function uploadFile($file): string
{
    if ($file === null) {
        throw new ClientException("File not set");
    }

    $targetDirectory = "./uploads/";

    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Generate a unique filename
    $timestamp = time();
    $randomString = bin2hex(random_bytes(8));
    $uniqueFilename = "$timestamp.$randomString.$ext";

    $targetFile = "/uploads/$uniqueFilename";

    if (file_exists($targetFile)) {
        throw new ClientException("file/not-exists");
    }

    if ($file['size'] > 5000000) {
        throw new ClientException("file/too-large");
    }

    try {
        move_uploaded_file($file['tmp_name'], getcwd() . $targetFile);
        return $targetFile;
    } catch (Throwable $ex) {
        throw new InternalServerErrorException("file/not-uploaded");
    }
}

function createFileRecursively($fullPath, $content = '')
{
    $directory = dirname($fullPath);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $file = fopen($fullPath, 'w');

    if ($file) {
        fwrite($file, $content);
        fclose($file);
    }
}

function uploadImage($file): string
{
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    $isFileImage = getimagesize($file['tmp_name']);

    if (!$isFileImage) {
        throw new ClientException("file/not-image");
    }

    // Allow only specific file formats (you can customize this)
    if ($imageFileType !== 'jpg' && $imageFileType !== 'png' && $imageFileType !== 'jpeg' && $imageFileType !== 'gif') {
        throw new ClientException("file/not-allowed");
    }

    return uploadFile($file);
}
