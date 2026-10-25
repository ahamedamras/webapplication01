<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/upload.php';


/*
|--------------------------------------------------------------------------
| GET ALL DESTINATIONS
|--------------------------------------------------------------------------
*/

$sql = "
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

    ORDER BY destinations.name ASC
";


$statement = $pdo->query($sql);

$destinations = $statement->fetchAll();


/*
|--------------------------------------------------------------------------
| GET ALL CATEGORIES
|--------------------------------------------------------------------------
*/

$categoryStatement = $pdo->query("
    SELECT
        id,
        name,
        slug

    FROM categories

    ORDER BY name ASC
");


$categories = $categoryStatement->fetchAll();


/*
|--------------------------------------------------------------------------
| FALLBACK IMAGE CLASS
|--------------------------------------------------------------------------
*/

function getImageClass(string $slug): string
{

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


    return $classes[$slug]
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
        Destinations | Day With Explore
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


        <!-- LOGO -->

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



<!-- ==================================================
     PAGE HEADER
================================================== -->

<section class="destination-page-header">

    <div class="container">

        <div class="destination-header-content">


            <span class="page-badge">

                <i class="fa-solid fa-compass"></i>

                Explore the East

            </span>



            <h1>

                Discover Amazing

                <span>
                    Destinations
                </span>

            </h1>



            <p>

                Explore beaches, historical landmarks,
                cultural attractions and popular places
                around Eravur and Batticaloa.

            </p>



            <!-- SEARCH -->

            <div class="destination-search">

                <i
                    class="fa-solid fa-magnifying-glass"
                ></i>


                <input
                    type="text"
                    id="destinationSearch"
                    placeholder="Search destination..."
                    autocomplete="off"
                >

            </div>


        </div>

    </div>

</section>



<!-- ==================================================
     DYNAMIC CATEGORY FILTERS
================================================== -->

<section
    class="filter-section"
    id="categories"
>

    <div class="container">

        <div class="filter-buttons">


            <!-- ALL -->

            <button
                type="button"
                class="filter-btn active"
                data-category="all"
            >

                All

            </button>



            <!-- DATABASE CATEGORIES -->

            <?php foreach ($categories as $category): ?>

                <button
                    type="button"
                    class="filter-btn"
                    data-category="<?= htmlspecialchars(
                        $category['slug'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                    <?= htmlspecialchars(
                        $category['name']
                    ) ?>

                </button>

            <?php endforeach; ?>


        </div>

    </div>

</section>



<!-- ==================================================
     DESTINATIONS
================================================== -->

<section class="destinations-page-section">

    <div class="container">


        <?php if (count($destinations) > 0): ?>


            <div
                class="row g-4"
                id="destinationGrid"
            >


                <?php foreach ($destinations as $destination): ?>


                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | GET REAL IMAGE
                    |--------------------------------------------------------------------------
                    */

                    $imageUrl =
                        destinationImageUrl(
                            $destination['image']
                            ?? null
                        );

                    ?>


                    <div
                        class="col-md-6 col-lg-4 destination-column"
                        data-category="<?= htmlspecialchars(
                            $destination['category_slug']
                            ?? 'other',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >


                        <div class="place-card">


                            <!-- ==================================================
                                 IMAGE
                            ================================================== -->

                            <div
                                class="place-image
                                <?= $imageUrl
                                    ? 'has-real-image'
                                    : htmlspecialchars(
                                        getImageClass(
                                            $destination['slug']
                                        )
                                    ) ?>"
                                <?php if ($imageUrl): ?>

                                    style="
                                        background-image:
                                            linear-gradient(
                                                to top,
                                                rgba(8, 37, 49, 0.45),
                                                rgba(8, 37, 49, 0.05)
                                            ),
                                            url('<?= htmlspecialchars(
                                                $imageUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>');
                                    "

                                <?php endif; ?>
                            >


                                <!-- CATEGORY BADGE -->

                                <span class="place-category">

                                    <?= htmlspecialchars(
                                        $destination['category_name']
                                        ?? 'Other'
                                    ) ?>

                                </span>


                            </div>



                            <!-- ==================================================
                                 CONTENT
                            ================================================== -->

                            <div class="place-body">


                                <!-- NAME -->

                                <h4>

                                    <?= htmlspecialchars(
                                        $destination['name']
                                    ) ?>

                                </h4>



                                <!-- DESCRIPTION -->

                                <p>

                                    <?= htmlspecialchars(
                                        $destination['description']
                                    ) ?>

                                </p>



                                <!-- INFORMATION -->

                                <div class="place-info">


                                    <!-- DISTANCE -->

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


                                            Distance not listed


                                        <?php endif; ?>


                                    </span>



                                    <!-- VIEW DETAILS -->

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



            <!-- ==================================================
                 SEARCH / FILTER NO RESULTS
            ================================================== -->

            <div
                class="destination-no-results d-none"
                id="destinationNoResults"
            >


                <div class="error-icon">

                    <i
                        class="fa-solid fa-magnifying-glass"
                    ></i>

                </div>


                <h3>

                    No Destinations Found

                </h3>


                <p>

                    Try another destination name
                    or select a different category.

                </p>


            </div>


        <?php else: ?>


            <!-- ==================================================
                 DATABASE EMPTY
            ================================================== -->

            <div class="text-center py-5">


                <div
                    class="error-icon"
                    style="margin-bottom:20px;"
                >

                    <i
                        class="fa-solid fa-location-dot"
                    ></i>

                </div>


                <h3 class="fw-bold">

                    No Destinations Available

                </h3>


                <p class="text-muted">

                    Tourist destination information
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
     MAIN JAVASCRIPT
================================================== -->

<script
    src="assets/js/script.js"
></script>



<!-- ==================================================
     DESTINATION SEARCH + CATEGORY FILTER
================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById(
                "destinationSearch"
            );


        const filterButtons =
            document.querySelectorAll(
                ".filter-btn"
            );


        const destinationColumns =
            document.querySelectorAll(
                ".destination-column"
            );


        const noResults =
            document.getElementById(
                "destinationNoResults"
            );


        let selectedCategory =
            "all";


        /*
        |--------------------------------------------------------------------------
        | FILTER DESTINATIONS
        |--------------------------------------------------------------------------
        */

        function filterDestinations()
        {

            const searchValue =
                searchInput
                    ? searchInput.value
                        .toLowerCase()
                        .trim()
                    : "";


            let visibleCount = 0;


            destinationColumns.forEach(
                function (column) {


                    /*
                    |--------------------------------------------------------------------------
                    | DESTINATION NAME
                    |--------------------------------------------------------------------------
                    */

                    const titleElement =
                        column.querySelector(
                            ".place-body h4"
                        );


                    const destinationName =
                        titleElement
                            ? titleElement.textContent
                                .toLowerCase()
                                .trim()
                            : "";


                    /*
                    |--------------------------------------------------------------------------
                    | DESTINATION CATEGORY
                    |--------------------------------------------------------------------------
                    */

                    const destinationCategory =
                        (
                            column.dataset.category
                            || "other"
                        )
                            .toLowerCase()
                            .trim();


                    /*
                    |--------------------------------------------------------------------------
                    | MATCH SEARCH
                    |--------------------------------------------------------------------------
                    |
                    | Search only title.
                    |
                    | Example:
                    | Search "Pasikudah"
                    | should NOT show Elephant Rock
                    | just because its description contains Pasikudah.
                    |
                    */

                    const matchesSearch =
                        destinationName.includes(
                            searchValue
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | MATCH CATEGORY
                    |--------------------------------------------------------------------------
                    */

                    const matchesCategory =
                        selectedCategory === "all"
                        ||
                        destinationCategory ===
                        selectedCategory;


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW / HIDE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        matchesSearch
                        &&
                        matchesCategory
                    ) {

                        column.style.display =
                            "";

                        visibleCount++;

                    } else {

                        column.style.display =
                            "none";

                    }


                }
            );


            /*
            |--------------------------------------------------------------------------
            | NO RESULT MESSAGE
            |--------------------------------------------------------------------------
            */

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



        /*
        |--------------------------------------------------------------------------
        | LIVE SEARCH
        |--------------------------------------------------------------------------
        */

        if (searchInput) {

            searchInput.addEventListener(
                "input",
                filterDestinations
            );

        }



        /*
        |--------------------------------------------------------------------------
        | CATEGORY BUTTON
        |--------------------------------------------------------------------------
        */

        filterButtons.forEach(
            function (button) {


                button.addEventListener(
                    "click",
                    function () {


                        /*
                         * Remove active from all buttons.
                         */

                        filterButtons.forEach(
                            function (btn) {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /*
                         * Active selected button.
                         */

                        button.classList.add(
                            "active"
                        );


                        selectedCategory =
                            (
                                button.dataset.category
                                || "all"
                            )
                                .toLowerCase()
                                .trim();


                        /*
                         * Apply search + category together.
                         */

                        filterDestinations();


                    }
                );


            }
        );



        /*
        |--------------------------------------------------------------------------
        | URL PARAMETERS
        |--------------------------------------------------------------------------
        |
        | Supports:
        |
        | destinations.php?category=beach
        |
        | destinations.php?search=Pasikudah
        |
        | destinations.php?category=beach&search=Pasikudah
        |
        */

        const parameters =
            new URLSearchParams(
                window.location.search
            );


        const requestedCategory =
            parameters.get(
                "category"
            );


        const requestedSearch =
            parameters.get(
                "search"
            );



        /*
        |--------------------------------------------------------------------------
        | HOME SEARCH VALUE
        |--------------------------------------------------------------------------
        */

        if (
            requestedSearch
            &&
            searchInput
        ) {

            searchInput.value =
                requestedSearch;

        }



        /*
        |--------------------------------------------------------------------------
        | CATEGORY FROM URL
        |--------------------------------------------------------------------------
        */

        if (requestedCategory) {


            const normalizedRequestedCategory =
                requestedCategory
                    .toLowerCase()
                    .trim();


            const matchingButton =
                Array.from(
                    filterButtons
                ).find(
                    function (button) {


                        return (
                            (
                                button.dataset.category
                                || ""
                            )
                                .toLowerCase()
                                .trim()
                            ===
                            normalizedRequestedCategory
                        );


                    }
                );


            if (matchingButton) {

                /*
                 * Clicking automatically:
                 *
                 * 1. Activates category button.
                 * 2. Updates selectedCategory.
                 * 3. Runs filterDestinations().
                 */

                matchingButton.click();

            } else {

                /*
                 * Invalid category URL.
                 * Still apply search normally.
                 */

                filterDestinations();

            }


        } else {


            /*
             * No category URL.
             * Apply search if available.
             */

            filterDestinations();


        }


    }
);

</script>


</body>

</html>