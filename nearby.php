<?php

require_once __DIR__ . '/config/database.php';


/*
|--------------------------------------------------------------------------
| GET ALL NEARBY FACILITIES
|--------------------------------------------------------------------------
*/

$statement = $pdo->query("
    SELECT

        nearby_facilities.id,
        nearby_facilities.facility_type,
        nearby_facilities.name,
        nearby_facilities.address,
        nearby_facilities.contact,
        nearby_facilities.distance_km,
        nearby_facilities.latitude,
        nearby_facilities.longitude,

        destinations.name AS destination_name,
        destinations.slug AS destination_slug,
        destinations.location AS destination_location

    FROM nearby_facilities

    INNER JOIN destinations
        ON nearby_facilities.destination_id = destinations.id

    ORDER BY
        nearby_facilities.facility_type ASC,
        nearby_facilities.name ASC
");


$facilities = $statement->fetchAll();


/*
|--------------------------------------------------------------------------
| FACILITY ICON
|--------------------------------------------------------------------------
*/

function getFacilityIcon(string $type): string
{

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


    return $icons[$type]
        ?? 'fa-location-dot';

}


/*
|--------------------------------------------------------------------------
| GOOGLE DIRECTIONS URL
|--------------------------------------------------------------------------
*/

function getFacilityDirectionsUrl(
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
            $facility['name']
            . ', '
            . (
                $facility['destination_location']
                ?? ''
            );

    }


    return
        'https://www.google.com/maps/dir/?api=1&destination='
        . rawurlencode($query);

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

<body>


<!-- ==================================================
     NAVBAR
================================================== -->

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
                        class="nav-link"
                        href="destinations.php#categories"
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
                        class="nav-link active"
                        href="nearby.php"
                    >
                        Nearby
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



<!-- ==================================================
     HEADER
================================================== -->

<section class="nearby-page-header">

    <div class="container">

        <span class="page-badge">

            <i class="fa-solid fa-building-circle-check"></i>

            Visitor Services

        </span>


        <h1>

            Find Helpful

            <span>
                Nearby Facilities
            </span>

        </h1>


        <p>

            Discover restaurants, cafes, hotels,
            hospitals, ATMs, fuel stations and
            shopping locations near tourist destinations.

        </p>


        <div class="destination-search nearby-search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="facilitySearch"
                placeholder="Search facility..."
                autocomplete="off"
            >

        </div>

    </div>

</section>



<!-- ==================================================
     FILTERS
================================================== -->

<section class="filter-section">

    <div class="container">

        <div class="filter-buttons">


            <button
                type="button"
                class="facility-filter-btn active"
                data-type="all"
            >
                All
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="restaurant"
            >
                Restaurants
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="cafe"
            >
                Cafes
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="hotel"
            >
                Hotels
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="hospital"
            >
                Hospitals
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="atm"
            >
                ATM
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="fuel station"
            >
                Fuel
            </button>


            <button
                type="button"
                class="facility-filter-btn"
                data-type="shopping"
            >
                Shopping
            </button>


        </div>

    </div>

</section>



<!-- ==================================================
     FACILITIES
================================================== -->

<section class="nearby-public-section">

    <div class="container">


        <?php if ($facilities): ?>


            <div
                class="row g-4"
                id="facilityGrid"
            >


                <?php foreach ($facilities as $facility): ?>


                    <?php

                    $directionsUrl =
                        getFacilityDirectionsUrl(
                            $facility
                        );

                    ?>


                    <div
                        class="col-md-6 col-lg-4 facility-column"
                        data-name="<?= htmlspecialchars(
                            strtolower(
                                $facility['name']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        data-type="<?= htmlspecialchars(
                            strtolower(
                                $facility['facility_type']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >


                        <div class="public-facility-card">


                            <div class="public-facility-top">


                                <div class="public-facility-icon">

                                    <i
                                        class="fa-solid <?= htmlspecialchars(
                                            getFacilityIcon(
                                                $facility['facility_type']
                                            )
                                        ) ?>"
                                    ></i>

                                </div>


                                <span class="public-facility-type">

                                    <?= htmlspecialchars(
                                        $facility['facility_type']
                                    ) ?>

                                </span>


                            </div>



                            <h4>

                                <?= htmlspecialchars(
                                    $facility['name']
                                ) ?>

                            </h4>



                            <div class="facility-near-place">

                                <i class="fa-solid fa-location-dot"></i>

                                Near

                                <a
                                    href="destination-details.php?id=<?= urlencode(
                                        $facility['destination_slug']
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        $facility['destination_name']
                                    ) ?>

                                </a>

                            </div>



                            <?php if (!empty($facility['address'])): ?>

                                <p class="facility-address">

                                    <i class="fa-solid fa-map-pin"></i>

                                    <?= htmlspecialchars(
                                        $facility['address']
                                    ) ?>

                                </p>

                            <?php endif; ?>



                            <div class="facility-meta">


                                <span>

                                    <i class="fa-solid fa-road"></i>

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

                                </span>



                                <span>

                                    <i class="fa-solid fa-phone"></i>

                                    <?= htmlspecialchars(
                                        $facility['contact']
                                        ?? 'Not listed'
                                    ) ?>

                                </span>


                            </div>



                            <div class="facility-actions">


                                <a
                                    href="<?= htmlspecialchars(
                                        $directionsUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn facility-direction-btn"
                                >

                                    <i class="fa-solid fa-route me-2"></i>

                                    Directions

                                </a>


                                <a
                                    href="destination-details.php?id=<?= urlencode(
                                        $facility['destination_slug']
                                    ) ?>"
                                    class="btn facility-place-btn"
                                >

                                    Destination

                                </a>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>



            <div
                class="facility-no-results d-none"
                id="facilityNoResults"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                <h4>
                    No facilities found
                </h4>

                <p>
                    Try another name or facility type.
                </p>

            </div>


        <?php else: ?>


            <div class="facility-no-results">

                <i class="fa-solid fa-building"></i>

                <h4>
                    No Nearby Facilities Available
                </h4>

                <p>

                    Nearby facility information
                    has not been added yet.

                </p>

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

                <a href="map.php">
                    Map
                </a>

                <a href="nearby.php">
                    Nearby
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


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const searchInput =
            document.getElementById(
                "facilitySearch"
            );

        const filterButtons =
            document.querySelectorAll(
                ".facility-filter-btn"
            );

        const facilityColumns =
            document.querySelectorAll(
                ".facility-column"
            );

        const noResults =
            document.getElementById(
                "facilityNoResults"
            );


        let selectedType = "all";


        function filterFacilities()
        {

            const searchValue =
                searchInput
                    ? searchInput.value
                        .toLowerCase()
                        .trim()
                    : "";


            let visibleCount = 0;


            facilityColumns.forEach(
                function (column) {

                    const name =
                        column.dataset.name;

                    const type =
                        column.dataset.type;


                    const matchesName =
                        name.includes(
                            searchValue
                        );


                    const matchesType =
                        selectedType === "all" ||
                        type === selectedType;


                    if (
                        matchesName &&
                        matchesType
                    ) {

                        column.style.display = "";

                        visibleCount++;

                    } else {

                        column.style.display =
                            "none";

                    }

                }
            );


            if (noResults) {

                if (visibleCount === 0) {

                    noResults.classList.remove(
                        "d-none"
                    );

                } else {

                    noResults.classList.add(
                        "d-none"
                    );

                }

            }

        }


        if (searchInput) {

            searchInput.addEventListener(
                "input",
                filterFacilities
            );

        }


        filterButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        filterButtons.forEach(
                            function (btn) {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                        button.classList.add(
                            "active"
                        );


                        selectedType =
                            button.dataset.type;


                        filterFacilities();

                    }
                );

            }
        );

    }
);

</script>


</body>
</html>