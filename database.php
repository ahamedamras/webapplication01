<?php

/**
 * Day With Explore
 * SQLite Database Connection
 */

$databasePath = __DIR__ . '/../database/tourism.db';

try {

    $pdo = new PDO(
        'sqlite:' . $databasePath
    );

    // Show database errors clearly while developing
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Return database rows as associative arrays
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    // Enable SQLite foreign keys
    $pdo->exec(
        'PRAGMA foreign_keys = ON;'
    );

} catch (PDOException $e) {

    die(
        'Database connection failed: ' .
        htmlspecialchars($e->getMessage())
    );

}