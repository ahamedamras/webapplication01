<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$statement = $pdo->query("
    SELECT
        nearby_facilities.id,
        nearby_facilities.name,
        nearby_facilities.facility_type,
        nearby_facilities.address,
        nearby_facilities.contact,
        nearby_facilities.distance_km,

        destinations.name AS destination_name

    FROM nearby_facilities

    LEFT JOIN destinations
        ON nearby_facilities.destination_id = destinations.id

    ORDER BY nearby_facilities.id DESC
");


$facilities = $statement->fetchAll();


$message = '';

if (isset($_GET['created'])) {

    $message =
        'Nearby facility added successfully.';

} elseif (isset($_GET['updated'])) {

    $message =
        'Nearby facility updated successfully.';

} elseif (isset($_GET['deleted'])) {

    $message =
        'Nearby facility deleted successfully.';

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
        Nearby Facilities | Day With Explore
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

                    FACILITY MANAGEMENT

                </span>

                <h1>
                    Nearby Facilities
                </h1>

                <p>

                    Manage restaurants, hotels,
                    hospitals, ATMs and other services.

                </p>

            </div>


            <a
                href="admin-facility-add.php"
                class="btn admin-add-btn"
            >

                <i class="fa-solid fa-plus me-2"></i>

                Add Facility

            </a>

        </div>


        <?php if ($message): ?>

            <div class="alert alert-success mt-4">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <div class="admin-panel-card mt-4">


            <div class="admin-panel-header">

                <div>

                    <span class="admin-section-label">
                        DATABASE
                    </span>

                    <h4>
                        All Nearby Facilities
                    </h4>

                </div>

                <span class="admin-coming-badge">

                    <?= count($facilities) ?>

                    Facilities

                </span>

            </div>


            <?php if ($facilities): ?>

                <div class="table-responsive">

                    <table
                        class="table admin-table align-middle"
                    >

                        <thead>

                            <tr>

                                <th>
                                    Facility
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Near Destination
                                </th>

                                <th>
                                    Distance
                                </th>

                                <th>
                                    Contact
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach ($facilities as $facility): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $facility['name']
                                        ) ?>

                                    </strong>

                                    <small class="admin-slug-text">

                                        <?= htmlspecialchars(
                                            $facility['address']
                                            ?? 'Address not listed'
                                        ) ?>

                                    </small>

                                </td>


                                <td>

                                    <span class="admin-table-category">

                                        <?= htmlspecialchars(
                                            $facility['facility_type']
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $facility['destination_name']
                                        ?? 'Unknown'
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $facility['distance_km']
                                        !== null
                                    ): ?>

                                        <?= htmlspecialchars(
                                            $facility['distance_km']
                                        ) ?>
                                        km

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $facility['contact']
                                        ?? '-'
                                    ) ?>

                                </td>


                                <td class="text-end">


                                    <a
                                        href="admin-facility-edit.php?id=<?= (int) $facility['id'] ?>"
                                        class="btn admin-icon-btn"
                                        title="Edit"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <form
                                        action="admin-facility-delete.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this nearby facility?');"
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
                                            value="<?= (int) $facility['id'] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="btn admin-icon-btn admin-delete-icon"
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
                        class="fa-solid fa-building
                               fs-1 text-secondary mb-3"
                    ></i>

                    <h4>
                        No Nearby Facilities
                    </h4>

                    <p class="text-muted">

                        Add the first facility
                        using the button above.

                    </p>

                </div>

            <?php endif; ?>


        </div>

    </div>

</section>

</body>
</html>