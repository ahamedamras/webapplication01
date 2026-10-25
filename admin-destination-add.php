<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';
require_once __DIR__ . '/config/upload.php';

requireAdmin();


$categories = $pdo
    ->query("
        SELECT id, name
        FROM categories
        ORDER BY name
    ")
    ->fetchAll();


$error = '';

$name = trim($_POST['name'] ?? '');
$categoryId = $_POST['category_id'] ?? '';
$description = trim($_POST['description'] ?? '');
$travelTips = trim($_POST['travel_tips'] ?? '');
$location = trim($_POST['location'] ?? '');
$address = trim($_POST['address'] ?? '');
$distanceKm = trim($_POST['distance_km'] ?? '');
$openingHours = trim($_POST['opening_hours'] ?? '');
$admissionFee = trim($_POST['admission_fee'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$latitude = trim($_POST['latitude'] ?? '');
$longitude = trim($_POST['longitude'] ?? '');

$isFeatured =
    isset($_POST['is_featured'])
        ? 1
        : 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error =
            'Invalid request. Please refresh the page.';

    } elseif ($name === '') {

        $error =
            'Destination name is required.';

    } elseif (
        empty($categoryId) ||
        !ctype_digit((string) $categoryId)
    ) {

        $error =
            'Please select a category.';

    } elseif ($description === '') {

        $error =
            'Description is required.';

    } elseif ($travelTips === '') {

        $error =
            'Travel tips are required.';

    } else {

        $uploadedImage = null;

        try {

            /*
            |--------------------------------------------------------------------------
            | CATEGORY CHECK
            |--------------------------------------------------------------------------
            */

            $categoryCheck =
                $pdo->prepare("
                    SELECT COUNT(*)
                    FROM categories
                    WHERE id = :id
                ");


            $categoryCheck->execute([
                ':id' => (int) $categoryId
            ]);


            if (
                (int) $categoryCheck->fetchColumn()
                === 0
            ) {

                throw new RuntimeException(
                    'Selected category does not exist.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | IMAGE UPLOAD
            |--------------------------------------------------------------------------
            */

            $uploadedImage =
                uploadDestinationImage(
                    $_FILES['image']
                    ?? [
                        'error' =>
                            UPLOAD_ERR_NO_FILE
                    ]
                );


            /*
            |--------------------------------------------------------------------------
            | CREATE SLUG
            |--------------------------------------------------------------------------
            */

            $slug =
                createUniqueSlug(
                    $pdo,
                    $name
                );


            /*
            |--------------------------------------------------------------------------
            | SAVE DESTINATION
            |--------------------------------------------------------------------------
            */

            $statement =
                $pdo->prepare("
                    INSERT INTO destinations
                    (
                        category_id,
                        name,
                        slug,
                        description,
                        travel_tips,
                        location,
                        address,
                        distance_km,
                        opening_hours,
                        admission_fee,
                        contact,
                        image,
                        latitude,
                        longitude,
                        is_featured
                    )
                    VALUES
                    (
                        :category_id,
                        :name,
                        :slug,
                        :description,
                        :travel_tips,
                        :location,
                        :address,
                        :distance_km,
                        :opening_hours,
                        :admission_fee,
                        :contact,
                        :image,
                        :latitude,
                        :longitude,
                        :is_featured
                    )
                ");


            $statement->execute([

                ':category_id' =>
                    (int) $categoryId,

                ':name' => $name,

                ':slug' => $slug,

                ':description' =>
                    $description,

                ':travel_tips' =>
                    $travelTips !== ''
                        ? $travelTips
                        : null,

                ':location' =>
                    $location !== ''
                        ? $location
                        : null,

                ':address' =>
                    $address !== ''
                        ? $address
                        : null,

                ':distance_km' =>
                    $distanceKm !== ''
                        ? (float) $distanceKm
                        : null,

                ':opening_hours' =>
                    $openingHours !== ''
                        ? $openingHours
                        : null,

                ':admission_fee' =>
                    $admissionFee !== ''
                        ? $admissionFee
                        : null,

                ':contact' =>
                    $contact !== ''
                        ? $contact
                        : null,

                ':image' =>
                    $uploadedImage,

                ':latitude' =>
                    $latitude !== ''
                        ? (float) $latitude
                        : null,

                ':longitude' =>
                    $longitude !== ''
                        ? (float) $longitude
                        : null,

                ':is_featured' =>
                    $isFeatured

            ]);


            header(
                'Location: admin-destinations.php?created=1'
            );

            exit;


        } catch (Throwable $e) {

            /*
             * DB save fail aana newly uploaded
             * image remove pannuvom.
             */

            if ($uploadedImage) {
                deleteDestinationImage(
                    $uploadedImage
                );
            }


            $error =
                $e->getMessage();

        }

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
        Add Destination | Day With Explore
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

<body class="admin-dashboard-body">


<nav class="admin-topbar">

    <div class="container-fluid">

        <div class="admin-topbar-inner">

            <a
                href="admin-dashboard.php"
                class="admin-dashboard-brand"
            >

                <i class="fa-solid fa-location-dot"></i>

                Day With Explore

            </a>


            <div class="admin-user-area">

                <strong>
                    <?= htmlspecialchars(
                        $_SESSION['admin_username']
                    ) ?>
                </strong>


                <form
                    action="admin-logout.php"
                    method="POST"
                >

                    <button
                        class="btn admin-logout-btn"
                        type="submit"
                    >
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </div>

</nav>



<section class="admin-dashboard-section">

    <div class="container">

        <div class="admin-form-wrapper">

            <a
                href="admin-destinations.php"
                class="admin-page-back"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Destinations

            </a>


            <div class="admin-form-card mt-3">

                <span class="admin-section-label">
                    NEW DESTINATION
                </span>

                <h1>
                    Add Tourist Place
                </h1>

                <p class="text-muted">
                    Enter accurate tourism information.
                </p>


                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <i class="fa-solid fa-circle-exclamation me-2"></i>

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                            csrfToken()
                        ) ?>"
                    >


                    <div class="row g-4">


                        <div class="col-md-8">

                            <label class="form-label admin-form-label">
                                Destination Name *
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($name) ?>"
                                required
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Category *
                            </label>

                            <select
                                name="category_id"
                                class="form-select admin-form-control"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>

                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= (string) $categoryId ===
                                            (string) $category['id']
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars(
                                            $category['name']
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- IMAGE -->

                        <div class="col-12">

                            <label class="form-label admin-form-label">

                                Destination Image

                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control admin-form-control"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                            <small class="admin-form-help">

                                JPG, JPEG, PNG or WebP.
                                Maximum 5 MB.

                            </small>

                        </div>


                        <div class="col-12">

                            <label class="form-label admin-form-label">
                                Description *
                            </label>

                            <textarea
                                name="description"
                                class="form-control admin-form-control"
                                rows="5"
                                required
                            ><?= htmlspecialchars($description) ?></textarea>

                        </div>


                        <!-- TRAVEL TIPS -->

                        <div class="col-12">

                            <label class="form-label admin-form-label">
                                Travel Tips *
                            </label>

                            <textarea
                                name="travel_tips"
                                class="form-control admin-form-control"
                                rows="4"
                                required
                                placeholder="Example: Visit during the morning or late afternoon, carry drinking water and sun protection."
                            ><?= htmlspecialchars($travelTips) ?></textarea>

                            <small class="admin-form-help">
                                Add practical advice that helps tourists plan a safe and comfortable visit.
                            </small>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label admin-form-label">
                                Location
                            </label>

                            <input
                                type="text"
                                name="location"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($location) ?>"
                            >

                        </div>


                        <div class="col-md-6">

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
                                Opening Hours
                            </label>

                            <input
                                type="text"
                                name="opening_hours"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($openingHours) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label admin-form-label">
                                Admission Fee
                            </label>

                            <input
                                type="text"
                                name="admission_fee"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($admissionFee) ?>"
                            >

                        </div>


                        <div class="col-md-6">

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


                        <div class="col-md-3">

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


                        <div class="col-md-3">

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


                        <div class="col-12">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_featured"
                                    id="isFeatured"
                                    <?= $isFeatured
                                        ? 'checked'
                                        : '' ?>
                                >

                                <label
                                    class="form-check-label"
                                    for="isFeatured"
                                >

                                    Show as Featured Destination

                                </label>

                            </div>

                        </div>

                    </div>


                    <div class="admin-form-actions">

                        <a
                            href="admin-destinations.php"
                            class="btn admin-cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn admin-add-btn"
                        >

                            <i class="fa-solid fa-floppy-disk me-2"></i>

                            Save Destination

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>


</body>
</html>