<?php

require_once __DIR__ . '/config/database.php';


/*
|--------------------------------------------------------------------------
| PROJECT SYSTEM CHECK
|--------------------------------------------------------------------------
*/

$checks = [];


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function addCheck(
    array &$checks,
    string $name,
    bool $status,
    string $message
): void {

    $checks[] = [
        'name' => $name,
        'status' => $status,
        'message' => $message
    ];

}


/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

addCheck(
    $checks,
    'SQLite Database Connection',
    isset($pdo),
    isset($pdo)
        ? 'Database connection is working.'
        : 'Database connection failed.'
);


/*
|--------------------------------------------------------------------------
| REQUIRED TABLES
|--------------------------------------------------------------------------
*/

$requiredTables = [

    'admins',
    'categories',
    'destinations',
    'nearby_facilities',
    'emergency_contacts'

];


foreach ($requiredTables as $table) {

    try {

        $statement = $pdo->prepare("
            SELECT name
            FROM sqlite_master
            WHERE type = 'table'
            AND name = :table_name
        ");


        $statement->execute([
            ':table_name' => $table
        ]);


        $exists =
            (bool) $statement->fetchColumn();


        addCheck(
            $checks,
            'Table: ' . $table,
            $exists,
            $exists
                ? 'Table exists.'
                : 'Table is missing.'
        );


    } catch (PDOException $e) {

        addCheck(
            $checks,
            'Table: ' . $table,
            false,
            'Unable to check table.'
        );

    }

}


/*
|--------------------------------------------------------------------------
| DATABASE COUNTS
|--------------------------------------------------------------------------
*/

$countQueries = [

    'Destinations' =>
        'SELECT COUNT(*) FROM destinations',

    'Categories' =>
        'SELECT COUNT(*) FROM categories',

    'Nearby Facilities' =>
        'SELECT COUNT(*) FROM nearby_facilities',

    'Admins' =>
        'SELECT COUNT(*) FROM admins'

];


foreach (
    $countQueries
    as $label => $query
) {

    try {

        $count =
            (int) $pdo
                ->query($query)
                ->fetchColumn();


        addCheck(
            $checks,
            $label . ' Data',
            $count > 0,
            $count . ' record(s) found.'
        );


    } catch (PDOException $e) {

        addCheck(
            $checks,
            $label . ' Data',
            false,
            'Could not read records.'
        );

    }

}


/*
|--------------------------------------------------------------------------
| EMERGENCY CONTACTS
|--------------------------------------------------------------------------
*/

try {

    $emergencyCount =
        (int) $pdo
            ->query("
                SELECT COUNT(*)
                FROM emergency_contacts
            ")
            ->fetchColumn();


    addCheck(
        $checks,
        'Emergency Contacts Data',
        true,
        $emergencyCount
        . ' emergency contact(s) found.'
    );


} catch (PDOException $e) {

    addCheck(
        $checks,
        'Emergency Contacts Data',
        false,
        'Emergency table is unavailable.'
    );

}


/*
|--------------------------------------------------------------------------
| REQUIRED FILES
|--------------------------------------------------------------------------
*/

$requiredFiles = [

    'index.php',
    'destinations.php',
    'destination-details.php',
    'categories.php',
    'map.php',
    'nearby.php',
    'planner.php',
    'emergency.php',

    'admin-login.php',
    'admin-dashboard.php',
    'admin-destinations.php',
    'admin-categories.php',
    'admin-facilities.php',
    'admin-emergency.php',

    'config/database.php',
    'config/admin.php',
    'config/upload.php',

    'assets/css/style.css',
    'assets/js/script.js'

];


foreach ($requiredFiles as $file) {

    $exists =
        file_exists(
            __DIR__ . '/' . $file
        );


    addCheck(
        $checks,
        'File: ' . $file,
        $exists,
        $exists
            ? 'File exists.'
            : 'Required file is missing.'
    );

}


/*
|--------------------------------------------------------------------------
| UPLOAD DIRECTORY
|--------------------------------------------------------------------------
*/

$uploadDirectory =
    __DIR__
    . '/assets/uploads/destinations';


$uploadExists =
    is_dir($uploadDirectory);


addCheck(
    $checks,
    'Destination Upload Folder',
    $uploadExists,
    $uploadExists
        ? 'Upload directory exists.'
        : 'Upload directory is missing.'
);


if ($uploadExists) {

    addCheck(
        $checks,
        'Upload Folder Writable',
        is_writable($uploadDirectory),
        is_writable($uploadDirectory)
            ? 'Upload folder is writable.'
            : 'Upload folder is not writable.'
    );

}


/*
|--------------------------------------------------------------------------
| DATABASE FILE
|--------------------------------------------------------------------------
*/

$databaseFile =
    __DIR__
    . '/database/tourism.db';


addCheck(
    $checks,
    'SQLite Database File',
    file_exists($databaseFile),
    file_exists($databaseFile)
        ? 'tourism.db exists.'
        : 'tourism.db is missing.'
);


/*
|--------------------------------------------------------------------------
| FINAL STATUS
|--------------------------------------------------------------------------
*/

$totalChecks =
    count($checks);


$passedChecks =
    count(
        array_filter(
            $checks,
            fn ($check) =>
                $check['status']
        )
    );


$failedChecks =
    $totalChecks
    - $passedChecks;


$allPassed =
    $failedChecks === 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        System Check | Day With Explore
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        body {
            background: #f5f9fa;
            color: #153243;
        }

        .check-wrapper {
            max-width: 950px;
            margin: 60px auto;
        }

        .check-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow:
                0 12px 35px
                rgba(30, 70, 80, 0.07);
        }

        .check-header h1 {
            font-weight: 800;
        }

        .check-summary {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .summary-box {
            padding: 12px 18px;
            border-radius: 12px;
            background: #f4f8f9;
            font-weight: 700;
        }

        .check-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            border: 1px solid #e1ebed;
            border-radius: 15px;
            padding: 16px 18px;
            margin-bottom: 10px;
        }

        .check-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
        }

        .check-success {
            background: #eaf8f2;
            color: #198754;
        }

        .check-failed {
            background: #fff0f0;
            color: #c94c4c;
        }

        .check-card strong {
            display: block;
            font-size: .9rem;
        }

        .check-card span {
            display: block;
            margin-top: 3px;
            color: #7b898f;
            font-size: .75rem;
        }

        .final-success {
            border-left: 5px solid #198754;
        }

        .final-warning {
            border-left: 5px solid #dc3545;
        }

    </style>

