<?php


/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}


/*
|--------------------------------------------------------------------------
| REQUIRE ADMIN LOGIN
|--------------------------------------------------------------------------
*/

function requireAdmin(): void
{

    if (
        empty($_SESSION['admin_id'])
    ) {

        header(
            'Location: admin-login.php'
        );

        exit;

    }

}


/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

function csrfToken(): string
{

    if (
        empty($_SESSION['csrf_token'])
    ) {

        $_SESSION['csrf_token'] =
            bin2hex(
                random_bytes(32)
            );

    }


    return
        $_SESSION['csrf_token'];

}


/*
|--------------------------------------------------------------------------
| VERIFY CSRF
|--------------------------------------------------------------------------
*/

function verifyCsrf(
    $token
): bool {

    return
        is_string($token)
        &&
        !empty(
            $_SESSION['csrf_token']
        )
        &&
        hash_equals(
            $_SESSION['csrf_token'],
            $token
        );

}


/*
|--------------------------------------------------------------------------
| CREATE SLUG
|--------------------------------------------------------------------------
*/

function slugify(
    string $value
): string {

    $value =
        strtolower(
            trim($value)
        );


    $value =
        preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $value
        );


    $value =
        trim(
            $value,
            '-'
        );


    return $value;

}


/*
|--------------------------------------------------------------------------
| UNIQUE DESTINATION SLUG
|--------------------------------------------------------------------------
*/

function createUniqueSlug(
    PDO $pdo,
    string $name
): string {

    $baseSlug =
        slugify($name);


    if ($baseSlug === '') {

        $baseSlug =
            'destination';

    }


    $slug =
        $baseSlug;


    $counter = 2;


    while (true) {


        $statement =
            $pdo->prepare("
                SELECT COUNT(*)
                FROM destinations
                WHERE slug = :slug
            ");


        $statement->execute([
            ':slug' => $slug
        ]);


        if (
            (int) $statement->fetchColumn()
            === 0
        ) {

            return $slug;

        }


        $slug =
            $baseSlug
            . '-'
            . $counter;


        $counter++;

    }

}


/*
|--------------------------------------------------------------------------
| UNIQUE CATEGORY SLUG
|--------------------------------------------------------------------------
*/

function createUniqueCategorySlug(
    PDO $pdo,
    string $name
): string {

    $baseSlug =
        slugify($name);


    if ($baseSlug === '') {

        $baseSlug =
            'category';

    }


    $slug =
        $baseSlug;


    $counter = 2;


    while (true) {


        $statement =
            $pdo->prepare("
                SELECT COUNT(*)
                FROM categories
                WHERE slug = :slug
            ");


        $statement->execute([
            ':slug' => $slug
        ]);


        if (
            (int) $statement->fetchColumn()
            === 0
        ) {

            return $slug;

        }


        $slug =
            $baseSlug
            . '-'
            . $counter;


        $counter++;

    }

}