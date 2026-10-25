<?php

require_once __DIR__ . '/config/database.php';


try {

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS emergency_contacts
        (
            id INTEGER PRIMARY KEY AUTOINCREMENT,

            title TEXT NOT NULL,

            emergency_type TEXT NOT NULL,

            phone TEXT NOT NULL,

            area TEXT,

            description TEXT,

            display_order INTEGER DEFAULT 0,

            is_active INTEGER DEFAULT 1,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");


    echo "
        <h2>
            Emergency contacts table created successfully ✅
        </h2>

        <p>
            You can now delete or keep this setup file.
        </p>

        <p>
            <a href='admin-dashboard.php'>
                Go to Admin Dashboard
            </a>
        </p>
    ";


} catch (PDOException $e) {

    echo "
        <h2>
            Database Error
        </h2>

        <p>"
        . htmlspecialchars($e->getMessage())
        . "</p>
    ";

}