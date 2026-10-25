<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


/*
|--------------------------------------------------------------------------
| GET DESTINATIONS
|--------------------------------------------------------------------------
*/

$statement = $pdo->query("
    SELECT

        destinations.id,
        destinations.name,
        destinations.slug,
        destinations.location,
        destinations.distance_km,
        destinations.is_featured,

        categories.name AS category_name

    FROM destinations

    LEFT JOIN categories
        ON destinations.category_id = categories.id

    ORDER BY destinations.id DESC
");


$destinations =
    $statement->fetchAll();


/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/

$message = '';

if (isset($_GET['created'])) {

    $message =
        'Destination added successfully.';

} elseif (isset($_GET['updated'])) {

    $message =
        'Destination updated successfully.';

} elseif (isset($_GET['deleted'])) {

    $message =
        'Destination deleted successfully.';

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
        Manage Destinations | Day With Explore
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


<!-- ================= TOP BAR ================= -->

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
                            $_SESSION['admin_username']
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

                        <i class="fa-solid fa-right-from-bracket"></i>

                        Logout

                    </button>

                </form>


            </div>

        </div>

    </div>

</nav>



<section class="admin-dashboard-section">

    <div class="container-fluid">


        <!-- ================= HEADER ================= -->

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

                    DESTINATION MANAGEMENT

                </span>


                <h1>
                    Tourist Destinations
                </h1>


                <p>
                    Add, edit and remove tourism information.
                </p>

            </div>


            <a
                href="admin-destination-add.php"
                class="btn admin-add-btn"
            >

                <i class="fa-solid fa-plus me-2"></i>

                Add Destination

            </a>

        </div>



        <?php if ($message): ?>

            <div class="alert alert-success mt-4">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>



        <!-- ================= TABLE ================= -->

        <div class="admin-panel-card mt-4">


            <div class="admin-panel-header">

                <div>

                    <span class="admin-section-label">
                        DATABASE
                    </span>

                    <h4>
                        All Destinations
                    </h4>

                </div>


                <span class="admin-coming-badge">

                    <?= count($destinations) ?>

                    Places

                </span>

            </div>



            <?php if ($destinations): ?>


                <div class="table-responsive">


                    <table
                        class="table admin-table align-middle"
                    >

                        <thead>

                            <tr>

                                <th>
                                    Destination
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Location
                                </th>

                                <th>
                                    Distance
                                </th>

                                <th>
                                    Featured
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($destinations as $place): ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $place['name']
                                        ) ?>

                                    </strong>


                                    <small class="admin-slug-text">

                                        <?= htmlspecialchars(
                                            $place['slug']
                                        ) ?>

                                    </small>

                                </td>


                                <td>

                                    <span class="admin-table-category">

                                        <?= htmlspecialchars(
                                            $place['category_name']
                                            ?? 'Other'
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $place['location']
                                        ?? 'Not listed'
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $place['distance_km']
                                        !== null
                                    ): ?>

                                        <?= htmlspecialchars(
                                            $place['distance_km']
                                        ) ?>

                                        km

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if (
                                        (int) $place['is_featured'] === 1
                                    ): ?>

                                        <span class="admin-featured-yes">

                                            <i class="fa-solid fa-star"></i>

                                            Yes

                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            No
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td class="text-end">


                                    <a
                                        href="destination-details.php?id=<?= urlencode(
                                            $place['slug']
                                        ) ?>"
                                        target="_blank"
                                        class="btn admin-icon-btn"
                                        title="View"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    <a
                                        href="admin-destination-edit.php?id=<?= (int) $place['id'] ?>"
                                        class="btn admin-icon-btn"
                                        title="Edit"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <form
                                        action="admin-destination-delete.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this destination?');"
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
                                            value="<?= (int) $place['id'] ?>"
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

                    <i
                        class="fa-solid fa-location-dot
                               fs-1 text-secondary mb-3"
                    ></i>

                    <h4>
                        No Destinations
                    </h4>

                    <p class="text-muted">

                        Add your first tourist destination.

                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>

</section>


</body>
</html>