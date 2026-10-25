<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$id) {

    header('Location: admin-facilities.php');
    exit;

}


$statement = $pdo->prepare("
    SELECT *
    FROM nearby_facilities
    WHERE id = :id
    LIMIT 1
");


$statement->execute([
    ':id' => $id
]);


$facility =
    $statement->fetch();


if (!$facility) {

    header('Location: admin-facilities.php');
    exit;

}


$destinations = $pdo
    ->query("
        SELECT id, name
        FROM destinations
        ORDER BY name
    ")
    ->fetchAll();


$facilityTypes = [

    'Restaurant',
    'Cafe',
    'Hotel',
    'Hospital',
    'ATM',
    'Fuel Station',
    'Shopping'

];


$error = '';


$destinationId =
    $facility['destination_id'];

$facilityType =
    $facility['facility_type'];

$name =
    $facility['name'];

$address =
    $facility['address'] ?? '';

$contact =
    $facility['contact'] ?? '';

$distanceKm =
    $facility['distance_km'] ?? '';

$latitude =
    $facility['latitude'] ?? '';

$longitude =
    $facility['longitude'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $destinationId =
        $_POST['destination_id'] ?? '';

    $facilityType =
        trim($_POST['facility_type'] ?? '');

    $name =
        trim($_POST['name'] ?? '');

    $address =
        trim($_POST['address'] ?? '');

    $contact =
        trim($_POST['contact'] ?? '');

    $distanceKm =
        trim($_POST['distance_km'] ?? '');

    $latitude =
        trim($_POST['latitude'] ?? '');

    $longitude =
        trim($_POST['longitude'] ?? '');


    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error =
            'Invalid request.';

    } elseif (
        empty($destinationId) ||
        !ctype_digit((string) $destinationId)
    ) {

        $error =
            'Please select a destination.';

    } elseif (
        !in_array(
            $facilityType,
            $facilityTypes,
            true
        )
    ) {

        $error =
            'Please select a valid facility type.';

    } elseif ($name === '') {

        $error =
            'Facility name is required.';

    } else {


        $updateStatement =
            $pdo->prepare("
                UPDATE nearby_facilities

                SET
                    destination_id = :destination_id,
                    facility_type = :facility_type,
                    name = :name,
                    address = :address,
                    contact = :contact,
                    distance_km = :distance_km,
                    latitude = :latitude,
                    longitude = :longitude

                WHERE id = :id
            ");


        $updateStatement->execute([

            ':destination_id' =>
                (int) $destinationId,

            ':facility_type' =>
                $facilityType,

            ':name' =>
                $name,

            ':address' =>
                $address !== ''
                    ? $address
                    : null,

            ':contact' =>
                $contact !== ''
                    ? $contact
                    : null,

            ':distance_km' =>
                $distanceKm !== ''
                    ? (float) $distanceKm
                    : null,

            ':latitude' =>
                $latitude !== ''
                    ? (float) $latitude
                    : null,

            ':longitude' =>
                $longitude !== ''
                    ? (float) $longitude
                    : null,

            ':id' =>
                $id

        ]);


        header(
            'Location: admin-facilities.php?updated=1'
        );

        exit;

    }

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
        Edit Facility | Day With Explore
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body class="admin-dashboard-body">


<section class="admin-dashboard-section">

    <div class="container">

        <div class="admin-form-wrapper">


            <a
                href="admin-facilities.php"
                class="admin-page-back"
            >
                ← Back to Facilities
            </a>


            <div class="admin-form-card mt-3">

                <span class="admin-section-label">
                    EDIT FACILITY
                </span>

                <h1>
                    <?= htmlspecialchars($facility['name']) ?>
                </h1>


                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <form method="POST">


                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            csrfToken()
                        ) ?>"
                    >


                    <div class="row g-4">


                        <div class="col-md-6">

                            <label class="form-label admin-form-label">
                                Near Destination *
                            </label>

                            <select
                                name="destination_id"
                                class="form-select admin-form-control"
                                required
                            >

                                <?php foreach ($destinations as $destination): ?>

                                    <option
                                        value="<?= (int) $destination['id'] ?>"
                                        <?= (string) $destinationId ===
                                            (string) $destination['id']
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $destination['name']
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label admin-form-label">
                                Facility Type *
                            </label>

                            <select
                                name="facility_type"
                                class="form-select admin-form-control"
                                required
                            >

                                <?php foreach ($facilityTypes as $type): ?>

                                    <option
                                        value="<?= htmlspecialchars($type) ?>"
                                        <?= $facilityType === $type
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars($type) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="col-12">

                            <label class="form-label admin-form-label">
                                Facility Name *
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($name) ?>"
                                required
                            >

                        </div>


                        <div class="col-md-8">

                            <label class="form-label admin-form-label">
                                Address
                            </label>

                            <input
                                type="text"
                                name="address"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($address) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Contact
                            </label>

                            <input
                                type="text"
                                name="contact"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($contact) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Distance (km)
                            </label>

                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                name="distance_km"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($distanceKm) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Latitude
                            </label>

                            <input
                                type="number"
                                step="any"
                                min="-90"
                                max="90"
                                name="latitude"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($latitude) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Longitude
                            </label>

                            <input
                                type="number"
                                step="any"
                                min="-180"
                                max="180"
                                name="longitude"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($longitude) ?>"
                            >

                        </div>


                    </div>


                    <div class="admin-form-actions">

                        <a
                            href="admin-facilities.php"
                            class="btn admin-cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn admin-add-btn"
                        >
                            Update Facility
                        </button>

                    </div>


                </form>

            </div>

        </div>

    </div>

</section>

</body>
</html>