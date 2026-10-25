<?php

require_once __DIR__ . '/config/database.php';


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
        destinations.address,
        destinations.latitude,
        destinations.longitude,

        categories.name AS category_name

    FROM destinations

    LEFT JOIN categories
        ON destinations.category_id = categories.id

    ORDER BY destinations.name ASC
");


$destinations =
    $statement->fetchAll();


/*
|--------------------------------------------------------------------------
| CREATE MAP QUERY
|--------------------------------------------------------------------------
*/

function createMapQuery(
    array $destination
): string {

    /*
     * First priority:
     * Latitude + Longitude
     */

    if (
        $destination['latitude'] !== null
        &&
        $destination['longitude'] !== null
        &&
        is_numeric(
            $destination['latitude']
        )
        &&
        is_numeric(
            $destination['longitude']
        )
    ) {

        return
            $destination['latitude']
            . ','
            . $destination['longitude'];

    }


    /*
     * Second priority:
     * Address
     */

    if (
        !empty(
            $destination['address']
        )
    ) {

        return
            $destination['address'];

    }


    /*
     * Third priority:
     * Location
     */

    if (
        !empty(
            $destination['location']
        )
    ) {

        return
            $destination['location'];

    }


    /*
     * Final fallback:
     * Destination name
     */

    return
        $destination['name'];

}


/*
|--------------------------------------------------------------------------
| FIRST DESTINATION
|--------------------------------------------------------------------------
*/

$firstDestination =
    $destinations[0]
    ?? null;


$firstMapQuery = '';

$firstMapUrl = '';

$firstDirectionsUrl = '';


