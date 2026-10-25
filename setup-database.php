<?php

require_once __DIR__ . '/config/database.php';

$message = "";
$error = "";

try {

    /*
    |--------------------------------------------------------------------------
    | ADMINS TABLE
    |--------------------------------------------------------------------------
    */

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (

            id INTEGER PRIMARY KEY AUTOINCREMENT,

            username TEXT NOT NULL UNIQUE,

            password TEXT NOT NULL,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP

        )
    ");



    /*
    |--------------------------------------------------------------------------
    | CATEGORIES TABLE
    |--------------------------------------------------------------------------
    */

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (

            id INTEGER PRIMARY KEY AUTOINCREMENT,

            name TEXT NOT NULL UNIQUE,

            slug TEXT NOT NULL UNIQUE,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP

        )
    ");



    /*
    |--------------------------------------------------------------------------
    | DESTINATIONS TABLE
    |--------------------------------------------------------------------------
    */

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS destinations (

            id INTEGER PRIMARY KEY AUTOINCREMENT,

            category_id INTEGER,

            name TEXT NOT NULL,

            slug TEXT NOT NULL UNIQUE,

            description TEXT NOT NULL,

            location TEXT,

            address TEXT,

            distance_km REAL,

            opening_hours TEXT,

            admission_fee TEXT,

            contact TEXT,

            image TEXT,

            latitude REAL,

            longitude REAL,

            is_featured INTEGER DEFAULT 0,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (category_id)
                REFERENCES categories(id)
                ON DELETE SET NULL

        )
    ");



    /*
    |--------------------------------------------------------------------------
    | NEARBY FACILITIES TABLE
    |--------------------------------------------------------------------------
    */

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS nearby_facilities (

            id INTEGER PRIMARY KEY AUTOINCREMENT,

            destination_id INTEGER NOT NULL,

            facility_type TEXT NOT NULL,

            name TEXT NOT NULL,

            address TEXT,

            contact TEXT,

            distance_km REAL,

            latitude REAL,

            longitude REAL,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (destination_id)
                REFERENCES destinations(id)
                ON DELETE CASCADE

        )
    ");



    /*
    |--------------------------------------------------------------------------
    | DEFAULT CATEGORIES
    |--------------------------------------------------------------------------
    */

    $categories = [

        [
            'name' => 'Beach',
            'slug' => 'beach'
        ],

        [
            'name' => 'Historical',
            'slug' => 'historical'
        ],

        [
            'name' => 'Religious',
            'slug' => 'religious'
        ],

        [
            'name' => 'Cultural',
            'slug' => 'cultural'
        ],

        [
            'name' => 'Nature',
            'slug' => 'nature'
        ],

        [
            'name' => 'Food',
            'slug' => 'food'
        ],

        [
            'name' => 'Landmark',
            'slug' => 'landmark'
        ],

        [
            'name' => 'Community',
            'slug' => 'community'
        ]

    ];


    $insertCategory = $pdo->prepare("
        INSERT OR IGNORE INTO categories
        (name, slug)
        VALUES
        (:name, :slug)
    ");


    foreach ($categories as $category) {

        $insertCategory->execute([

            ':name' => $category['name'],

            ':slug' => $category['slug']

        ]);

    }


    $message =
        "Database and tables created successfully!";


} catch (PDOException $e) {

    $error = $e->getMessage();

}



/*
|--------------------------------------------------------------------------
| GET TABLE INFORMATION
|--------------------------------------------------------------------------
*/

$tables = [];

if (!$error) {

    $statement = $pdo->query("
        SELECT name
        FROM sqlite_master
        WHERE type = 'table'
        AND name NOT LIKE 'sqlite_%'
        ORDER BY name
    ");

    $tables = $statement->fetchAll();

}



$categoryCount = 0;

if (!$error) {

    $categoryCount =
        $pdo->query(
            "SELECT COUNT(*) FROM categories"
        )->fetchColumn();

}

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
        Database Setup | Day With Explore
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body class="bg-light">


<div class="container py-5">

    <div
        class="card border-0 shadow-sm mx-auto"
        style="max-width:700px;"
    >

        <div class="card-body p-5">


            <h2 class="fw-bold mb-3">

                <i
                    class="fa-solid fa-database text-primary me-2"
                ></i>

                Day With Explore Database

            </h2>



            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <h5 class="fw-bold">

                        <i
                            class="fa-solid fa-circle-exclamation me-2"
                        ></i>

                        Database Error

                    </h5>

                    <p class="mb-0">

                        <?= htmlspecialchars($error) ?>

                    </p>

                </div>


            <?php else: ?>


                <div class="alert alert-success">

                    <i
                        class="fa-solid fa-circle-check me-2"
                    ></i>

                    <?= htmlspecialchars($message) ?>

                </div>


                <h5 class="fw-bold mt-4">
                    Created Tables
                </h5>


                <div class="list-group mb-4">

                    <?php foreach ($tables as $table): ?>

                        <div
                            class="list-group-item d-flex align-items-center"
                        >

                            <i
                                class="fa-solid fa-table text-primary me-3"
                            ></i>

                            <?= htmlspecialchars($table['name']) ?>

                        </div>

                    <?php endforeach; ?>

                </div>


                <div class="alert alert-info mb-0">

                    <strong>
                        Categories:
                    </strong>

                    <?= (int) $categoryCount ?>

                    categories added successfully.

                </div>


            <?php endif; ?>


        </div>

    </div>

</div>


</body>

</html>