</head>


<body>


<div class="container">

    <div class="check-wrapper">


        <div
            class="check-header
            <?= $allPassed
                ? 'final-success'
                : 'final-warning' ?>"
        >


            <span class="text-uppercase small fw-bold text-primary">

                Day With Explore

            </span>


            <h1 class="mt-2">

                System Health Check

            </h1>


            <p class="text-muted">

                Final project database,
                file and configuration verification.

            </p>



            <div class="check-summary">


                <div class="summary-box">

                    Total:
                    <?= $totalChecks ?>

                </div>


                <div class="summary-box text-success">

                    Passed:
                    <?= $passedChecks ?>

                </div>


                <div class="summary-box text-danger">

                    Failed:
                    <?= $failedChecks ?>

                </div>


            </div>


        </div>



        <?php foreach ($checks as $check): ?>


            <div class="check-card">


                <div
                    class="check-icon
                    <?= $check['status']
                        ? 'check-success'
                        : 'check-failed' ?>"
                >


                    <?php if ($check['status']): ?>

                        <i
                            class="fa-solid fa-check"
                        ></i>

                    <?php else: ?>

                        <i
                            class="fa-solid fa-xmark"
                        ></i>

                    <?php endif; ?>


                </div>



                <div>

                    <strong>

                        <?= htmlspecialchars(
                            $check['name']
                        ) ?>

                    </strong>


                    <span>

                        <?= htmlspecialchars(
                            $check['message']
                        ) ?>

                    </span>

                </div>


            </div>


        <?php endforeach; ?>



        <div class="mt-4 text-center">


            <a
                href="index.php"
                class="btn btn-primary"
            >

                View Website

            </a>


            <a
                href="admin-dashboard.php"
                class="btn btn-outline-secondary ms-2"
            >

                Admin Dashboard

            </a>


        </div>


    </div>

</div>


</body>

</html>