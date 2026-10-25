<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/upload.php';


/*
|--------------------------------------------------------------------------
| HOME PAGE STATISTICS
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


$totalFeatured =
    (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM destinations
            WHERE is_featured = 1
        ")
        ->fetchColumn();


/*
|--------------------------------------------------------------------------
| ACTIVE EMERGENCY CONTACT COUNT
|--------------------------------------------------------------------------
*/

$totalEmergencyContacts = 0;

try {

    $totalEmergencyContacts =
        (int) $pdo
            ->query("
                SELECT COUNT(*)
                FROM emergency_contacts
                WHERE is_active = 1
            ")
            ->fetchColumn();

} catch (PDOException $e) {

    $totalEmergencyContacts = 0;

}


/*
|--------------------------------------------------------------------------
| FEATURED DESTINATIONS
|--------------------------------------------------------------------------
*/

$featuredStatement =
    $pdo->query("
        SELECT

            destinations.id,
            destinations.name,
            destinations.slug,
            destinations.description,
            destinations.location,
            destinations.distance_km,
            destinations.image,

            categories.name AS category_name,
            categories.slug AS category_slug

        FROM destinations

        LEFT JOIN categories
            ON destinations.category_id = categories.id

        WHERE destinations.is_featured = 1

        ORDER BY destinations.id DESC

        LIMIT 3
    ");


$featuredDestinations =
    $featuredStatement->fetchAll();


/*
|--------------------------------------------------------------------------
| FALLBACK IF NO FEATURED PLACES
|--------------------------------------------------------------------------
*/

if (!$featuredDestinations) {

    $featuredStatement =
        $pdo->query("
            SELECT

                destinations.id,
                destinations.name,
                destinations.slug,
                destinations.description,
                destinations.location,
                destinations.distance_km,
                destinations.image,

                categories.name AS category_name,
                categories.slug AS category_slug

            FROM destinations

            LEFT JOIN categories
                ON destinations.category_id = categories.id

            ORDER BY destinations.id DESC

            LIMIT 3
        ");


    $featuredDestinations =
        $featuredStatement->fetchAll();

}


/*
|--------------------------------------------------------------------------
| HOME CATEGORIES
|--------------------------------------------------------------------------
*/

$categoryStatement =
    $pdo->query("
        SELECT

            categories.id,
            categories.name,
            categories.slug,

            COUNT(destinations.id)
                AS destination_count

        FROM categories

        LEFT JOIN destinations
            ON destinations.category_id = categories.id

        GROUP BY
            categories.id,
            categories.name,
            categories.slug

        ORDER BY
            destination_count DESC,
            categories.name ASC

        LIMIT 8
    ");


$categories =
    $categoryStatement->fetchAll();


/*
|--------------------------------------------------------------------------
| CATEGORY ICON
|--------------------------------------------------------------------------
*/

function getHomeCategoryIcon(
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


    return
        $icons[$slug]
        ?? 'fa-compass';

}


/*
|--------------------------------------------------------------------------
| FALLBACK DESTINATION IMAGE CLASS
|--------------------------------------------------------------------------
*/

function getHomeImageClass(
    string $slug
): string {

    $classes = [

        'pasikudah' =>
            'place-beach',

        'elephant-rock' =>
            'place-elephant',

        'batticaloa-fort' =>
            'place-fort',

        'lagoon' =>
            'place-lagoon',

        'aksaa-mosque' =>
            'place-mosque',

        'lighthouse' =>
            'place-lighthouse',

        'hospice' =>
            'place-hospice',

        'heritage-museum' =>
            'place-museum',

        'kattankudy-beach' =>
            'place-kattankudy',

        'food-bin' =>
            'place-food'

    ];


    return
        $classes[$slug]
        ?? 'place-lagoon';

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
        Day With Explore | Local Tourist Day-Visit Planner
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


<body>


<!-- ==================================================
     NAVBAR
================================================== -->

<nav
    class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top"
>

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
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul
                class="navbar-nav ms-auto align-items-lg-center gap-lg-2"
            >


                <li class="nav-item">

                    <a
                        class="nav-link active"
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
                        class="nav-link"
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

                        <i class="fa-solid fa-route me-1"></i>

                        My Trip

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="emergency.php"
                    >

                        <i
                            class="fa-solid fa-phone-volume me-1"
                        ></i>

                        Emergency

                    </a>

                </li>


                <li class="nav-item ms-lg-2">

                    <a
                        class="btn btn-outline-primary rounded-pill px-4"
                        href="admin-login.php"
                    >

                        <i
                            class="fa-solid fa-user-shield me-1"
                        ></i>

                        Admin

                    </a>

                </li>


            </ul>

        </div>

    </div>

</nav>



<!-- ==================================================
     HERO
================================================== -->

<section class="home-dynamic-hero">

    <div class="container">

        <div class="row align-items-center g-5">


            <!-- LEFT -->

            <div class="col-lg-7">


                <span class="page-badge">

                    <i class="fa-solid fa-compass"></i>

                    Explore Eravur & Batticaloa

                </span>


                <h1>

                    Plan an Amazing

                    <span>
                        Day With Explore
                    </span>

                </h1>


                <p class="home-hero-description">

                    Discover local tourist attractions,
                    nearby facilities, emergency information
                    and build your own one-day travel plan.

                </p>



                <!-- SEARCH -->

                <form
                    id="homeDestinationSearch"
                    class="home-main-search"
                >

                    <div class="home-search-field">

                        <i
                            class="fa-solid fa-magnifying-glass"
                        ></i>


                        <input
                            type="text"
                            id="homeSearchInput"
                            placeholder="Where would you like to explore?"
                            autocomplete="off"
                        >


                        <button
                            type="submit"
                            class="btn"
                        >

                            Search

                        </button>

                    </div>

                </form>



                <div class="home-hero-actions">


                    <a
                        href="destinations.php"
                        class="btn explore-btn"
                    >

                        <i
                            class="fa-solid fa-compass me-2"
                        ></i>

                        Explore Destinations

                    </a>


                    <a
                        href="planner.php"
                        class="btn home-planner-outline-btn"
                    >

                        <i
                            class="fa-solid fa-route me-2"
                        ></i>

                        Plan My Trip

                    </a>


                </div>


            </div>



            <!-- RIGHT -->

            <div class="col-lg-5">


                <div class="home-hero-info-card">


                    <div class="home-hero-map-icon">

                        <i class="fa-solid fa-map-location-dot"></i>

                    </div>


                    <span>
                        YOUR LOCAL TRAVEL GUIDE
                    </span>


                    <h3>

                        Everything for your
                        local day visit

                    </h3>


                    <div class="home-hero-mini-features">


                        <div>

                            <i class="fa-solid fa-location-dot"></i>

                            <span>
                                Tourist Places
                            </span>

                        </div>


                        <div>

                            <i class="fa-solid fa-building"></i>

                            <span>
                                Nearby Services
                            </span>

                        </div>


                        <div>

                            <i class="fa-solid fa-route"></i>

                            <span>
                                Trip Planner
                            </span>

                        </div>


                        <div>

                            <i class="fa-solid fa-phone-volume"></i>

                            <span>
                                Emergency Help
                            </span>

                        </div>


                    </div>


                </div>


            </div>


        </div>

    </div>

</section>



<!-- ==================================================
     LIVE DATABASE STATS
================================================== -->

<section class="home-stats-section">

    <div class="container">


        <div class="row g-3">


            <div class="col-6 col-lg-3">

                <div class="home-stat-card">

                    <i class="fa-solid fa-location-dot"></i>

                    <strong>
                        <?= $totalDestinations ?>
                    </strong>

                    <span>
                        Destinations
                    </span>

                </div>

            </div>


            <div class="col-6 col-lg-3">

                <div class="home-stat-card">

                    <i class="fa-solid fa-layer-group"></i>

                    <strong>
                        <?= $totalCategories ?>
                    </strong>

                    <span>
                        Categories
                    </span>

                </div>

            </div>


            <div class="col-6 col-lg-3">

                <div class="home-stat-card">

                    <i class="fa-solid fa-building"></i>

                    <strong>
                        <?= $totalFacilities ?>
                    </strong>

                    <span>
                        Nearby Facilities
                    </span>

                </div>

            </div>


            <div class="col-6 col-lg-3">

                <div class="home-stat-card">

                    <i class="fa-solid fa-star"></i>

                    <strong>
                        <?= $totalFeatured ?>
                    </strong>

                    <span>
                        Featured Places
                    </span>

                </div>

            </div>


        </div>


    </div>

</section>



<!-- ==================================================
     CATEGORIES
================================================== -->

<section class="home-dynamic-section">

    <div class="container">


        <div class="home-section-heading">


            <div>

                <span class="home-section-label">
                    BROWSE BY INTEREST
                </span>


                <h2>

                    Explore Categories

                </h2>


                <p>

                    Find tourist places based on
                    the experience you are looking for.

                </p>

            </div>


            <a
                href="categories.php"
                class="home-view-all-link"
            >

                View All Categories

                <i
                    class="fa-solid fa-arrow-right"
                ></i>

            </a>


        </div>



        <?php if ($categories): ?>


            <div class="row g-4">


                <?php foreach ($categories as $category): ?>


                    <div class="col-6 col-md-4 col-lg-3">


                        <a
                            href="destinations.php?category=<?= urlencode(
                                $category['slug']
                            ) ?>"
                            class="home-category-card"
                        >


                            <div class="home-category-icon">

                                <i
                                    class="fa-solid <?= htmlspecialchars(
                                        getHomeCategoryIcon(
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
                                    ? 'place'
                                    : 'places' ?>

                            </span>


                        </a>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="text-center py-5">

                <p class="text-muted">
                    Categories are not available yet.
                </p>

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- ==================================================
     FEATURED DESTINATIONS
================================================== -->

<section class="home-featured-section">

    <div class="container">


        <div class="home-section-heading">


            <div>

                <span class="home-section-label">
                    RECOMMENDED PLACES
                </span>


                <h2>

                    Featured Destinations

                </h2>


                <p>

                    Start exploring some of the
                    highlighted tourist attractions.

                </p>

            </div>


            <a
                href="destinations.php"
                class="home-view-all-link"
            >

                View All Destinations

                <i class="fa-solid fa-arrow-right"></i>

            </a>


        </div>



        <?php if ($featuredDestinations): ?>


            <div class="row g-4">


                <?php foreach (
                    $featuredDestinations
                    as $destination
                ): ?>


                    <?php

                    $imageUrl =
                        destinationImageUrl(
                            $destination['image']
                            ?? null
                        );

                    ?>


                    <div class="col-md-6 col-lg-4">


                        <div class="place-card home-featured-card">


                            <div
                                class="place-image
                                <?= $imageUrl
                                    ? 'has-real-image'
                                    : htmlspecialchars(
                                        getHomeImageClass(
                                            $destination['slug']
                                        )
                                    ) ?>"
                                <?php if ($imageUrl): ?>

                                    style="
                                        background-image:
                                            linear-gradient(
                                                to top,
                                                rgba(7, 31, 42, 0.55),
                                                rgba(7, 31, 42, 0.06)
                                            ),
                                            url('<?= htmlspecialchars(
                                                $imageUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>');
                                    "

                                <?php endif; ?>
                            >


                                <span class="place-category">

                                    <?= htmlspecialchars(
                                        $destination['category_name']
                                        ?? 'Other'
                                    ) ?>

                                </span>


                                <span class="home-featured-badge">

                                    <i class="fa-solid fa-star"></i>

                                    Featured

                                </span>


                            </div>



                            <div class="place-body">


                                <h4>

                                    <?= htmlspecialchars(
                                        $destination['name']
                                    ) ?>

                                </h4>


                                <p>

                                    <?= htmlspecialchars(
                                        $destination['description']
                                    ) ?>

                                </p>



                                <div class="place-info">


                                    <span>

                                        <i
                                            class="fa-solid fa-location-dot"
                                        ></i>


                                        <?php if (
                                            $destination['distance_km']
                                            !== null
                                        ): ?>

                                            Approx.

                                            <?= htmlspecialchars(
                                                $destination['distance_km']
                                            ) ?>

                                            km

                                        <?php else: ?>

                                            <?= htmlspecialchars(
                                                $destination['location']
                                                ?? 'Location'
                                            ) ?>

                                        <?php endif; ?>

                                    </span>



                                    <a
                                        href="destination-details.php?id=<?= urlencode(
                                            $destination['slug']
                                        ) ?>"
                                    >

                                        View Details

                                    </a>


                                </div>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="text-center py-5">

                <p class="text-muted">

                    Featured destinations
                    are not available yet.

                </p>

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- ==================================================
     TRIP PLANNER CTA
================================================== -->

<section class="home-planner-cta-section">

    <div class="container">


        <div class="home-planner-cta">


            <div>


                <span>

                    <i class="fa-solid fa-route"></i>

                    ONE-DAY TRIP PLANNER

                </span>


                <h2>

                    Build your own
                    perfect day trip

                </h2>


                <p>

                    Add the places you like,
                    arrange your visiting order and
                    open the complete route in Google Maps.

                </p>


            </div>



            <a
                href="planner.php"
                class="btn home-cta-light-btn"
            >

                Start Planning

                <i class="fa-solid fa-arrow-right ms-2"></i>

            </a>


        </div>


    </div>

</section>



<!-- ==================================================
     HELPFUL SERVICES
================================================== -->

<section class="home-services-section">

    <div class="container">


        <div class="home-section-heading text-center justify-content-center">


            <div>

                <span class="home-section-label">
                    TRAVEL WITH CONFIDENCE
                </span>


                <h2>

                    Helpful Visitor Tools

                </h2>

            </div>


        </div>



        <div class="row g-4">


            <!-- MAP -->

            <div class="col-md-6 col-lg-4">


                <a
                    href="map.php"
                    class="home-service-card"
                >

                    <div class="home-service-icon">

                        <i
                            class="fa-solid fa-map-location-dot"
                        ></i>

                    </div>


                    <div>

                        <h4>
                            Map Explorer
                        </h4>

                        <p>

                            View destination locations
                            and open Google Maps directions.

                        </p>

                    </div>


                </a>


            </div>



            <!-- NEARBY -->

            <div class="col-md-6 col-lg-4">


                <a
                    href="nearby.php"
                    class="home-service-card"
                >

                    <div class="home-service-icon">

                        <i class="fa-solid fa-building-circle-check"></i>

                    </div>


                    <div>

                        <h4>
                            Nearby Facilities
                        </h4>

                        <p>

                            Find restaurants, hotels,
                            hospitals, ATMs and other services.

                        </p>

                    </div>


                    <span class="home-service-number">

                        <?= $totalFacilities ?>

                    </span>


                </a>


            </div>



            <!-- EMERGENCY -->

            <div class="col-md-6 col-lg-4">


                <a
                    href="emergency.php"
                    class="home-service-card home-emergency-service"
                >

                    <div class="home-service-icon">

                        <i
                            class="fa-solid fa-phone-volume"
                        ></i>

                    </div>


                    <div>

                        <h4>
                            Emergency Information
                        </h4>

                        <p>

                            Quickly access important
                            visitor emergency contacts.

                        </p>

                    </div>


                    <?php if (
                        $totalEmergencyContacts > 0
                    ): ?>

                        <span class="home-service-number">

                            <?= $totalEmergencyContacts ?>

                        </span>

                    <?php endif; ?>


                </a>


            </div>


        </div>


    </div>

</section>



<!-- ==================================================
     FOOTER
================================================== -->

<footer>

    <div class="container">


        <div class="footer-content">


            <div>

                <h5>

                    <i
                        class="fa-solid fa-location-dot me-2"
                    ></i>

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


                <a href="nearby.php">
                    Nearby
                </a>


                <a href="planner.php">
                    My Trip
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



<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<!-- Main JavaScript -->

<script
    src="assets/js/script.js"
></script>



<!-- ==================================================
     HOME SEARCH
================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        const form =
            document.getElementById(
                "homeDestinationSearch"
            );


        const input =
            document.getElementById(
                "homeSearchInput"
            );


        if (!form || !input) {
            return;
        }


        form.addEventListener(
            "submit",
            function (event) {


                event.preventDefault();


                const query =
                    input.value.trim();


                if (query === "") {

                    window.location.href =
                        "destinations.php";

                    return;

                }


                window.location.href =
                    "destinations.php?search="
                    + encodeURIComponent(
                        query
                    );


            }
        );


    }
);

</script>


</body>

</html>