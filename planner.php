<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/upload.php';


/*
|--------------------------------------------------------------------------
| GET DESTINATIONS FOR TRIP PLANNER
|--------------------------------------------------------------------------
*/

$statement = $pdo->query("
    SELECT

        destinations.id,
        destinations.name,
        destinations.slug,
        destinations.description,
        destinations.location,
        destinations.address,
        destinations.distance_km,
        destinations.image,
        destinations.latitude,
        destinations.longitude,

        categories.name AS category_name

    FROM destinations

    LEFT JOIN categories
        ON destinations.category_id = categories.id

    ORDER BY destinations.name ASC
");


$destinations = $statement->fetchAll();


/*
|--------------------------------------------------------------------------
| PREPARE SAFE IMAGE URL
|--------------------------------------------------------------------------
*/

foreach ($destinations as &$destination) {

    $destination['image_url'] =
        destinationImageUrl(
            $destination['image'] ?? null
        );

}

unset($destination);


/*
|--------------------------------------------------------------------------
| JSON FOR JAVASCRIPT
|--------------------------------------------------------------------------
*/

$destinationJson = json_encode(
    $destinations,
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
);

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
        My Day Trip | Day With Explore
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

    <style>
        /* ==================================================
           PLANNER TRIP ESTIMATE
        ================================================== */

        .planner-estimate-btn {
            width: 100%;
            margin-bottom: 12px;
            padding: 13px;
            border: 1px solid rgba(22, 138, 173, 0.24);
            border-radius: 12px;
            background: #ffffff;
            color: var(--primary);
            font-weight: 750;
            transition: 0.2s ease;
        }

        .planner-estimate-btn:hover:not(:disabled) {
            background: #f2fafb;
            border-color: var(--primary);
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .planner-estimate-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .trip-estimate-panel {
            margin: 0 0 18px;
            padding: 16px;
            border: 1px solid #dcebed;
            border-radius: 16px;
            background: linear-gradient(135deg, #f8fcfd, #f2f9fa);
        }

        .trip-estimate-panel.d-none {
            display: none !important;
        }

        .trip-estimate-heading {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 12px;
            color: var(--dark);
            font-size: 0.83rem;
            font-weight: 800;
        }

        .trip-estimate-heading i {
            color: var(--primary);
        }

        .trip-estimate-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
        }

        .trip-estimate-stat {
            padding: 12px 10px;
            border: 1px solid #e3edef;
            border-radius: 13px;
            background: #ffffff;
            text-align: center;
        }

        .trip-estimate-stat span {
            display: block;
            margin-bottom: 3px;
            color: #7d8f95;
            font-size: 0.64rem;
            font-weight: 700;
        }

        .trip-estimate-stat strong {
            display: block;
            color: var(--dark);
            font-size: 1rem;
            font-weight: 800;
        }

        .trip-estimate-message {
            margin: 12px 0 0;
            color: #687a80;
            font-size: 0.7rem;
            line-height: 1.55;
        }

        .trip-estimate-message.is-warning {
            color: #8a6d2f;
        }

        .trip-estimate-legs {
            display: grid;
            gap: 8px;
            margin-top: 13px;
        }

        .trip-estimate-leg {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 11px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.85);
        }

        .trip-estimate-leg-route {
            min-width: 0;
        }

        .trip-estimate-leg-route strong {
            display: block;
            color: var(--dark);
            font-size: 0.72rem;
            font-weight: 750;
        }

        .trip-estimate-leg-route span {
            display: block;
            margin-top: 2px;
            color: #829197;
            font-size: 0.63rem;
        }

        .trip-estimate-leg-value {
            flex-shrink: 0;
            color: var(--primary-dark);
            font-size: 0.72rem;
            font-weight: 800;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .trip-estimate-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

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
                        class="nav-link active"
                        href="planner.php"
                    >

                        <i class="fa-solid fa-route me-1"></i>

                        My Trip

                        <span
                            class="trip-nav-count"
                            id="tripNavCount"
                        >
                            0
                        </span>

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="emergency.php"
                    >

                        <i class="fa-solid fa-phone-volume me-1"></i>

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



<!-- ==================================================
     HEADER
================================================== -->

<section class="planner-page-header">

    <div class="container">


        <span class="page-badge">

            <i class="fa-solid fa-route"></i>

            One-Day Planner

        </span>


        <h1>

            Plan Your

            <span>
                Perfect Day Trip
            </span>

        </h1>


        <p>

            Arrange your selected tourist destinations,
            remove unwanted places and open the complete
            multi-stop route in Google Maps.

        </p>


    </div>

</section>



<!-- ==================================================
     PLANNER
================================================== -->

<section class="trip-planner-section">

    <div class="container">


        <div class="row g-4">


            <!-- ================= LEFT ================= -->

            <div class="col-lg-8">


                <div class="trip-planner-card">


                    <div class="trip-planner-title-row">


                        <div>

                            <span class="trip-planner-label">
                                YOUR ITINERARY
                            </span>

                            <h3>
                                Selected Places
                            </h3>

                        </div>


                        <button
                            type="button"
                            class="btn trip-clear-btn"
                            id="clearTripBtn"
                        >

                            <i class="fa-solid fa-trash me-2"></i>

                            Clear Trip

                        </button>


                    </div>



                    <!-- LIST -->

                    <div
                        id="tripPlannerList"
                        class="trip-planner-list"
                    ></div>



                    <!-- EMPTY STATE -->

                    <div
                        id="tripEmptyState"
                        class="trip-empty-state d-none"
                    >

                        <div class="trip-empty-icon">

                            <i class="fa-solid fa-map-location-dot"></i>

                        </div>


                        <h4>
                            Your trip is empty
                        </h4>


                        <p>

                            Explore destinations and add
                            places to build your one-day trip.

                        </p>


                        <a
                            href="destinations.php"
                            class="btn explore-btn"
                        >

                            <i class="fa-solid fa-compass me-2"></i>

                            Explore Destinations

                        </a>


                    </div>


                </div>


            </div>



            <!-- ================= RIGHT ================= -->

            <div class="col-lg-4">


                <div class="trip-summary-card sticky-lg-top">


                    <span class="trip-planner-label">

                        TRIP SUMMARY

                    </span>


                    <h3>
                        Your Day Plan
                    </h3>



                    <div class="trip-summary-stat">


                        <div class="trip-summary-icon">

                            <i class="fa-solid fa-location-dot"></i>

                        </div>


                        <div>

                            <span>
                                Selected Places
                            </span>

                            <strong id="selectedPlaceCount">
                                0
                            </strong>

                        </div>


                    </div>



                    <div class="trip-summary-note">

                        <i class="fa-solid fa-circle-info"></i>

                        <p>

                            Arrange the places in the order
                            you want to visit. Google Maps
                            will use this order for the route.

                        </p>

                    </div>



                    <!-- ==================================================
                         TRIP ESTIMATE
                    ================================================== -->

                    <button
                        type="button"
                        class="btn planner-estimate-btn"
                        id="calculateTripEstimateBtn"
                        disabled
                    >

                        <i class="fa-solid fa-calculator me-2"></i>

                        Calculate Trip Estimate

                    </button>


                    <div
                        class="trip-estimate-panel d-none"
                        id="tripEstimatePanel"
                    >

                        <div class="trip-estimate-heading">

                            <i class="fa-solid fa-chart-simple"></i>

                            Trip Estimate

                        </div>


                        <div class="trip-estimate-grid">

                            <div class="trip-estimate-stat">

                                <span>
                                    Total Distance
                                </span>

                                <strong id="estimateDistance">
                                    0 km
                                </strong>

                            </div>


                            <div class="trip-estimate-stat">

                                <span>
                                    Travel Time
                                </span>

                                <strong id="estimateTime">
                                    0 min
                                </strong>

                            </div>


                            <div class="trip-estimate-stat">

                                <span>
                                    Stops
                                </span>

                                <strong id="estimateStops">
                                    0
                                </strong>

                            </div>

                        </div>


                        <p
                            class="trip-estimate-message"
                            id="tripEstimateMessage"
                        ></p>


                        <div
                            class="trip-estimate-legs"
                            id="tripEstimateLegs"
                        ></div>

                    </div>



                    <button
                        type="button"
                        class="btn planner-route-btn"
                        id="openTripRouteBtn"
                        disabled
                    >

                        <i class="fa-solid fa-route me-2"></i>

                        Open Full Route

                    </button>



                    <a
                        href="destinations.php"
                        class="btn planner-add-more-btn"
                    >

                        <i class="fa-solid fa-plus me-2"></i>

                        Add More Places

                    </a>



                    <small class="planner-route-help">

                        Google Maps opens in a new tab.
                        Exact driving distance and travel
                        time are calculated there.

                    </small>


                </div>


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



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<script>

/*
|--------------------------------------------------------------------------
| DATABASE DESTINATIONS
|--------------------------------------------------------------------------
*/

const plannerDestinations =
    <?= $destinationJson ?: '[]' ?>;


/*
|--------------------------------------------------------------------------
| LOCAL STORAGE
|--------------------------------------------------------------------------
*/

const tripStorageKey =
    "dayWithExploreTrip";


function getSavedTrip()
{

    try {

        const saved =
            localStorage.getItem(
                tripStorageKey
            );


        if (!saved) {
            return [];
        }


        const parsed =
            JSON.parse(saved);


        return Array.isArray(parsed)
            ? parsed
            : [];

    } catch (error) {

        return [];

    }

}


function saveTrip(trip)
{

    localStorage.setItem(
        tripStorageKey,
        JSON.stringify(trip)
    );

}


/*
|--------------------------------------------------------------------------
| FIND DESTINATION FROM DATABASE DATA
|--------------------------------------------------------------------------
*/

function findDestination(slug)
{

    return plannerDestinations.find(
        function (destination) {

            return destination.slug === slug;

        }
    );

}


/*
|--------------------------------------------------------------------------
| MAP QUERY
|--------------------------------------------------------------------------
*/

function getMapQuery(destination)
{

    if (
        destination.latitude !== null &&
        destination.longitude !== null &&
        destination.latitude !== "" &&
        destination.longitude !== ""
    ) {

        return (
            destination.latitude
            + ","
            + destination.longitude
        );

    }


    if (destination.address) {

        return destination.address;

    }


    if (destination.location) {

        return destination.location;

    }


    return destination.name;

}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(value)
{

    const div =
        document.createElement("div");


    div.textContent =
        value ?? "";


    return div.innerHTML;

}


/*
|--------------------------------------------------------------------------
| RENDER TRIP
|--------------------------------------------------------------------------
*/

function renderTripPlanner()
{

    const savedTrip =
        getSavedTrip();


    const list =
        document.getElementById(
            "tripPlannerList"
        );


    const emptyState =
        document.getElementById(
            "tripEmptyState"
        );


    const count =
        document.getElementById(
            "selectedPlaceCount"
        );


    const navCount =
        document.getElementById(
            "tripNavCount"
        );


    const routeButton =
        document.getElementById(
            "openTripRouteBtn"
        );


    const clearButton =
        document.getElementById(
            "clearTripBtn"
        );


    const estimateButton =
        document.getElementById(
            "calculateTripEstimateBtn"
        );


    const estimatePanel =
        document.getElementById(
            "tripEstimatePanel"
        );


    list.innerHTML = "";


    /*
     * Any itinerary change makes an old
     * estimate out of date.
     */

    estimatePanel.classList.add(
        "d-none"
    );


    count.textContent =
        savedTrip.length;


    navCount.textContent =
        savedTrip.length;


    if (savedTrip.length === 0) {

        emptyState.classList.remove(
            "d-none"
        );


        routeButton.disabled = true;

        clearButton.disabled = true;

        estimateButton.disabled = true;

        return;

    }


    emptyState.classList.add(
        "d-none"
    );


    routeButton.disabled = false;

    clearButton.disabled = false;

    estimateButton.disabled = false;



    savedTrip.forEach(
        function (savedPlace, index) {


            const destination =
                findDestination(
                    savedPlace.id
                );


            /*
             * Destination DB-la delete aagirundha
             * skip pannuvom.
             */

            if (!destination) {
                return;
            }



            const item =
                document.createElement(
                    "div"
                );


            item.className =
                "trip-planner-item";



            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            let imageHtml = "";


            if (destination.image_url) {

                imageHtml = `

                    <div
                        class="trip-planner-image"
                        style="
                            background-image:
                            linear-gradient(
                                rgba(8, 37, 49, 0.10),
                                rgba(8, 37, 49, 0.35)
                            ),
                            url('${escapeHtml(
                                destination.image_url
                            )}');
                        "
                    ></div>

                `;

            } else {

                imageHtml = `

                    <div class="trip-planner-image trip-image-fallback">

                        <i class="fa-solid fa-location-dot"></i>

                    </div>

                `;

            }



            item.innerHTML = `

                <div class="trip-order-number">

                    ${index + 1}

                </div>


                ${imageHtml}


                <div class="trip-planner-item-info">


                    <span>

                        ${escapeHtml(
                            destination.category_name
                            || "Other"
                        )}

                    </span>


                    <h4>

                        ${escapeHtml(
                            destination.name
                        )}

                    </h4>


                    <small>

                        <i class="fa-solid fa-location-dot"></i>

                        ${escapeHtml(
                            destination.location
                            || "Location not listed"
                        )}

                    </small>


                    <a
                        href="destination-details.php?id=${encodeURIComponent(
                            destination.slug
                        )}"
                    >

                        View Details

                    </a>


                </div>



                <div class="trip-planner-controls">


                    <button
                        type="button"
                        class="trip-control-btn"
                        onclick="moveTripPlace(
                            ${index},
                            -1
                        )"
                        ${index === 0
                            ? "disabled"
                            : ""}
                        title="Move Up"
                    >

                        <i class="fa-solid fa-arrow-up"></i>

                    </button>


                    <button
                        type="button"
                        class="trip-control-btn"
                        onclick="moveTripPlace(
                            ${index},
                            1
                        )"
                        ${index === savedTrip.length - 1
                            ? "disabled"
                            : ""}
                        title="Move Down"
                    >

                        <i class="fa-solid fa-arrow-down"></i>

                    </button>


                    <button
                        type="button"
                        class="trip-control-btn trip-remove-btn"
                        onclick="removeTripPlace(
                            '${escapeHtml(
                                destination.slug
                            )}'
                        )"
                        title="Remove"
                    >

                        <i class="fa-solid fa-trash"></i>

                    </button>


                </div>

            `;


            list.appendChild(item);

        }
    );

}


/*
|--------------------------------------------------------------------------
| REMOVE
|--------------------------------------------------------------------------
*/

function removeTripPlace(slug)
{

    let trip =
        getSavedTrip();


    trip =
        trip.filter(
            function (place) {

                return place.id !== slug;

            }
        );


    saveTrip(trip);

    renderTripPlanner();

}


/*
|--------------------------------------------------------------------------
| MOVE UP / DOWN
|--------------------------------------------------------------------------
*/

function moveTripPlace(
    index,
    direction
)
{

    const trip =
        getSavedTrip();


    const newIndex =
        index + direction;


    if (
        newIndex < 0 ||
        newIndex >= trip.length
    ) {

        return;

    }


    const temporary =
        trip[index];


    trip[index] =
        trip[newIndex];


    trip[newIndex] =
        temporary;


    saveTrip(trip);

    renderTripPlanner();

}


/*
|--------------------------------------------------------------------------
| CLEAR TRIP
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "clearTripBtn"
    )
    .addEventListener(
        "click",
        function () {


            if (
                !confirm(
                    "Remove all destinations from your trip?"
                )
            ) {

                return;

            }


            localStorage.removeItem(
                tripStorageKey
            );


            renderTripPlanner();

        }
    );


/*
|--------------------------------------------------------------------------
| TRIP ESTIMATE
|--------------------------------------------------------------------------
*/

function calculateDistanceKm(
    latitude1,
    longitude1,
    latitude2,
    longitude2
)
{

    const earthRadiusKm = 6371;

    const toRadians =
        function (degrees) {

            return degrees * Math.PI / 180;

        };


    const lat1 =
        toRadians(latitude1);

    const lat2 =
        toRadians(latitude2);

    const deltaLatitude =
        toRadians(
            latitude2 - latitude1
        );

    const deltaLongitude =
        toRadians(
            longitude2 - longitude1
        );


    const a =
        Math.sin(
            deltaLatitude / 2
        ) ** 2
        +
        Math.cos(lat1)
        *
        Math.cos(lat2)
        *
        Math.sin(
            deltaLongitude / 2
        ) ** 2;


    const c =
        2
        *
        Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1 - a)
        );


    return earthRadiusKm * c;

}


