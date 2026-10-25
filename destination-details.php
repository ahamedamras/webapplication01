<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/upload.php';


/*
|--------------------------------------------------------------------------
| GET DESTINATION SLUG
|--------------------------------------------------------------------------
*/

$slug = trim($_GET['id'] ?? '');

$destination = null;


/*
|--------------------------------------------------------------------------
| GET DESTINATION FROM DATABASE
|--------------------------------------------------------------------------
*/

if ($slug !== '') {

    $statement = $pdo->prepare("
        SELECT

            destinations.id,
            destinations.name,
            destinations.slug,
            destinations.description,
            destinations.travel_tips,
            destinations.location,
            destinations.address,
            destinations.distance_km,
            destinations.opening_hours,
            destinations.admission_fee,
            destinations.contact,
            destinations.image,
            destinations.latitude,
            destinations.longitude,
            destinations.is_featured,

            categories.name AS category_name,
            categories.slug AS category_slug

        FROM destinations

        LEFT JOIN categories
            ON destinations.category_id = categories.id

        WHERE destinations.slug = :slug

        LIMIT 1
    ");


    $statement->execute([
        ':slug' => $slug
    ]);


    $destination = $statement->fetch();

}


/*
|--------------------------------------------------------------------------
| DESTINATION IMAGE
|--------------------------------------------------------------------------
*/

$destinationImageUrl =
    $destination
        ? destinationImageUrl(
            $destination['image'] ?? null
        )
        : null;


/*
|--------------------------------------------------------------------------
| GOOGLE MAP DATA
|--------------------------------------------------------------------------
*/

$mapQuery = '';

$mapEmbedUrl = '';

$directionsUrl = '';


if ($destination) {


    /*
     * 1. Latitude + Longitude
     */

    if (
        $destination['latitude'] !== null &&
        $destination['longitude'] !== null &&
        is_numeric($destination['latitude']) &&
        is_numeric($destination['longitude'])
    ) {

        $mapQuery =
            $destination['latitude']
            . ','
            . $destination['longitude'];


    /*
     * 2. Address
     */

    } elseif (!empty($destination['address'])) {

        $mapQuery =
            $destination['address'];


    /*
     * 3. Location
     */

    } elseif (!empty($destination['location'])) {

        $mapQuery =
            $destination['location'];

    }


    if ($mapQuery !== '') {

        $encodedMapQuery =
            rawurlencode(
                $mapQuery
            );


        $mapEmbedUrl =
            'https://www.google.com/maps?q='
            . $encodedMapQuery
            . '&output=embed';


        $directionsUrl =
            'https://www.google.com/maps/dir/?api=1&destination='
            . $encodedMapQuery;

    }

}


/*
|--------------------------------------------------------------------------
| GET NEARBY FACILITIES
|--------------------------------------------------------------------------
*/

$nearbyFacilities = [];


if ($destination) {

    $facilityStatement =
        $pdo->prepare("
            SELECT

                id,
                facility_type,
                name,
                address,
                contact,
                distance_km,
                latitude,
                longitude

            FROM nearby_facilities

            WHERE destination_id = :destination_id

            ORDER BY
                distance_km IS NULL,
                distance_km ASC,
                name ASC
        ");


    $facilityStatement->execute([

        ':destination_id' =>
            $destination['id']

    ]);


    $nearbyFacilities =
        $facilityStatement->fetchAll();

}


/*
|--------------------------------------------------------------------------
| FALLBACK DESTINATION IMAGE CLASS
|--------------------------------------------------------------------------
*/

function getDestinationImageClass(
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


/*
|--------------------------------------------------------------------------
| FACILITY ICON
|--------------------------------------------------------------------------
*/

function getNearbyFacilityIcon(
    string $type
): string {

    $icons = [

        'Restaurant' =>
            'fa-utensils',

        'Cafe' =>
            'fa-mug-hot',

        'Hotel' =>
            'fa-hotel',

        'Hospital' =>
            'fa-hospital',

        'ATM' =>
            'fa-building-columns',

        'Fuel Station' =>
            'fa-gas-pump',

        'Shopping' =>
            'fa-bag-shopping'

    ];


    return
        $icons[$type]
        ?? 'fa-location-dot';

}


/*
|--------------------------------------------------------------------------
| FACILITY DIRECTIONS
|--------------------------------------------------------------------------
*/

function getNearbyFacilityDirections(
    array $facility
): string {

    if (
        $facility['latitude'] !== null &&
        $facility['longitude'] !== null &&
        is_numeric($facility['latitude']) &&
        is_numeric($facility['longitude'])
    ) {

        $query =
            $facility['latitude']
            . ','
            . $facility['longitude'];

    } elseif (!empty($facility['address'])) {

        $query =
            $facility['address'];

    } else {

        $query =
            $facility['name'];

    }


    return
        'https://www.google.com/maps/dir/?api=1&destination='
        . rawurlencode(
            $query
        );

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

        <?php if ($destination): ?>

            <?= htmlspecialchars(
                $destination['name']
            ) ?>

            | Day With Explore

        <?php else: ?>

            Destination Not Found
            | Day With Explore

        <?php endif; ?>

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



        <!-- MOBILE BUTTON -->

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



        <!-- NAV LINKS -->

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
                        class="nav-link active"
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
                        class="nav-link"
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



<?php if (!$destination): ?>


<!-- ==================================================
     DESTINATION NOT FOUND
================================================== -->

<section class="destination-error">

    <div class="container text-center">


        <div class="error-icon">

            <i
                class="fa-solid fa-location-question"
            ></i>

        </div>


        <h1>

            Destination Not Found

        </h1>


        <p>

            The destination you requested
            is not available in our database.

        </p>


        <a
            href="destinations.php"
            class="btn explore-btn"
        >

            <i
                class="fa-solid fa-arrow-left me-2"
            ></i>

            Back to Destinations

        </a>


    </div>

</section>


<?php else: ?>


<!-- ==================================================
     BREADCRUMB
================================================== -->

<section class="details-breadcrumb">

    <div class="container">


        <a href="index.php">
            Home
        </a>


        <i
            class="fa-solid fa-chevron-right"
        ></i>


        <a href="destinations.php">
            Destinations
        </a>


        <i
            class="fa-solid fa-chevron-right"
        ></i>


        <span>

            <?= htmlspecialchars(
                $destination['name']
            ) ?>

        </span>


    </div>

</section>



<!-- ==================================================
     DESTINATION DETAILS
================================================== -->

<section class="destination-details-section">

    <div class="container">


        <!-- ==================================================
             IMAGE AREA
        ================================================== -->

        <div
            class="details-image
            <?= $destinationImageUrl
                ? 'has-real-image'
                : htmlspecialchars(
                    getDestinationImageClass(
                        $destination['slug']
                    )
                ) ?>"
            <?php if ($destinationImageUrl): ?>

                style="
                    background-image:
                        linear-gradient(
                            to top,
                            rgba(7, 31, 42, 0.55),
                            rgba(7, 31, 42, 0.08)
                        ),
                        url('<?= htmlspecialchars(
                            $destinationImageUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>');
                "

            <?php endif; ?>
        >


            <span class="details-category">

                <?= htmlspecialchars(
                    $destination['category_name']
                    ?? 'Other'
                ) ?>

            </span>



            <div class="details-image-center">

                <i
                    class="fa-solid fa-location-dot"
                ></i>


                <span>

                    <?= htmlspecialchars(
                        $destination['name']
                    ) ?>

                </span>

            </div>


        </div>



        <div class="row g-5 mt-1">


            <!-- ==================================================
                 LEFT CONTENT
            ================================================== -->

            <div class="col-lg-8">


                <!-- ==================================================
                     TITLE
                ================================================== -->

                <div class="destination-title-area">


                    <span class="details-location">

                        <i
                            class="fa-solid fa-location-dot"
                        ></i>


                        <?= htmlspecialchars(
                            !empty($destination['location'])
                                ? $destination['location']
                                : 'Location not listed'
                        ) ?>

                    </span>



                    <h1>

                        <?= htmlspecialchars(
                            $destination['name']
                        ) ?>

                    </h1>



                    <span class="distance-badge">

                        <i
                            class="fa-solid fa-road"
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

                            Distance not listed

                        <?php endif; ?>

                    </span>


                </div>



                <!-- ==================================================
                     ABOUT
                ================================================== -->

                <div class="details-content-block">


                    <h3>

                        <i
                            class="fa-solid fa-circle-info"
                        ></i>

                        About this destination

                    </h3>


                    <p>

                        <?= htmlspecialchars(
                            $destination['description']
                        ) ?>

                    </p>


                </div>



                <!-- ==================================================
                     TRAVEL TIPS
                ================================================== -->

                <div class="details-content-block">


                    <h3>

                        <i
                            class="fa-solid fa-lightbulb"
                        ></i>

                        Travel Tips

                    </h3>


                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                !empty(
                                    $destination['travel_tips']
                                )
                                    ? $destination['travel_tips']
                                    : 'Travel tips are not listed for this destination yet.'
                            )
                        ) ?>

                    </p>


                </div>



                <!-- ==================================================
                     VISITOR INFORMATION
                ================================================== -->

                <div class="details-content-block">


                    <h3>

                        <i
                            class="fa-regular fa-clock"
                        ></i>

                        Visitor Information

                    </h3>



                    <div class="visitor-info-grid">


                        <!-- OPENING HOURS -->

                        <div class="visitor-info-card">


                            <div class="visitor-icon">

                                <i
                                    class="fa-regular fa-clock"
                                ></i>

                            </div>


                            <div>

                                <span>
                                    Opening Hours
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        !empty(
                                            $destination['opening_hours']
                                        )
                                            ? $destination['opening_hours']
                                            : 'Not listed'
                                    ) ?>

                                </strong>

                            </div>


                        </div>



                        <!-- ADMISSION FEE -->

                        <div class="visitor-info-card">


                            <div class="visitor-icon">

                                <i
                                    class="fa-solid fa-ticket"
                                ></i>

                            </div>


                            <div>

                                <span>
                                    Admission Fee
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        !empty(
                                            $destination['admission_fee']
                                        )
                                            ? $destination['admission_fee']
                                            : 'Not listed'
                                    ) ?>

                                </strong>

                            </div>


                        </div>



                        <!-- CONTACT -->

                        <div class="visitor-info-card">


                            <div class="visitor-icon">

                                <i
                                    class="fa-solid fa-phone"
                                ></i>

                            </div>


                            <div>

                                <span>
                                    Contact
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        !empty(
                                            $destination['contact']
                                        )
                                            ? $destination['contact']
                                            : 'Not listed'
                                    ) ?>

                                </strong>

                            </div>


                        </div>



                        <!-- LOCATION -->

                        <div class="visitor-info-card">


                            <div class="visitor-icon">

                                <i
                                    class="fa-solid fa-location-dot"
                                ></i>

                            </div>


                            <div>

                                <span>
                                    Location
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        !empty(
                                            $destination['location']
                                        )
                                            ? $destination['location']
                                            : 'Not listed'
                                    ) ?>

                                </strong>

                            </div>


                        </div>


                    </div>


                </div>



                <!-- ==================================================
                     ADDRESS
                ================================================== -->

                <div class="details-content-block">


                    <h3>

                        <i
                            class="fa-solid fa-map-pin"
                        ></i>

                        Address

                    </h3>


                    <p>

                        <?= htmlspecialchars(
                            !empty(
                                $destination['address']
                            )
                                ? $destination['address']
                                : 'Address not listed'
                        ) ?>

                    </p>


                </div>



                <!-- ==================================================
                     GOOGLE MAP
                ================================================== -->

                <div class="details-content-block">


                    <h3>

                        <i
                            class="fa-solid fa-map-location-dot"
                        ></i>

                        Map Location

                    </h3>



                    <?php if ($mapEmbedUrl): ?>


                        <div class="destination-map-wrapper">


                            <iframe
                                src="<?= htmlspecialchars(
                                    $mapEmbedUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                width="100%"
                                height="400"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="<?= htmlspecialchars(
                                    $destination['name']
                                ) ?> Map"
                            ></iframe>


                        </div>



                        <a
                            href="<?= htmlspecialchars(
                                $directionsUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn map-direction-btn"
                        >

                            <i
                                class="fa-solid fa-route me-2"
                            ></i>

                            Open Directions

                        </a>


                    <?php else: ?>


                        <div class="alert alert-warning">

                            <i
                                class="fa-solid fa-triangle-exclamation me-2"
                            ></i>

                            Map location is not available
                            for this destination.

                        </div>


                    <?php endif; ?>


                </div>



                <!-- ==================================================
                     NEARBY FACILITIES
                ================================================== -->

                <div class="details-content-block">


                    <div class="section-title-row">


                        <div>


                            <h3>

                                <i
                                    class="fa-solid fa-building-circle-check"
                                ></i>

                                Nearby Facilities

                            </h3>


                            <p>

                                Helpful facilities located near

                                <?= htmlspecialchars(
                                    $destination['name']
                                ) ?>.

                            </p>


                        </div>



                        <a
                            href="nearby.php"
                            class="details-view-all-link"
                        >

                            View All

                            <i
                                class="fa-solid fa-arrow-right ms-1"
                            ></i>

                        </a>


                    </div>



                    <?php if ($nearbyFacilities): ?>


                        <div class="destination-nearby-list">


                            <?php foreach (
                                $nearbyFacilities
                                as $facility
                            ): ?>


                                <?php

                                $facilityIcon =
                                    getNearbyFacilityIcon(
                                        $facility['facility_type']
                                    );


                                $facilityDirections =
                                    getNearbyFacilityDirections(
                                        $facility
                                    );

                                ?>


                                <div class="destination-nearby-card">


                                    <!-- ICON -->

                                    <div class="destination-nearby-icon">

                                        <i
                                            class="fa-solid <?= htmlspecialchars(
                                                $facilityIcon
                                            ) ?>"
                                        ></i>

                                    </div>



                                    <!-- INFO -->

                                    <div class="destination-nearby-info">


                                        <span>

                                            <?= htmlspecialchars(
                                                $facility['facility_type']
                                            ) ?>

                                        </span>


                                        <strong>

                                            <?= htmlspecialchars(
                                                $facility['name']
                                            ) ?>

                                        </strong>


                                        <small>


                                            <?php if (
                                                $facility['distance_km']
                                                !== null
                                            ): ?>

                                                <?= htmlspecialchars(
                                                    $facility['distance_km']
                                                ) ?>

                                                km away

                                            <?php else: ?>

                                                Distance not listed

                                            <?php endif; ?>


                                            <?php if (
                                                !empty(
                                                    $facility['contact']
                                                )
                                            ): ?>

                                                •

                                                <?= htmlspecialchars(
                                                    $facility['contact']
                                                ) ?>

                                            <?php endif; ?>


                                        </small>


                                    </div>



                                    <!-- DIRECTIONS -->

                                    <a
                                        href="<?= htmlspecialchars(
                                            $facilityDirections,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="destination-nearby-direction"
                                        title="Directions"
                                    >

                                        <i
                                            class="fa-solid fa-route"
                                        ></i>

                                    </a>


                                </div>


                            <?php endforeach; ?>


                        </div>


                    <?php else: ?>


                        <div class="destination-no-facilities">


                            <i
                                class="fa-solid fa-building"
                            ></i>


                            <div>

                                <strong>

                                    No nearby facilities added yet

                                </strong>


                                <span>

                                    Facility information for this
                                    destination is not available.

                                </span>

                            </div>


                        </div>


                    <?php endif; ?>


                </div>


            </div>



            <!-- ==================================================
                 RIGHT SIDE
            ================================================== -->

            <div class="col-lg-4">


                <!-- ==================================================
                     TRIP ACTION CARD
                ================================================== -->

                <div
                    class="trip-action-card sticky-lg-top"
                >


                    <span class="trip-small-title">

                        DAY WITH EXPLORE

                    </span>


                    <h3>

                        Add this place
                        to your day trip

                    </h3>


                    <p>

                        Save this destination to your
                        selected trip places.

                    </p>



                    <!-- ==================================================
                         ADD TO TRIP
                    ================================================== -->

                    <button
                        type="button"
                        class="btn add-trip-btn"
                        id="addToTripBtn"

                        data-id="<?= htmlspecialchars(
                            $destination['slug'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"

                        data-name="<?= htmlspecialchars(
                            $destination['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                        <i
                            class="fa-solid fa-plus me-2"
                        ></i>

                        Add to My Trip

                    </button>



                    <!-- ==================================================
                         VIEW TRIP
                    ================================================== -->

                    <a
                        href="planner.php"
                        class="btn planner-view-trip-btn"
                    >

                        <i
                            class="fa-solid fa-map-location-dot me-2"
                        ></i>

                        View My Trip

                    </a>



                    <!-- ==================================================
                         GET DIRECTIONS
                    ================================================== -->

                    <?php if ($directionsUrl): ?>


                        <a
                            href="<?= htmlspecialchars(
                                $directionsUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn route-btn"
                        >

                            <i
                                class="fa-solid fa-route me-2"
                            ></i>

                            Get Directions

                        </a>


                    <?php else: ?>


                        <button
                            type="button"
                            class="btn route-btn"
                            disabled
                        >

                            <i
                                class="fa-solid fa-route me-2"
                            ></i>

                            Directions Unavailable

                        </button>


                    <?php endif; ?>



                    <small>

                        <i
                            class="fa-solid fa-circle-info me-1"
                        ></i>

                        Directions will open in Google Maps.

                    </small>


</section>


<?php endif; ?>



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
     CUSTOM JS
================================================== -->

<script
    src="assets/js/script.js"
></script>


</body>

</html>