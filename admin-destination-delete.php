<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';
require_once __DIR__ . '/config/upload.php';

requireAdmin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        'Location: admin-destinations.php'
    );

    exit;

}


if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

    header(
        'Location: admin-destinations.php'
    );

    exit;

}


$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id) {

    header(
        'Location: admin-destinations.php'
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| GET IMAGE BEFORE DELETE
|--------------------------------------------------------------------------
*/

$imageStatement =
    $pdo->prepare("
        SELECT image
        FROM destinations
        WHERE id = :id
        LIMIT 1
    ");


$imageStatement->execute([
    ':id' => $id
]);


$destination =
    $imageStatement->fetch();


/*
|--------------------------------------------------------------------------
| DELETE DATABASE ROW
|--------------------------------------------------------------------------
*/

$statement =
    $pdo->prepare("
        DELETE FROM destinations
        WHERE id = :id
    ");


$statement->execute([
    ':id' => $id
]);


/*
|--------------------------------------------------------------------------
| DELETE IMAGE FILE
|--------------------------------------------------------------------------
*/

if ($destination) {

    deleteDestinationImage(
        $destination['image']
        ?? null
    );

}


header(
    'Location: admin-destinations.php?deleted=1'
);

exit;