function getEstimatedRoadDistance(
    straightLineDistance
)
{

    const roadFactor = 1.18;

    return straightLineDistance * roadFactor;

}


function getEstimatedTravelMinutes(
    distanceKm
)
{

    const averageDrivingSpeedKmh = 35;


    return Math.round(
        (
            distanceKm
            / averageDrivingSpeedKmh
        )
        * 60
    );

}


function formatTravelTime(
    totalMinutes
)
{

    if (totalMinutes < 60) {

        return totalMinutes + " min";

    }


    const hours =
        Math.floor(
            totalMinutes / 60
        );


    const minutes =
        totalMinutes % 60;


    if (minutes === 0) {

        return hours + " hr";

    }


    return (
        hours
        + " hr "
        + minutes
        + " min"
    );

}


function hasValidCoordinates(
    destination
)
{

    return (
        destination
        &&
        destination.latitude !== null
        &&
        destination.longitude !== null
        &&
        destination.latitude !== ""
        &&
        destination.longitude !== ""
        &&
        Number.isFinite(
            Number(destination.latitude)
        )
        &&
        Number.isFinite(
            Number(destination.longitude)
        )
    );

}


function calculateTripEstimate()
{

    const savedTrip =
        getSavedTrip();


    const routePlaces =
        savedTrip
            .map(
                function (savedPlace) {

                    return findDestination(
                        savedPlace.id
                    );

                }
            )
            .filter(Boolean);


    const panel =
        document.getElementById(
            "tripEstimatePanel"
        );


    const distanceElement =
        document.getElementById(
            "estimateDistance"
        );


    const timeElement =
        document.getElementById(
            "estimateTime"
        );


    const stopsElement =
        document.getElementById(
            "estimateStops"
        );


    const messageElement =
        document.getElementById(
            "tripEstimateMessage"
        );


    const legsElement =
        document.getElementById(
            "tripEstimateLegs"
        );


    panel.classList.remove(
        "d-none"
    );


    legsElement.innerHTML = "";

    messageElement.classList.remove(
        "is-warning"
    );


    stopsElement.textContent =
        routePlaces.length;


    if (routePlaces.length === 0) {

        distanceElement.textContent =
            "0 km";

        timeElement.textContent =
            "0 min";

        messageElement.textContent =
            "Add destinations to your trip before calculating an estimate.";

        messageElement.classList.add(
            "is-warning"
        );

        return;

    }


    if (routePlaces.length === 1) {

        distanceElement.textContent =
            "0 km";

        timeElement.textContent =
            "0 min";

        messageElement.textContent =
            "One place is selected. Add another destination to calculate travel distance between stops.";

        return;

    }


    const placesWithoutCoordinates =
        routePlaces.filter(
            function (destination) {

                return !hasValidCoordinates(
                    destination
                );

            }
        );


    if (
        placesWithoutCoordinates.length
        > 0
    ) {

        distanceElement.textContent =
            "Unavailable";

        timeElement.textContent =
            "Unavailable";

        messageElement.textContent =
            "Add latitude and longitude for: "
            +
            placesWithoutCoordinates
                .map(
                    function (place) {

                        return place.name;

                    }
                )
                .join(", ")
            +
            ".";

        messageElement.classList.add(
            "is-warning"
        );

        return;

    }


    let totalDistanceKm = 0;

    let totalMinutes = 0;


    for (
        let index = 0;
        index < routePlaces.length - 1;
        index++
    ) {

        const fromPlace =
            routePlaces[index];

        const toPlace =
            routePlaces[index + 1];


        const straightLineDistance =
            calculateDistanceKm(
                Number(fromPlace.latitude),
                Number(fromPlace.longitude),
                Number(toPlace.latitude),
                Number(toPlace.longitude)
            );


        const estimatedRoadDistance =
            getEstimatedRoadDistance(
                straightLineDistance
            );


        const legMinutes =
            getEstimatedTravelMinutes(
                estimatedRoadDistance
            );


        totalDistanceKm +=
            estimatedRoadDistance;


        totalMinutes +=
            legMinutes;


        const leg =
            document.createElement(
                "div"
            );


        leg.className =
            "trip-estimate-leg";


        leg.innerHTML = `

            <div class="trip-estimate-leg-route">

                <strong>
                    ${escapeHtml(fromPlace.name)}
                    →
                    ${escapeHtml(toPlace.name)}
                </strong>

                <span>
                    Approx. ${formatTravelTime(legMinutes)}
                </span>

            </div>

            <div class="trip-estimate-leg-value">
                ${estimatedRoadDistance.toFixed(1)} km
            </div>

        `;


        legsElement.appendChild(
            leg
        );

    }


    distanceElement.textContent =
        totalDistanceKm.toFixed(1)
        + " km";


    timeElement.textContent =
        formatTravelTime(
            totalMinutes
        );


    messageElement.textContent =
        "Approximate estimate based on stored coordinates, a local-road adjustment and an average driving speed. Use Open Full Route for the exact Google Maps road route, distance and live travel time.";

}


