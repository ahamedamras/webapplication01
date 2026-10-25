<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


/*
|--------------------------------------------------------------------------
| DASHBOARD COUNTS
|--------------------------------------------------------------------------
*/

$totalDestinations =
    (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM destinations
        ")
        ->fetchColumn();


$totalCategories =
    (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM categories
        ")
        ->fetchColumn();


$totalFacilities =
    (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM nearby_facilities
        ")
        ->fetchColumn();


$featuredDestinations =
    (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM destinations
            WHERE is_featured = 1
        ")
        ->fetchColumn();


/*
|--------------------------------------------------------------------------
| EMERGENCY CONTACT COUNT
|--------------------------------------------------------------------------
*/

$totalEmergencyContacts = 0;


try {

    $totalEmergencyContacts =
        (int) $pdo
            ->query("
                SELECT COUNT(*)
                FROM emergency_contacts
            ")
            ->fetchColumn();

} catch (PDOException $e) {

    $totalEmergencyContacts = 0;

}


/*
|--------------------------------------------------------------------------
| RECENT DESTINATIONS
|--------------------------------------------------------------------------
*/

$recentStatement =
    $pdo->query("
        SELECT

            destinations.id,
            destinations.name,
            destinations.slug,
            destinations.location,
            destinations.distance_km,

            categories.name AS category_name

        FROM destinations

        LEFT JOIN categories
            ON destinations.category_id = categories.id

        ORDER BY destinations.id DESC

        LIMIT 5
    ");


$recentDestinations =
    $recentStatement->fetchAll();

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
        Admin Dashboard | Day With Explore
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
     TOP NAV
================================================== -->

<nav class="admin-topbar">

    <div class="container-fluid">

        <div class="admin-topbar-inner">


            <!-- BRAND -->

            <a
                href="admin-dashboard.php"
                class="admin-dashboard-brand"
            >

                <i class="fa-solid fa-location-dot"></i>

                <span>
                    Day With Explore
                </span>

            </a>



            <!-- ADMIN USER -->

            <div class="admin-user-area">


                <div class="admin-user-info">

                    <span>
                        Administrator
                    </span>


                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['admin_username']
                            ?? 'Admin'
                        ) ?>

                    </strong>

                </div>



                <!-- LOGOUT -->

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
     DASHBOARD
================================================== -->

<section class="admin-dashboard-section">

    <div class="container-fluid">


        <!-- ==================================================
             WELCOME AREA
        ================================================== -->

        <div class="admin-welcome-row">


            <div>


                <span class="admin-section-label">

                    ADMIN DASHBOARD

                </span>


                <h1>

                    Tourism Management

                </h1>


                <p>

                    Manage destinations, categories,
                    nearby facilities and emergency
                    visitor information.

                </p>


            </div>



            <a
                href="index.php"
                target="_blank"
                rel="noopener noreferrer"
                class="btn admin-view-site-btn"
            >

                <i
                    class="fa-solid fa-arrow-up-right-from-square me-2"
                ></i>

                View Website

            </a>


        </div>



        <!-- ==================================================
             STATISTICS
        ================================================== -->

        <div class="row g-4 mt-2">


            <!-- DESTINATIONS -->

            <div class="col-sm-6 col-xl-3">

                <div class="admin-stat-card">


                    <div class="admin-stat-icon">

                        <i
                            class="fa-solid fa-location-dot"
                        ></i>

                    </div>


                    <div>

                        <span>
                            Destinations
                        </span>

                        <h2>

                            <?= $totalDestinations ?>

                        </h2>

                    </div>


                </div>

            </div>



            <!-- CATEGORIES -->

            <div class="col-sm-6 col-xl-3">

                <div class="admin-stat-card">


                    <div class="admin-stat-icon">

                        <i
                            class="fa-solid fa-layer-group"
                        ></i>

                    </div>


                    <div>

                        <span>
                            Categories
                        </span>

                        <h2>

                            <?= $totalCategories ?>

                        </h2>

                    </div>


                </div>

            </div>



            <!-- NEARBY FACILITIES -->

            <div class="col-sm-6 col-xl-3">

                <div class="admin-stat-card">


                    <div class="admin-stat-icon">

                        <i
                            class="fa-solid fa-building"
                        ></i>

                    </div>


                    <div>

                        <span>
                            Nearby Facilities
                        </span>

                        <h2>

                            <?= $totalFacilities ?>

                        </h2>

                    </div>


                </div>

            </div>



            <!-- FEATURED -->

            <div class="col-sm-6 col-xl-3">

                <div class="admin-stat-card">


                    <div class="admin-stat-icon">

                        <i
                            class="fa-solid fa-star"
                        ></i>

                    </div>


                    <div>

                        <span>
                            Featured
                        </span>

                        <h2>

                            <?= $featuredDestinations ?>

                        </h2>

                    </div>


                </div>

            </div>


        </div>



        <!-- ==================================================
             MAIN MANAGEMENT AREA
        ================================================== -->

        <div
            class="row g-4 mt-3 align-items-start"
        >


            <!-- ==================================================
                 LEFT SIDE
                 RECENT DESTINATIONS
            ================================================== -->

            <div class="col-lg-8">


                <div
                    class="admin-panel-card h-auto"
                >


                    <!-- HEADER -->

                    <div class="admin-panel-header">


                        <div>


                            <span class="admin-section-label">

                                DESTINATIONS

                            </span>


                            <h4>

                                Recent Tourist Places

                            </h4>


                        </div>



                        <a
                            href="admin-destinations.php"
                            class="admin-coming-badge text-decoration-none"
                        >

                            View All

                        </a>


                    </div>



                    <!-- DESTINATION TABLE -->

                    <?php if ($recentDestinations): ?>


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


                                        <th class="text-end">
                                            Action
                                        </th>


                                    </tr>

                                </thead>



                                <tbody>


                                    <?php foreach (
                                        $recentDestinations
                                        as $place
                                    ): ?>


                                        <tr>


                                            <!-- DESTINATION NAME -->

                                            <td>

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        $place['name']
                                                    ) ?>

                                                </strong>

                                            </td>



                                            <!-- CATEGORY -->

                                            <td>

                                                <span
                                                    class="admin-table-category"
                                                >

                                                    <?= htmlspecialchars(
                                                        $place[
                                                            'category_name'
                                                        ]
                                                        ?? 'Other'
                                                    ) ?>

                                                </span>

                                            </td>



                                            <!-- LOCATION -->

                                            <td>

                                                <?= htmlspecialchars(
                                                    !empty(
                                                        $place['location']
                                                    )
                                                        ? $place['location']
                                                        : 'Not listed'
                                                ) ?>

                                            </td>



                                            <!-- DISTANCE -->

                                            <td>

                                                <?php if (
                                                    $place['distance_km']
                                                    !== null
                                                ): ?>


                                                    <?= htmlspecialchars(
                                                        $place[
                                                            'distance_km'
                                                        ]
                                                    ) ?>

                                                    km


                                                <?php else: ?>


                                                    -


                                                <?php endif; ?>

                                            </td>



                                            <!-- ACTION -->

                                            <td class="text-end">


                                                <a
                                                    href="destination-details.php?id=<?= urlencode(
                                                        $place['slug']
                                                    ) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn admin-icon-btn"
                                                    title="View Destination"
                                                >

                                                    <i
                                                        class="fa-solid fa-eye"
                                                    ></i>

                                                </a>


                                            </td>


                                        </tr>


                                    <?php endforeach; ?>


                                </tbody>


                            </table>


                        </div>


                    <?php else: ?>


                        <!-- EMPTY STATE -->

                        <div class="text-center py-5">


                            <i
                                class="fa-solid fa-location-dot fs-1 text-secondary mb-3"
                            ></i>


                            <h4>

                                No Destinations

                            </h4>


                            <p class="text-muted">

                                Add your first tourist
                                destination.

                            </p>


                            <a
                                href="admin-destination-add.php"
                                class="btn admin-add-btn"
                            >

                                <i
                                    class="fa-solid fa-plus me-2"
                                ></i>

                                Add Destination

                            </a>


                        </div>


                    <?php endif; ?>


                </div>


            </div>



            <!-- ==================================================
                 RIGHT SIDE
            ================================================== -->

            <div class="col-lg-4">


                <!-- ==================================================
                     QUICK ACTIONS
                ================================================== -->

                <div
                    class="admin-panel-card h-auto"
                >


                    <span class="admin-section-label">

                        QUICK ACTIONS

                    </span>


                    <h4 class="mb-4">

                        Management

                    </h4>



                    <!-- ADD DESTINATION -->

                    <a
                        href="admin-destination-add.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-plus"
                        ></i>


                        <div>

                            <strong>

                                Add Destination

                            </strong>

                            <span>

                                Create a new tourist place

                            </span>

                        </div>


                    </a>



                    <!-- MANAGE DESTINATIONS -->

                    <a
                        href="admin-destinations.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-list"
                        ></i>


                        <div>

                            <strong>

                                Manage Destinations

                            </strong>

                            <span>

                                View, edit and delete places

                            </span>

                        </div>


                    </a>



                    <!-- MANAGE CATEGORIES -->

                    <a
                        href="admin-categories.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-layer-group"
                        ></i>


                        <div>

                            <strong>

                                Manage Categories

                            </strong>

                            <span>

                                Add and organize categories

                            </span>

                        </div>


                    </a>



                    <!-- MANAGE NEARBY FACILITIES -->

                    <a
                        href="admin-facilities.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-building"
                        ></i>


                        <div>

                            <strong>

                                Manage Nearby Facilities

                            </strong>

                            <span>

                                Restaurants, hotels, ATM and more

                            </span>

                        </div>


                    </a>



                    <!-- MANAGE EMERGENCY CONTACTS -->

                    <a
                        href="admin-emergency.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-phone-volume"
                        ></i>


                        <div>

                            <strong>

                                Manage Emergency Contacts

                            </strong>

                            <span>

                                Update visitor emergency information

                            </span>

                        </div>


                    </a>



                    <!-- ACCOUNT SETTINGS -->

                    <a
                        href="admin-settings.php"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-gear"
                        ></i>


                        <div>

                            <strong>

                                Account Settings

                            </strong>

                            <span>

                                Change admin username and password

                            </span>

                        </div>


                    </a>



                    <!-- VIEW PUBLIC CATEGORIES -->

                    <a
                        href="categories.php"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="admin-action-item text-decoration-none"
                    >

                        <i
                            class="fa-solid fa-eye"
                        ></i>


                        <div>

                            <strong>

                                View Public Categories

                            </strong>

                            <span>

                                Check the visitor category page

                            </span>

                        </div>


                    </a>


                </div>



                <!-- ==================================================
                     SYSTEM SUMMARY
                ================================================== -->

                <div
                    class="admin-panel-card h-auto mt-4"
                >


                    <span class="admin-section-label">

                        SYSTEM SUMMARY

                    </span>


                    <h4 class="mb-4">

                        Current Content

                    </h4>



                    <!-- TOURIST PLACES -->

                    <div class="admin-dashboard-mini-stat">


                        <span>

                            <i
                                class="fa-solid fa-location-dot"
                            ></i>

                            Tourist Places

                        </span>


                        <strong>

                            <?= $totalDestinations ?>

                        </strong>


                    </div>



                    <!-- CATEGORIES -->

                    <div class="admin-dashboard-mini-stat">


                        <span>

                            <i
                                class="fa-solid fa-layer-group"
                            ></i>

                            Categories

                        </span>


                        <strong>

                            <?= $totalCategories ?>

                        </strong>


                    </div>



                    <!-- FACILITIES -->

                    <div class="admin-dashboard-mini-stat">


                        <span>

                            <i
                                class="fa-solid fa-building"
                            ></i>

                            Nearby Facilities

                        </span>


                        <strong>

                            <?= $totalFacilities ?>

                        </strong>


                    </div>



                    <!-- EMERGENCY CONTACTS -->

                    <div class="admin-dashboard-mini-stat">


                        <span>

                            <i
                                class="fa-solid fa-phone-volume"
                            ></i>

                            Emergency Contacts

                        </span>


                        <strong>

                            <?= $totalEmergencyContacts ?>

                        </strong>


                    </div>


                </div>


            </div>


        </div>


    </div>

</section>



</body>

</html>