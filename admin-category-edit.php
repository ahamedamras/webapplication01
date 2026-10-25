<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id) {

    header(
        'Location: admin-categories.php'
    );

    exit;

}


$statement =
    $pdo->prepare("
        SELECT *
        FROM categories
        WHERE id = :id
        LIMIT 1
    ");


$statement->execute([
    ':id' => $id
]);


$category =
    $statement->fetch();


if (!$category) {

    header(
        'Location: admin-categories.php'
    );

    exit;

}


$error = '';

$name =
    $category['name'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $name =
        trim($_POST['name'] ?? '');


    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error =
            'Invalid request.';

    } elseif ($name === '') {

        $error =
            'Category name is required.';

    } else {


        $checkStatement =
            $pdo->prepare("
                SELECT COUNT(*)

                FROM categories

                WHERE LOWER(name) = LOWER(:name)
                AND id != :id
            ");


        $checkStatement->execute([

            ':name' =>
                $name,

            ':id' =>
                $id

        ]);


        if (
            (int) $checkStatement->fetchColumn()
            > 0
        ) {

            $error =
                'Another category already uses this name.';

        } else {


            $updateStatement =
                $pdo->prepare("
                    UPDATE categories

                    SET name = :name

                    WHERE id = :id
                ");


            $updateStatement->execute([

                ':name' =>
                    $name,

                ':id' =>
                    $id

            ]);


            header(
                'Location: admin-categories.php?updated=1'
            );

            exit;

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
        Edit Category | Day With Explore
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


<body class="admin-dashboard-body">


<section class="admin-dashboard-section">

    <div class="container">


        <div class="admin-form-wrapper">


            <a
                href="admin-categories.php"
                class="admin-page-back"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Categories

            </a>



            <div class="admin-form-card mt-3">


                <span class="admin-section-label">

                    EDIT CATEGORY

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $category['name']
                    ) ?>

                </h1>


                <p class="text-muted">

                    URL slug:

                    <strong>

                        <?= htmlspecialchars(
                            $category['slug']
                        ) ?>

                    </strong>

                </p>



                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>



                <form method="POST">


                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            csrfToken()
                        ) ?>"
                    >


                    <div class="row g-4">


                        <div class="col-12">

                            <label class="form-label admin-form-label">

                                Category Name *

                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($name) ?>"
                                required
                            >

                        </div>


                    </div>



                    <div class="admin-form-actions">


                        <a
                            href="admin-categories.php"
                            class="btn admin-cancel-btn"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="btn admin-add-btn"
                        >

                            <i class="fa-solid fa-floppy-disk me-2"></i>

                            Update Category

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </div>

</section>


</body>
</html>