document
    .getElementById(
        "calculateTripEstimateBtn"
    )
    .addEventListener(
        "click",
        calculateTripEstimate
    );


/*
|--------------------------------------------------------------------------
| GOOGLE MAPS MULTI-STOP ROUTE
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "openTripRouteBtn"
    )
    .addEventListener(
        "click",
        function () {


            const savedTrip =
                getSavedTrip();


            const routePlaces =
                savedTrip
                    .map(
                        function (savedPlace) {

                            return findDestination(
                                savedPlace.id
                            );

                        }
                    )
                    .filter(Boolean);


            if (routePlaces.length === 0) {

                return;

            }



            /*
             * One destination only
             */

            if (routePlaces.length === 1) {


                const destination =
                    encodeURIComponent(
                        getMapQuery(
                            routePlaces[0]
                        )
                    );


                const url =
                    "https://www.google.com/maps/dir/?api=1"
                    + "&destination="
                    + destination;


                window.open(
                    url,
                    "_blank",
                    "noopener"
                );


                return;

            }



            /*
             * Last place = destination
             */

            const lastPlace =
                routePlaces[
                    routePlaces.length - 1
                ];


            const destination =
                encodeURIComponent(
                    getMapQuery(
                        lastPlace
                    )
                );



            /*
             * Previous selected places = waypoints
             */

            const waypointPlaces =
                routePlaces.slice(
                    0,
                    -1
                );


            const waypoints =
                waypointPlaces
                    .map(
                        function (place) {

                            return getMapQuery(
                                place
                            );

                        }
                    )
                    .join("|");


            const url =
                "https://www.google.com/maps/dir/?api=1"
                + "&destination="
                + destination
                + "&waypoints="
                + encodeURIComponent(
                    waypoints
                );


            window.open(
                url,
                "_blank",
                "noopener"
            );

        }
    );


/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

renderTripPlanner();

</script>


</body>

</html>