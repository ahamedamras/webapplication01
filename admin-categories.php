<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$statement = $pdo->query("
    SELECT

        categories.id,
        categories.name,
        categories.slug,
        categories.created_at,

        COUNT(destinations.id) AS destination_count

    FROM categories

    LEFT JOIN destinations
        ON destinations.category_id = categories.id

    GROUP BY categories.id

    ORDER BY categories.name ASC
");


$categories =
    $statement->fetchAll();


$message = '';

$errorMessage = '';


if (isset($_GET['created'])) {

    $message =
        'Category added successfully.';

} elseif (isset($_GET['updated'])) {

    $message =
        'Category updated successfully.';

} elseif (isset($_GET['deleted'])) {

    $message =
        'Category deleted successfully.';

}


if (isset($_GET['inuse'])) {

    $errorMessage =
        'This category cannot be deleted because destinations are currently using it.';

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
        Categories | Day With Explore
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


<nav class="admin-topbar">

    <div class="container-fluid">

        <div class="admin-topbar-inner">


            <a
                href="admin-dashboard.php"
                class="admin-dashboard-brand"
            >

                <i class="fa-solid fa-location-dot"></i>

                Day With Explore

            </a>


            <div class="admin-user-area">

                <strong>

                    <?= htmlspecialchars(
                        $_SESSION['admin_username']
                    ) ?>

                </strong>


                <form
                    action="admin-logout.php"
                    method="POST"
                >

                    <button
                        type="submit"
                        class="btn admin-logout-btn"
                    >
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>



<section class="admin-dashboard-section">

    <div class="container-fluid">


        <div class="admin-page-header">


            <div>

                <a
                    href="admin-dashboard.php"
                    class="admin-page-back"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Dashboard

                </a>


                <span class="admin-section-label d-block mt-3">

                    CATEGORY MANAGEMENT

                </span>


                <h1>
                    Destination Categories
                </h1>


                <p>

                    Manage categories used to organize
                    tourist destinations.

                </p>

            </div>


            <a
                href="admin-category-add.php"
                class="btn admin-add-btn"
            >

                <i class="fa-solid fa-plus me-2"></i>

                Add Category

            </a>


        </div>



        <?php if ($message): ?>

            <div class="alert alert-success mt-4">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if ($errorMessage): ?>

            <div class="alert alert-danger mt-4">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                <?= htmlspecialchars($errorMessage) ?>

            </div>

        <?php endif; ?>



        <div class="admin-panel-card mt-4">


            <div class="admin-panel-header">


                <div>

                    <span class="admin-section-label">
                        DATABASE
                    </span>

                    <h4>
                        All Categories
                    </h4>

                </div>


                <span class="admin-coming-badge">

                    <?= count($categories) ?>

                    Categories

                </span>


            </div>



            <?php if ($categories): ?>


                <div class="table-responsive">


                    <table class="table admin-table align-middle">


                        <thead>

                            <tr>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Slug
                                </th>

                                <th>
                                    Destinations
                                </th>

                                <th>
                                    Created
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($categories as $category): ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $category['name']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <span class="admin-slug-text">

                                        <?= htmlspecialchars(
                                            $category['slug']
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="admin-table-category">

                                        <?= (int) $category['destination_count'] ?>

                                        Places

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $category['created_at']
                                    ) ?>

                                </td>


                                <td class="text-end">


                                    <a
                                        href="categories.php"
                                        target="_blank"
                                        class="btn admin-icon-btn"
                                        title="View Public Categories"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    <a
                                        href="admin-category-edit.php?id=<?= (int) $category['id'] ?>"
                                        class="btn admin-icon-btn"
                                        title="Edit"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <form
                                        action="admin-category-delete.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this category?');"
                                    >


                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                csrfToken()
                                            ) ?>"
                                        >


                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $category['id'] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="btn admin-icon-btn admin-delete-icon"
                                            title="Delete"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>


                                    </form>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <div class="text-center py-5">

                    <i class="fa-solid fa-layer-group fs-1 text-secondary mb-3"></i>

                    <h4>
                        No Categories
                    </h4>

                    <p class="text-muted">

                        Create your first destination category.

                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>

</section>


</body>
</html>