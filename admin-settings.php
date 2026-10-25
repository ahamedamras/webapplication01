<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


/*
|--------------------------------------------------------------------------
| GET CURRENT ADMIN
|--------------------------------------------------------------------------
*/

$adminId =
    (int) $_SESSION['admin_id'];


$statement =
    $pdo->prepare("
        SELECT
            id,
            username,
            password

        FROM admins

        WHERE id = :id

        LIMIT 1
    ");


$statement->execute([
    ':id' => $adminId
]);


$admin =
    $statement->fetch();


if (!$admin) {

    session_destroy();

    header(
        'Location: admin-login.php'
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| MESSAGES
|--------------------------------------------------------------------------
*/

$profileError = '';

$passwordError = '';

$profileSuccess =
    isset($_GET['profile']);

$passwordSuccess =
    isset($_GET['password']);


/*
|--------------------------------------------------------------------------
| CHANGE USERNAME
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['action_type'] ?? '') === 'username'
) {

    $newUsername =
        trim(
            $_POST['username'] ?? ''
        );


    $currentPassword =
        $_POST['current_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        !verifyCsrf(
            $_POST['csrf_token'] ?? ''
        )
    ) {

        $profileError =
            'Invalid request. Please refresh the page.';


    } elseif (
        strlen($newUsername) < 4
    ) {

        $profileError =
            'Username must contain at least 4 characters.';


    } elseif (
        !preg_match(
            '/^[A-Za-z0-9._-]+$/',
            $newUsername
        )
    ) {

        $profileError =
            'Username can contain only letters, numbers, dot, underscore and hyphen.';


    } elseif (
        !password_verify(
            $currentPassword,
            $admin['password']
        )
    ) {

        $profileError =
            'Current password is incorrect.';


    } else {


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE USERNAME
        |--------------------------------------------------------------------------
        */

        $checkStatement =
            $pdo->prepare("
                SELECT COUNT(*)

                FROM admins

                WHERE LOWER(username) =
                      LOWER(:username)

                AND id != :id
            ");


        $checkStatement->execute([

            ':username' =>
                $newUsername,

            ':id' =>
                $adminId

        ]);


        $usernameExists =
            (int) $checkStatement
                ->fetchColumn();


        if ($usernameExists > 0) {

            $profileError =
                'This username is already being used.';

        } else {


            /*
            |--------------------------------------------------------------------------
            | UPDATE USERNAME
            |--------------------------------------------------------------------------
            */

            $updateStatement =
                $pdo->prepare("
                    UPDATE admins

                    SET username = :username

                    WHERE id = :id
                ");


            $updateStatement->execute([

                ':username' =>
                    $newUsername,

                ':id' =>
                    $adminId

            ]);


            /*
             * Update current session username.
             */

            $_SESSION['admin_username'] =
                $newUsername;


            session_regenerate_id(
                true
            );


            header(
                'Location: admin-settings.php?profile=1'
            );

            exit;

        }

    }

}


/*
|--------------------------------------------------------------------------
| CHANGE PASSWORD
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['action_type'] ?? '') === 'password'
) {

    $currentPassword =
        $_POST['current_password'] ?? '';

    $newPassword =
        $_POST['new_password'] ?? '';

    $confirmPassword =
        $_POST['confirm_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        !verifyCsrf(
            $_POST['csrf_token'] ?? ''
        )
    ) {

        $passwordError =
            'Invalid request. Please refresh the page.';


    } elseif (
        !password_verify(
            $currentPassword,
            $admin['password']
        )
    ) {

        $passwordError =
            'Current password is incorrect.';


    } elseif (
        strlen($newPassword) < 8
    ) {

        $passwordError =
            'New password must contain at least 8 characters.';


    } elseif (
        $newPassword !==
        $confirmPassword
    ) {

        $passwordError =
            'New password and confirm password do not match.';


    } elseif (
        password_verify(
            $newPassword,
            $admin['password']
        )
    ) {

        $passwordError =
            'New password must be different from the current password.';


    } else {


        /*
        |--------------------------------------------------------------------------
        | HASH NEW PASSWORD
        |--------------------------------------------------------------------------
        */

        $passwordHash =
            password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );


        /*
        |--------------------------------------------------------------------------
        | UPDATE PASSWORD
        |--------------------------------------------------------------------------
        */

        $updatePasswordStatement =
            $pdo->prepare("
                UPDATE admins

                SET password = :password

                WHERE id = :id
            ");


        $updatePasswordStatement->execute([

            ':password' =>
                $passwordHash,

            ':id' =>
                $adminId

        ]);


        session_regenerate_id(
            true
        );


        header(
            'Location: admin-settings.php?password=1'
        );

        exit;

    }

}


/*
|--------------------------------------------------------------------------
| REFRESH CURRENT ADMIN DATA
|--------------------------------------------------------------------------
*/

$statement->execute([
    ':id' => $adminId
]);


$admin =
    $statement->fetch();

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
        Account Settings | Day With Explore
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body class="admin-dashboard-body">


<!-- ==================================================
     ADMIN TOPBAR
================================================== -->

<nav class="admin-topbar">

    <div class="container-fluid">

        <div class="admin-topbar-inner">


            <a
                href="admin-dashboard.php"
                class="admin-dashboard-brand"
            >

                <i class="fa-solid fa-location-dot"></i>

                <span>
                    Day With Explore
                </span>

            </a>



            <div class="admin-user-area">


                <div class="admin-user-info">

                    <span>
                        Administrator
                    </span>


                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION[
                                'admin_username'
                            ]
                            ?? 'Admin'
                        ) ?>

                    </strong>

                </div>



                <form
                    action="admin-logout.php"
                    method="POST"
                >

                    <button
                        type="submit"
                        class="btn admin-logout-btn"
                    >

                        <i
                            class="fa-solid fa-right-from-bracket"
                        ></i>

                        Logout

                    </button>

                </form>


            </div>


        </div>

    </div>