if ($firstDestination) {

    $firstMapQuery =
        createMapQuery(
            $firstDestination
        );


    $encodedQuery =
        rawurlencode(
            $firstMapQuery
        );


    $firstMapUrl =
        'https://www.google.com/maps?q='
        . $encodedQuery
        . '&output=embed';


    $firstDirectionsUrl =
        'https://www.google.com/maps/dir/?api=1&destination='
        . $encodedQuery;

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
        Map Explorer | Day With Explore
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


        <!-- BRAND -->

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



        <!-- MOBILE MENU -->

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



        <!-- NAVIGATION -->

        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul
                class="navbar-nav ms-auto align-items-lg-center gap-lg-2"
            >


                <!-- HOME -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="index.php"
                    >
                        Home
                    </a>

                </li>



                <!-- DESTINATIONS -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="destinations.php"
                    >
                        Destinations
                    </a>

                </li>



                <!-- CATEGORIES -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="categories.php"
                    >
                        Categories
                    </a>

                </li>



                <!-- MAP -->

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="map.php"
                    >
                        Map
                    </a>

                </li>



                <!-- NEARBY -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="nearby.php"
                    >
                        Nearby
                    </a>

                </li>



                <!-- MY TRIP -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="planner.php"
                    >

                        <i
                            class="fa-solid fa-route me-1"
                        ></i>

                        My Trip

                    </a>

                </li>



                <!-- EMERGENCY -->

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



                <!-- ADMIN -->

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
     PAGE HEADER
================================================== -->

<section class="map-page-header">

    <div class="container">


        <span class="page-badge">

            <i
                class="fa-solid fa-map-location-dot"
            ></i>

            Explore Locations

        </span>



        <h1>

            Find Places on the

            <span>
                Map
            </span>

        </h1>



        <p>

            Select a tourist destination
            to view its location and open
            route directions.

        </p>


    </div>

</section>



<!-- ==================================================
     MAP EXPLORER
================================================== -->

<section class="map-explorer-section">

    <div class="container">


        <?php if ($destinations): ?>


            <div class="row g-4">


                <!-- ==================================================
                     LEFT SIDE
                ================================================== -->

                <div class="col-lg-4">


                    <div class="map-place-panel">


                        <!-- SEARCH -->

                        <div class="map-place-search">


                            <i
                                class="fa-solid fa-magnifying-glass"
                            ></i>


                            <input
                                type="text"
                                id="mapPlaceSearch"
                                placeholder="Search destination..."
                                autocomplete="off"
                            >


                        </div>



                        <!-- DESTINATION LIST -->

                        <div
                            class="map-place-list"
                            id="mapPlaceList"
                        >


                            <?php foreach (
                                $destinations
                                as $index => $place
                            ): ?>


                                <?php

                                $mapQuery =
                                    createMapQuery(
                                        $place
                                    );


                                $directionsUrl =
                                    'https://www.google.com/maps/dir/?api=1&destination='
                                    . rawurlencode(
                                        $mapQuery
                                    );

                                ?>


                                <button
                                    type="button"
                                    class="
                                        map-place-item
                                        <?= $index === 0
                                            ? 'active'
                                            : '' ?>
                                    "

                                    data-name="<?= htmlspecialchars(
                                        $place['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"

                                    data-query="<?= htmlspecialchars(
                                        $mapQuery,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"

                                    data-directions="<?= htmlspecialchars(
                                        $directionsUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"

                                    data-details="destination-details.php?id=<?= urlencode(
                                        $place['slug']
                                    ) ?>"
                                >


                                    <!-- ICON -->

                                    <div class="map-place-icon">

                                        <i
                                            class="fa-solid fa-location-dot"
                                        ></i>

                                    </div>



                                    <!-- INFO -->

                                    <div class="map-place-text">


                                        <strong>

                                            <?= htmlspecialchars(
                                                $place['name']
                                            ) ?>

                                        </strong>



                                        <span>

                                            <?= htmlspecialchars(
                                                $place['category_name']
                                                ?? 'Other'
                                            ) ?>

                                        </span>



                                        <small>

                                            <?= htmlspecialchars(
                                                $place['location']
                                                ?? 'Location not listed'
                                            ) ?>

                                        </small>


                                    </div>



                                    <!-- ARROW -->

                                    <i
                                        class="fa-solid fa-chevron-right map-place-arrow"
                                    ></i>


                                </button>


                            <?php endforeach; ?>


                        </div>



                        <!-- NO RESULT -->

                        <div
                            class="map-no-result d-none"
                            id="mapNoResult"
                        >

                            <i
                                class="fa-solid fa-magnifying-glass"
                            ></i>


                            <strong>

                                No destination found

                            </strong>


                        </div>


                    </div>


                </div>



                <!-- ==================================================
                     RIGHT SIDE MAP
                ================================================== -->

                <div class="col-lg-8">


                    <div class="main-map-card">


                        <!-- TOPBAR -->

                        <div class="main-map-topbar">


                            <div>


                                <span>
                                    SELECTED DESTINATION
                                </span>


                                <h4 id="selectedMapPlace">

                                    <?= htmlspecialchars(
                                        $firstDestination['name']
                                    ) ?>

                                </h4>


                            </div>



                            <!-- ACTIONS -->

                            <div class="map-action-buttons">


                                <!-- DIRECTIONS -->

                                <a
                                    href="<?= htmlspecialchars(
                                        $firstDirectionsUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    id="mainDirectionsBtn"
                                    class="btn map-direction-btn m-0"
                                >

                                    <i
                                        class="fa-solid fa-route me-2"
                                    ></i>

                                    Directions

                                </a>



                                <!-- DETAILS -->

                                <a
                                    href="destination-details.php?id=<?= urlencode(
                                        $firstDestination['slug']
                                    ) ?>"
                                    id="mainDetailsBtn"
                                    class="btn map-details-btn"
                                >

                                    <i
                                        class="fa-solid fa-circle-info me-2"
                                    ></i>

                                    Details

                                </a>


                            </div>


                        </div>



                        <!-- MAP -->

                        <div class="main-map-frame">


                            <iframe
                                id="mainMapFrame"
                                src="<?= htmlspecialchars(
                                    $firstMapUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Tourist destination map"
                            ></iframe>


                        </div>


                    </div>


                </div>


            </div>


        <?php else: ?>


            <!-- ==================================================
                 EMPTY DATABASE
            ================================================== -->

            <div class="text-center py-5">


                <div class="error-icon">

                    <i
                        class="fa-solid fa-map-location-dot"
                    ></i>

                </div>


                <h3>

                    No Destinations Available

                </h3>


                <p class="text-muted">

                    Add tourist destinations
                    before using the map.

                </p>


                <a
                    href="destinations.php"
                    class="btn explore-btn"
                >

                    Explore Destinations

                </a>


            </div>


        <?php endif; ?>


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



