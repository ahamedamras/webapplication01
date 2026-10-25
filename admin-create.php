<?php

require_once __DIR__ . '/config/database.php';

session_start();

$message = '';
$error = '';


/*
|--------------------------------------------------------------------------
| CHECK IF ADMIN ALREADY EXISTS
|--------------------------------------------------------------------------
*/

$adminCount = (int) $pdo
    ->query("SELECT COUNT(*) FROM admins")
    ->fetchColumn();


/*
|--------------------------------------------------------------------------
| CREATE ADMIN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';


    if ($username === '') {

        $error = 'Username is required.';

    } elseif (strlen($username) < 4) {

        $error = 'Username must contain at least 4 characters.';

    } elseif ($password === '') {

        $error = 'Password is required.';

    } elseif (strlen($password) < 8) {

        $error = 'Password must contain at least 8 characters.';

    } elseif ($password !== $confirmPassword) {

        $error = 'Passwords do not match.';

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK USERNAME
            |--------------------------------------------------------------------------
            */

            $checkStatement = $pdo->prepare("
                SELECT id
                FROM admins
                WHERE username = :username
                LIMIT 1
            ");

            $checkStatement->execute([
                ':username' => $username
            ]);


            if ($checkStatement->fetch()) {

                $error = 'This username already exists.';

            } else {

                /*
                |--------------------------------------------------------------------------
                | HASH PASSWORD
                |--------------------------------------------------------------------------
                */

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /*
                |--------------------------------------------------------------------------
                | INSERT ADMIN
                |--------------------------------------------------------------------------
                */

                $insertStatement = $pdo->prepare("
                    INSERT INTO admins
                    (
                        username,
                        password
                    )
                    VALUES
                    (
                        :username,
                        :password
                    )
                ");


                $insertStatement->execute([

                    ':username' => $username,

                    ':password' => $hashedPassword

                ]);


                $message =
                    'Administrator account created successfully.';

                $adminCount++;

            }

        } catch (PDOException $e) {

            $error =
                'Unable to create administrator account.';

        }

    }

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
        Create Admin | Day With Explore
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body class="admin-auth-body">


<section class="admin-auth-section">

    <div class="container">

        <div class="admin-auth-card">


            <div class="admin-auth-logo">

                <i class="fa-solid fa-location-dot"></i>

            </div>


            <span class="admin-auth-small-title">

                DAY WITH EXPLORE

            </span>


            <h1>
                Create Administrator
            </h1>


            <p class="admin-auth-subtitle">

                Create an administrator account
                to manage tourism information.

            </p>



            <?php if ($message): ?>

                <div class="alert alert-success">

                    <i class="fa-solid fa-circle-check me-2"></i>

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>



            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>



            <?php if ($message): ?>


                <a
                    href="admin-login.php"
                    class="btn admin-main-btn"
                >

                    Go to Admin Login

                    <i class="fa-solid fa-arrow-right ms-2"></i>

                </a>


            <?php else: ?>


                <form
                    method="POST"
                    autocomplete="off"
                >


                    <div class="mb-3">

                        <label
                            for="username"
                            class="form-label"
                        >
                            Username
                        </label>


                        <div class="admin-input-group">

                            <i class="fa-solid fa-user"></i>


                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                placeholder="Enter admin username"
                                required
                                minlength="4"
                                value="<?= htmlspecialchars(
                                    $_POST['username'] ?? ''
                                ) ?>"
                            >

                        </div>

                    </div>



                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Password
                        </label>


                        <div class="admin-input-group">

                            <i class="fa-solid fa-lock"></i>


                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimum 8 characters"
                                required
                                minlength="8"
                            >

                        </div>

                    </div>



                    <div class="mb-4">

                        <label
                            for="confirm_password"
                            class="form-label"
                        >
                            Confirm Password
                        </label>


                        <div class="admin-input-group">

                            <i class="fa-solid fa-shield-halved"></i>


                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Enter password again"
                                required
                                minlength="8"
                            >

                        </div>

                    </div>



                    <button
                        type="submit"
                        class="btn admin-main-btn"
                    >

                        <i class="fa-solid fa-user-plus me-2"></i>

                        Create Admin Account

                    </button>


                </form>


            <?php endif; ?>



            <?php if ($adminCount > 0 && !$message): ?>

                <div class="admin-login-link">

                    Already have an account?

                    <a href="admin-login.php">
                        Admin Login
                    </a>

                </div>

            <?php endif; ?>


            <a
                href="index.php"
                class="admin-back-link"
            >

                <i class="fa-solid fa-arrow-left me-2"></i>

                Back to Website

            </a>


        </div>

    </div>

</section>


</body>
</html>