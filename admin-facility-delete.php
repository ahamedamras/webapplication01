<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header(
        'Location: admin-facilities.php'
    );

    exit;
}


if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

    header(
        'Location: admin-facilities.php'
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
        'Location: admin-facilities.php'
    );

    exit;
}


$statement =
    $pdo->prepare("
        DELETE FROM nearby_facilities
        WHERE id = :id
    ");


$statement->execute([
    ':id' => $id
]);


header(
    'Location: admin-facilities.php?deleted=1'
);

exit;