</nav>



<!-- ==================================================
     SETTINGS
================================================== -->

<section class="admin-dashboard-section">

    <div class="container">


        <!-- HEADER -->

        <div class="admin-page-header">


            <div>


                <a
                    href="admin-dashboard.php"
                    class="admin-page-back"
                >

                    <i
                        class="fa-solid fa-arrow-left"
                    ></i>

                    Dashboard

                </a>


                <span
                    class="admin-section-label d-block mt-3"
                >

                    ACCOUNT SETTINGS

                </span>


                <h1>

                    Administrator Account

                </h1>


                <p>

                    Update the administrator
                    username and login password.

                </p>


            </div>


        </div>



        <div class="row g-4 mt-1">


            <!-- ==================================================
                 USERNAME SETTINGS
            ================================================== -->

            <div class="col-lg-6">


                <div
                    class="admin-panel-card admin-settings-card"
                >


                    <div class="admin-settings-icon">

                        <i
                            class="fa-solid fa-user-shield"
                        ></i>

                    </div>


                    <span
                        class="admin-section-label"
                    >

                        LOGIN USERNAME

                    </span>


                    <h3>

                        Change Username

                    </h3>


                    <p class="text-muted">

                        Change the username used
                        to sign in to the admin panel.

                    </p>



                    <?php if ($profileSuccess): ?>

                        <div
                            class="alert alert-success"
                        >

                            <i
                                class="fa-solid fa-circle-check me-2"
                            ></i>

                            Username updated successfully.

                        </div>

                    <?php endif; ?>



                    <?php if ($profileError): ?>

                        <div
                            class="alert alert-danger"
                        >

                            <i
                                class="fa-solid fa-circle-exclamation me-2"
                            ></i>

                            <?= htmlspecialchars(
                                $profileError
                            ) ?>

                        </div>

                    <?php endif; ?>



                    <form method="POST">


                        <input
                            type="hidden"
                            name="action_type"
                            value="username"
                        >


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                csrfToken()
                            ) ?>"
                        >



                        <!-- USERNAME -->

                        <div class="mb-4">


                            <label
                                class="form-label admin-form-label"
                            >

                                Username

                            </label>


                            <input
                                type="text"
                                name="username"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars(
                                    $admin[
                                        'username'
                                    ]
                                ) ?>"
                                required
                            >


                            <small
                                class="admin-form-help"
                            >

                                Minimum 4 characters.
                                Letters, numbers,
                                dot, underscore and
                                hyphen are allowed.

                            </small>


                        </div>



                        <!-- CURRENT PASSWORD -->

                        <div class="mb-4">


                            <label
                                class="form-label admin-form-label"
                            >

                                Current Password

                            </label>


                            <input
                                type="password"
                                name="current_password"
                                class="form-control admin-form-control"
                                placeholder="Enter current password"
                                autocomplete="current-password"
                                required
                            >


                            <small
                                class="admin-form-help"
                            >

                                Current password is
                                required to change
                                the username.

                            </small>


                        </div>



                        <button
                            type="submit"
                            class="btn admin-add-btn w-100"
                        >

                            <i
                                class="fa-solid fa-floppy-disk me-2"
                            ></i>

                            Update Username

                        </button>


                    </form>


                </div>


            </div>



            <!-- ==================================================
                 PASSWORD SETTINGS
            ================================================== -->

            <div class="col-lg-6">


                <div
                    class="admin-panel-card admin-settings-card"
                >


                    <div class="admin-settings-icon">

                        <i
                            class="fa-solid fa-lock"
                        ></i>

                    </div>


                    <span
                        class="admin-section-label"
                    >

                        LOGIN PASSWORD

                    </span>


                    <h3>

                        Change Password

                    </h3>


                    <p class="text-muted">

                        Set a new secure password
                        for the administrator account.

                    </p>



                    <?php if ($passwordSuccess): ?>

                        <div
                            class="alert alert-success"
                        >

                            <i
                                class="fa-solid fa-circle-check me-2"
                            ></i>

                            Password updated successfully.

                        </div>

                    <?php endif; ?>



                    <?php if ($passwordError): ?>

                        <div
                            class="alert alert-danger"
                        >

                            <i
                                class="fa-solid fa-circle-exclamation me-2"
                            ></i>

                            <?= htmlspecialchars(
                                $passwordError
                            ) ?>

                        </div>

                    <?php endif; ?>



                    <form method="POST">


                        <input
                            type="hidden"
                            name="action_type"
                            value="password"
                        >


                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                csrfToken()
                            ) ?>"
                        >



                        <!-- CURRENT PASSWORD -->

                        <div class="mb-3">


                            <label
                                class="form-label admin-form-label"
                            >

                                Current Password

                            </label>


                            <input
                                type="password"
                                name="current_password"
                                class="form-control admin-form-control"
                                placeholder="Enter current password"
                                autocomplete="current-password"
                                required
                            >


                        </div>



                        <!-- NEW PASSWORD -->

                        <div class="mb-3">


                            <label
                                class="form-label admin-form-label"
                            >

                                New Password

                            </label>


                            <div class="admin-password-field">


                                <input
                                    type="password"
                                    name="new_password"
                                    id="newPassword"
                                    class="form-control admin-form-control"
                                    placeholder="Enter new password"
                                    autocomplete="new-password"
                                    required
                                >


                                <button
                                    type="button"
                                    class="admin-password-toggle"
                                    data-target="newPassword"
                                    aria-label="Show password"
                                >

                                    <i
                                        class="fa-solid fa-eye"
                                    ></i>

                                </button>


                            </div>


                            <small
                                class="admin-form-help"
                            >

                                Use at least
                                8 characters.

                            </small>


                        </div>



                        <!-- CONFIRM PASSWORD -->

                        <div class="mb-4">


                            <label
                                class="form-label admin-form-label"
                            >

                                Confirm New Password

                            </label>


                            <div class="admin-password-field">


                                <input
                                    type="password"
                                    name="confirm_password"
                                    id="confirmNewPassword"
                                    class="form-control admin-form-control"
                                    placeholder="Enter new password again"
                                    autocomplete="new-password"
                                    required
                                >


                                <button
                                    type="button"
                                    class="admin-password-toggle"
                                    data-target="confirmNewPassword"
                                    aria-label="Show password"
                                >

                                    <i
                                        class="fa-solid fa-eye"
                                    ></i>

                                </button>


                            </div>


                        </div>



                        <button
                            type="submit"
                            class="btn admin-add-btn w-100"
                        >

                            <i
                                class="fa-solid fa-key me-2"
                            ></i>

                            Update Password

                        </button>


                    </form>


                </div>


            </div>


        </div>



        <!-- ==================================================
             SECURITY INFORMATION
        ================================================== -->

        <div
            class="admin-panel-card admin-security-notice mt-4"
        >


            <div class="admin-security-notice-icon">

                <i
                    class="fa-solid fa-shield-halved"
                ></i>

            </div>


            <div>


                <strong>

                    Account Security

                </strong>


                <p>

                    The administrator password
                    is securely hashed before
                    being stored in the SQLite
                    database. The original
                    password is not stored as
                    readable text.

                </p>


            </div>


        </div>


    </div>

</section>



<!-- ==================================================
     PASSWORD SHOW / HIDE
================================================== -->

<script>

document
    .querySelectorAll(
        ".admin-password-toggle"
    )
    .forEach(
        function (button) {


            button.addEventListener(
                "click",
                function () {


                    const targetId =
                        button.dataset.target;


                    const input =
                        document.getElementById(
                            targetId
                        );


                    if (!input) {
                        return;
                    }


                    const icon =
                        button.querySelector(
                            "i"
                        );


                    if (
                        input.type ===
                        "password"
                    ) {

                        input.type =
                            "text";


                        icon.classList.remove(
                            "fa-eye"
                        );


                        icon.classList.add(
                            "fa-eye-slash"
                        );


                    } else {


                        input.type =
                            "password";


                        icon.classList.remove(
                            "fa-eye-slash"
                        );


                        icon.classList.add(
                            "fa-eye"
                        );


                    }


                }
            );


        }
    );

</script>


</body>
</html>