<?php

require_once __DIR__ . '/config/database.php';


$statement = $pdo->query("
    SELECT
        id,
        title,
        emergency_type,
        phone,
        area,
        description

    FROM emergency_contacts

    WHERE is_active = 1

    ORDER BY
        display_order ASC,
        title ASC
");


$contacts =
    $statement->fetchAll();



function getEmergencyIcon(
    string $type
): string {

    $icons = [

        'Police' =>
            'fa-shield-halved',

        'Ambulance' =>
            'fa-truck-medical',

        'Hospital' =>
            'fa-hospital',

        'Fire' =>
            'fa-fire-extinguisher',

        'Tourist Support' =>
            'fa-circle-info',

        'Other' =>
            'fa-phone-volume'

    ];


    return $icons[$type]
        ?? 'fa-phone-volume';

}



/*
|--------------------------------------------------------------------------
| TEL LINK
|--------------------------------------------------------------------------
*/

function createPhoneLink(
    string $phone
): string {

    return preg_replace(
        '/[^0-9+]/',
        '',
        $phone
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
        Emergency Information | Day With Explore
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
                        class="nav-link active emergency-nav-link"
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



<!-- HEADER -->

<section class="emergency-page-header">

    <div class="container text-center">


        <span class="emergency-page-badge">

            <i class="fa-solid fa-triangle-exclamation"></i>

            Important Visitor Information

        </span>


        <h1>

            Emergency

            <span>
                Contacts
            </span>

        </h1>


        <p>

            Quickly access important emergency
            services while travelling around
            Eravur and Batticaloa.

        </p>


    </div>

</section>



<!-- IMPORTANT MESSAGE -->

<section class="emergency-warning-section">

    <div class="container">


        <div class="emergency-warning-box">


            <div class="emergency-warning-icon">

                <i class="fa-solid fa-circle-exclamation"></i>

            </div>


            <div>

                <strong>
                    Need immediate help?
                </strong>

                <p>

                    Select the appropriate emergency
                    service below and use the Call Now button.
                    Always confirm your current location
                    when speaking with emergency services.

                </p>

            </div>


        </div>


    </div>

</section>



<!-- CONTACTS -->

<section class="emergency-contacts-section">

    <div class="container">


        <?php if ($contacts): ?>


            <div class="row g-4">


                <?php foreach ($contacts as $contact): ?>


                    <?php

                    $phoneLink =
                        createPhoneLink(
                            $contact['phone']
                        );

                    ?>


                    <div class="col-md-6 col-lg-4">


                        <div class="emergency-public-card">


                            <div class="emergency-card-top">


                                <div class="emergency-public-icon">

                                    <i
                                        class="fa-solid <?= htmlspecialchars(
                                            getEmergencyIcon(
                                                $contact['emergency_type']
                                            )
                                        ) ?>"
                                    ></i>

                                </div>


                                <span class="emergency-type-badge">

                                    <?= htmlspecialchars(
                                        $contact['emergency_type']
                                    ) ?>

                                </span>


                            </div>



                            <h3>

                                <?= htmlspecialchars(
                                    $contact['title']
                                ) ?>

                            </h3>



                            <?php if (!empty($contact['area'])): ?>

                                <div class="emergency-area">

                                    <i class="fa-solid fa-location-dot"></i>

                                    <?= htmlspecialchars(
                                        $contact['area']
                                    ) ?>

                                </div>

                            <?php endif; ?>



                            <?php if (!empty($contact['description'])): ?>

                                <p>

                                    <?= htmlspecialchars(
                                        $contact['description']
                                    ) ?>

                                </p>

                            <?php endif; ?>



                            <div class="emergency-phone-number">

                                <span>
                                    CONTACT NUMBER
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $contact['phone']
                                    ) ?>

                                </strong>

                            </div>



                            <a
                                href="tel:<?= htmlspecialchars(
                                    $phoneLink,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="btn emergency-call-btn"
                            >

                                <i class="fa-solid fa-phone me-2"></i>

                                Call Now

                            </a>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="emergency-empty-state">


                <i class="fa-solid fa-phone-slash"></i>


                <h3>
                    Emergency Information Not Available
                </h3>


                <p>

                    Verified emergency contact
                    information has not been added yet.

                </p>


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

                <a href="map.php">
                    Map
                </a>

                <a href="nearby.php">
                    Nearby
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