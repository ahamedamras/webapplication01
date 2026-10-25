<?php

require_once __DIR__ . '/config/database.php';

session_start();


/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['admin_id'])) {

    header('Location: admin-dashboard.php');
    exit;

}


$error = '';


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($username === '' || $password === '') {

        $error =
            'Please enter username and password.';

    } else {

        $statement = $pdo->prepare("
            SELECT
                id,
                username,
                password

            FROM admins

            WHERE username = :username

            LIMIT 1
        ");


        $statement->execute([
            ':username' => $username
        ]);


        $admin = $statement->fetch();


        /*
        |--------------------------------------------------------------------------
        | VERIFY PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            $admin &&
            password_verify(
                $password,
                $admin['password']
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | NEW SESSION ID
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);


            $_SESSION['admin_id'] =
                $admin['id'];


            $_SESSION['admin_username'] =
                $admin['username'];


            header(
                'Location: admin-dashboard.php'
            );

            exit;

        }


        $error =
            'Invalid username or password.';

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
        Admin Login | Day With Explore
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

                <i class="fa-solid fa-user-shield"></i>

            </div>


            <span class="admin-auth-small-title">

                ADMINISTRATION

            </span>


            <h1>
                Welcome Back
            </h1>


            <p class="admin-auth-subtitle">

                Sign in to manage
                Day With Explore tourism information.

            </p>



            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <i
                        class="fa-solid fa-circle-exclamation me-2"
                    ></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>



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
                            placeholder="Enter username"
                            required
                            value="<?= htmlspecialchars(
                                $_POST['username'] ?? ''
                            ) ?>"
                        >

                    </div>

                </div>



                <div class="mb-4">

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
                            placeholder="Enter password"
                            required
                        >

                    </div>

                </div>



                <button
                    type="submit"
                    class="btn admin-main-btn"
                >

                    <i
                        class="fa-solid fa-right-to-bracket me-2"
                    ></i>

                    Login to Dashboard

                </button>


            </form>



            <div class="admin-login-link">

                First time setup?

                <a href="admin-create.php">

                    Create Admin Account

                </a>

            </div>



            <a
                href="index.php"
                class="admin-back-link"
            >

                <i
                    class="fa-solid fa-arrow-left me-2"
                ></i>

                Back to Website

            </a>


        </div>


    </div>

</section>


</body>
</html>