<!-- ==================================================
     BOOTSTRAP
================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<!-- ==================================================
     MAP JAVASCRIPT
================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*
        |--------------------------------------------------------------------------
        | GET ELEMENTS
        |--------------------------------------------------------------------------
        */

        const mapItems =
            document.querySelectorAll(
                ".map-place-item"
            );


        const mapFrame =
            document.getElementById(
                "mainMapFrame"
            );


        const placeTitle =
            document.getElementById(
                "selectedMapPlace"
            );


        const directionsButton =
            document.getElementById(
                "mainDirectionsBtn"
            );


        const detailsButton =
            document.getElementById(
                "mainDetailsBtn"
            );


        const searchInput =
            document.getElementById(
                "mapPlaceSearch"
            );


        const noResult =
            document.getElementById(
                "mapNoResult"
            );



        /*
        |--------------------------------------------------------------------------
        | CHANGE SELECTED MAP
        |--------------------------------------------------------------------------
        */

        mapItems.forEach(
            function (item) {


                item.addEventListener(
                    "click",
                    function () {


                        /*
                         * Remove old active state
                         */

                        mapItems.forEach(
                            function (button) {

                                button.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /*
                         * Set selected item active
                         */

                        item.classList.add(
                            "active"
                        );


                        const name =
                            item.dataset.name;


                        const query =
                            item.dataset.query;


                        const directions =
                            item.dataset.directions;


                        const details =
                            item.dataset.details;



                        /*
                         * Build Google map URL
                         */

                        const mapUrl =
                            "https://www.google.com/maps?q="
                            +
                            encodeURIComponent(
                                query
                            )
                            +
                            "&output=embed";


                        /*
                         * Update map
                         */

                        if (mapFrame) {

                            mapFrame.src =
                                mapUrl;

                        }


                        /*
                         * Update title
                         */

                        if (placeTitle) {

                            placeTitle.textContent =
                                name;

                        }


                        /*
                         * Update directions
                         */

                        if (
                            directionsButton
                        ) {

                            directionsButton.href =
                                directions;

                        }


                        /*
                         * Update details
                         */

                        if (
                            detailsButton
                        ) {

                            detailsButton.href =
                                details;

                        }


                    }
                );


            }
        );



        /*
        |--------------------------------------------------------------------------
        | SEARCH DESTINATIONS
        |--------------------------------------------------------------------------
        */

        if (searchInput) {


            searchInput.addEventListener(
                "input",
                function () {


                    const searchValue =
                        searchInput
                            .value
                            .toLowerCase()
                            .trim();


                    let visibleCount = 0;


                    mapItems.forEach(
                        function (item) {


                            const placeName =
                                (
                                    item.dataset.name
                                    || ''
                                )
                                    .toLowerCase()
                                    .trim();


                            if (
                                placeName.includes(
                                    searchValue
                                )
                            ) {

                                item.style.display =
                                    "flex";

                                visibleCount++;

                            } else {

                                item.style.display =
                                    "none";

                            }


                        }
                    );


                    /*
                     * No result message
                     */

                    if (noResult) {


                        if (
                            visibleCount === 0
                        ) {

                            noResult.classList.remove(
                                "d-none"
                            );

                        } else {

                            noResult.classList.add(
                                "d-none"
                            );

                        }


                    }


                }
            );


        }


    }
);

</script>


</body>

</html>