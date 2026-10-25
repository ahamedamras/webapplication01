<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        'Location: admin-categories.php'
    );

    exit;

}


if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

    header(
        'Location: admin-categories.php'
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
        'Location: admin-categories.php'
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| CHECK CATEGORY USAGE
|--------------------------------------------------------------------------
*/

$countStatement =
    $pdo->prepare("
        SELECT COUNT(*)

        FROM destinations

        WHERE category_id = :category_id
    ");


$countStatement->execute([
    ':category_id' => $id
]);


$destinationCount =
    (int) $countStatement->fetchColumn();


if ($destinationCount > 0) {

    header(
        'Location: admin-categories.php?inuse=1'
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| DELETE CATEGORY
|--------------------------------------------------------------------------
*/

$deleteStatement =
    $pdo->prepare("
        DELETE FROM categories
        WHERE id = :id
    ");


$deleteStatement->execute([
    ':id' => $id
]);


header(
    'Location: admin-categories.php?deleted=1'
);

exit;