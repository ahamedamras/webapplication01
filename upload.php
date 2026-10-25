<?php

/*
|--------------------------------------------------------------------------
| DESTINATION IMAGE UPLOAD
|--------------------------------------------------------------------------
*/

function destinationUploadDirectory(): string
{
    return __DIR__ . '/../assets/uploads/destinations';
}


function ensureDestinationUploadDirectory(): void
{
    $directory = destinationUploadDirectory();

    if (!is_dir($directory)) {

        if (!mkdir($directory, 0775, true)) {
            throw new RuntimeException(
                'Unable to create destination image folder.'
            );
        }

    }

    if (!is_writable($directory)) {
        throw new RuntimeException(
            'Destination image folder is not writable.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| UPLOAD IMAGE
|--------------------------------------------------------------------------
*/

function uploadDestinationImage(array $file): ?string
{
    if (
        !isset($file['error']) ||
        is_array($file['error'])
    ) {
        throw new RuntimeException(
            'Invalid image upload request.'
        );
    }


    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }


    if ($file['error'] !== UPLOAD_ERR_OK) {

        $messages = [

            UPLOAD_ERR_INI_SIZE =>
                'Image is larger than the PHP upload limit.',

            UPLOAD_ERR_FORM_SIZE =>
                'Image is too large.',

            UPLOAD_ERR_PARTIAL =>
                'Image upload was incomplete.',

            UPLOAD_ERR_NO_TMP_DIR =>
                'Temporary upload folder is missing.',

            UPLOAD_ERR_CANT_WRITE =>
                'Unable to save uploaded image.',

            UPLOAD_ERR_EXTENSION =>
                'Image upload was stopped by PHP.'

        ];

        throw new RuntimeException(
            $messages[$file['error']]
            ?? 'Image upload failed.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MAXIMUM 5 MB
    |--------------------------------------------------------------------------
    */

    $maxSize = 5 * 1024 * 1024;

    if ($file['size'] > $maxSize) {

        throw new RuntimeException(
            'Image must be 5 MB or smaller.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CHECK REAL IMAGE TYPE
    |--------------------------------------------------------------------------
    */

    $imageInfo = @getimagesize(
        $file['tmp_name']
    );


    if ($imageInfo === false) {

        throw new RuntimeException(
            'Uploaded file is not a valid image.'
        );

    }


    $mimeType =
        $imageInfo['mime'] ?? '';


    $allowedTypes = [

        'image/jpeg' => 'jpg',

        'image/png' => 'png',

        'image/webp' => 'webp'

    ];


    if (!isset($allowedTypes[$mimeType])) {

        throw new RuntimeException(
            'Only JPG, JPEG, PNG and WebP images are allowed.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UNIQUE FILE NAME
    |--------------------------------------------------------------------------
    */

    ensureDestinationUploadDirectory();


    $extension =
        $allowedTypes[$mimeType];


    $fileName =
        bin2hex(random_bytes(16))
        . '.'
        . $extension;


    $targetPath =
        destinationUploadDirectory()
        . DIRECTORY_SEPARATOR
        . $fileName;


    if (
        !move_uploaded_file(
            $file['tmp_name'],
            $targetPath
        )
    ) {

        throw new RuntimeException(
            'Unable to save destination image.'
        );

    }


    return
        'assets/uploads/destinations/'
        . $fileName;
}


/*
|--------------------------------------------------------------------------
| SAFE IMAGE URL
|--------------------------------------------------------------------------
*/

function destinationImageUrl(
    ?string $relativePath
): ?string {

    if (!$relativePath) {
        return null;
    }


    if (
        !preg_match(
            '#^assets/uploads/destinations/[A-Za-z0-9._-]+$#',
            $relativePath
        )
    ) {

        return null;

    }


    $fullPath =
        __DIR__
        . '/../'
        . $relativePath;


    if (!is_file($fullPath)) {
        return null;
    }


    return $relativePath;
}


/*
|--------------------------------------------------------------------------
| DELETE IMAGE
|--------------------------------------------------------------------------
*/

function deleteDestinationImage(
    ?string $relativePath
): void {

    if (!$relativePath) {
        return;
    }


    if (
        !preg_match(
            '#^assets/uploads/destinations/[A-Za-z0-9._-]+$#',
            $relativePath
        )
    ) {

        return;

    }


    $fullPath =
        __DIR__
        . '/../'
        . $relativePath;


    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}