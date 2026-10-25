<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$types = [

    'Police',
    'Ambulance',
    'Hospital',
    'Fire',
    'Tourist Support',
    'Other'

];


$error = '';


$title =
    trim($_POST['title'] ?? '');

$emergencyType =
    trim($_POST['emergency_type'] ?? '');

$phone =
    trim($_POST['phone'] ?? '');

$area =
    trim($_POST['area'] ?? '');

$description =
    trim($_POST['description'] ?? '');

$displayOrder =
    trim($_POST['display_order'] ?? '0');

$isActive =
    isset($_POST['is_active'])
        ? 1
        : 0;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error =
            'Invalid request. Please refresh the page.';

    } elseif ($title === '') {

        $error =
            'Service title is required.';

    } elseif (
        !in_array(
            $emergencyType,
            $types,
            true
        )
    ) {

        $error =
            'Please select a valid emergency type.';

    } elseif ($phone === '') {

        $error =
            'Phone number is required.';

    } elseif (
        !preg_match(
            '/^[0-9+\-\s()]{2,30}$/',
            $phone
        )
    ) {

        $error =
            'Please enter a valid phone number.';

    } else {


        $statement =
            $pdo->prepare("
                INSERT INTO emergency_contacts
                (
                    title,
                    emergency_type,
                    phone,
                    area,
                    description,
                    display_order,
                    is_active
                )
                VALUES
                (
                    :title,
                    :emergency_type,
                    :phone,
                    :area,
                    :description,
                    :display_order,
                    :is_active
                )
            ");


        $statement->execute([

            ':title' =>
                $title,

            ':emergency_type' =>
                $emergencyType,

            ':phone' =>
                $phone,

            ':area' =>
                $area !== ''
                    ? $area
                    : null,

            ':description' =>
                $description !== ''
                    ? $description
                    : null,

            ':display_order' =>
                is_numeric($displayOrder)
                    ? (int) $displayOrder
                    : 0,

            ':is_active' =>
                $isActive

        ]);


        header(
            'Location: admin-emergency.php?created=1'
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
        Add Emergency Contact | Day With Explore
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


<section class="admin-dashboard-section">

    <div class="container">


        <div class="admin-form-wrapper">


            <a
                href="admin-emergency.php"
                class="admin-page-back"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Emergency Contacts

            </a>



            <div class="admin-form-card mt-3">


                <span class="admin-section-label">

                    NEW EMERGENCY CONTACT

                </span>


                <h1>
                    Add Emergency Information
                </h1>


                <p class="text-muted">

                    Add only verified contact information
                    that is useful to visitors.

                </p>



                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <i class="fa-solid fa-circle-exclamation me-2"></i>

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


                        <div class="col-md-7">

                            <label class="form-label admin-form-label">

                                Service Name *

                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($title) ?>"
                                placeholder="Example: Local Police Station"
                                required
                            >

                        </div>



                        <div class="col-md-5">

                            <label class="form-label admin-form-label">

                                Emergency Type *

                            </label>

                            <select
                                name="emergency_type"
                                class="form-select admin-form-control"
                                required
                            >

                                <option value="">
                                    Select Type
                                </option>


                                <?php foreach ($types as $type): ?>

                                    <option
                                        value="<?= htmlspecialchars($type) ?>"
                                        <?= $emergencyType === $type
                                            ? 'selected'
                                            : '' ?>
                                    >

                                        <?= htmlspecialchars($type) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>



                        <div class="col-md-6">

                            <label class="form-label admin-form-label">

                                Phone Number *

                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($phone) ?>"
                                placeholder="Verified contact number"
                                required
                            >

                        </div>



                        <div class="col-md-6">

                            <label class="form-label admin-form-label">

                                Area

                            </label>

                            <input
                                type="text"
                                name="area"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($area) ?>"
                                placeholder="Eravur / Batticaloa"
                            >

                        </div>



                        <div class="col-12">

                            <label class="form-label admin-form-label">

                                Description

                            </label>

                            <textarea
                                name="description"
                                class="form-control admin-form-control"
                                rows="4"
                                placeholder="Short useful information for visitors"
                            ><?= htmlspecialchars($description) ?></textarea>

                        </div>



                        <div class="col-md-4">

                            <label class="form-label admin-form-label">

                                Display Order

                            </label>

                            <input
                                type="number"
                                min="0"
                                name="display_order"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($displayOrder) ?>"
                            >

                        </div>



                        <div class="col-md-8 d-flex align-items-end">

                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_active"
                                    id="isActive"
                                    <?= $isActive
                                        ? 'checked'
                                        : '' ?>
                                >

                                <label
                                    class="form-check-label"
                                    for="isActive"
                                >

                                    Show this contact on public Emergency page

                                </label>

                            </div>

                        </div>


                    </div>



                    <div class="admin-form-actions">


                        <a
                            href="admin-emergency.php"
                            class="btn admin-cancel-btn"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="btn admin-add-btn"
                        >

                            <i class="fa-solid fa-floppy-disk me-2"></i>

                            Save Contact

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </div>

</section>


</body>
</html>