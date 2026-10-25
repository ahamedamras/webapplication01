<?php

require_once __DIR__ . '/config/database.php';


$statement = $pdo->query("
    SELECT

        categories.id,
        categories.name,
        categories.slug,

        COUNT(destinations.id) AS destination_count

    FROM categories

    LEFT JOIN destinations
        ON destinations.category_id = categories.id

    GROUP BY categories.id

    ORDER BY categories.name ASC
");


$categories =
    $statement->fetchAll();



function getCategoryIcon(
    string $slug
): string {

    $icons = [

        'beach' =>
            'fa-umbrella-beach',

        'historical' =>
            'fa-landmark',

        'religious' =>
            'fa-place-of-worship',

        'cultural' =>
            'fa-masks-theater',

        'nature' =>
            'fa-leaf',

        'food' =>
            'fa-utensils',

        'landmark' =>
            'fa-location-dot',

        'community' =>
            'fa-people-group'

    ];


    return $icons[$slug]
        ?? 'fa-compass';

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
    <meta
    name="description"
    content="Day With Explore helps visitors discover tourist attractions, nearby facilities, routes and emergency information around Eravur and Batticaloa."
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


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">

    <div class="container">


        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >

            <span class="logo-icon">

                <i class="fa-solid fa-location-dot"></i>

            </span>

            Day With

            <span class="brand-highlight">
                Explore
            </span>

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="index.php"
                    >
                        Home
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="destinations.php"
                    >
                        Destinations
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="categories.php"
                    >
                        Categories
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="map.php"
                    >
                        Map
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="nearby.php"
                    >
                        Nearby
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="planner.php"
                    >
                        My Trip
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="emergency.php"
                    >
                        Emergency
                    </a>

                </li>


                <li class="nav-item ms-lg-2">

                    <a
                        class="btn btn-outline-primary rounded-pill px-4"
                        href="admin-login.php"
                    >

                        <i class="fa-solid fa-user-shield me-1"></i>

                        Admin

                    </a>

                </li>


            </ul>

        </div>

    </div>

</nav>



<!-- HEADER -->

<section class="category-page-header">

    <div class="container text-center">


        <span class="page-badge">

            <i class="fa-solid fa-layer-group"></i>

            Browse by Interest

        </span>


        <h1>

            Explore by

            <span>
                Category
            </span>

        </h1>


        <p>

            Choose a category to quickly discover
            tourist destinations that match your interests.

        </p>


    </div>

</section>



<!-- CATEGORIES -->

<section class="public-category-section">

    <div class="container">


        <?php if ($categories): ?>


            <div class="row g-4">


                <?php foreach ($categories as $category): ?>


                    <div class="col-sm-6 col-lg-3">


                        <a
                            href="destinations.php?category=<?= urlencode(
                                $category['slug']
                            ) ?>"
                            class="public-category-card"
                        >


                            <div class="public-category-icon">

                                <i
                                    class="fa-solid <?= htmlspecialchars(
                                        getCategoryIcon(
                                            $category['slug']
                                        )
                                    ) ?>"
                                ></i>

                            </div>


                            <h4>

                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>

                            </h4>


                            <span>

                                <?= (int) $category['destination_count'] ?>

                                <?= (int) $category['destination_count'] === 1
                                    ? 'Destination'
                                    : 'Destinations' ?>

                            </span>


                            <div class="public-category-arrow">

                                Explore

                                <i class="fa-solid fa-arrow-right"></i>

                            </div>


                        </a>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="text-center py-5">

                <i class="fa-solid fa-layer-group fs-1 text-secondary mb-3"></i>

                <h3>
                    No Categories Available
                </h3>

            </div>


        <?php endif; ?>


    </div>

</section>



<footer>

    <div class="container">

        <div class="footer-content">


            <div>

                <h5>

                    <i class="fa-solid fa-location-dot me-2"></i>

                    Day With Explore

                </h5>

                <p>

                    Local Tourist Day-Visit Planner &
                    Information System.

                </p>

            </div>


            <div class="footer-links">

                <a href="index.php">
                    Home
                </a>

                <a href="destinations.php">
                    Destinations
                </a>

                <a href="categories.php">
                    Categories
                </a>

                <a href="map.php">
                    Map
                </a>

                <a href="emergency.php">
                    Emergency
                </a>

            </div>


        </div>


        <hr>


        <p class="copyright">

            © 2026 Day With Explore.
            All rights reserved.

        </p>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>
</html>