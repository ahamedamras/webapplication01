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

    header(
        'Location: admin-emergency.php'
    );

    exit;

}


$statement =
    $pdo->prepare("
        SELECT *
        FROM emergency_contacts
        WHERE id = :id
        LIMIT 1
    ");


$statement->execute([
    ':id' => $id
]);


$contact =
    $statement->fetch();


if (!$contact) {

    header(
        'Location: admin-emergency.php'
    );

    exit;

}



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
    $contact['title'];

$emergencyType =
    $contact['emergency_type'];

$phone =
    $contact['phone'];

$area =
    $contact['area'] ?? '';

$description =
    $contact['description'] ?? '';

$displayOrder =
    $contact['display_order'];

$isActive =
    (int) $contact['is_active'];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


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



    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error =
            'Invalid request.';

    } elseif ($title === '') {

        $error =
            'Service name is required.';

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


        $updateStatement =
            $pdo->prepare("
                UPDATE emergency_contacts

                SET
                    title = :title,
                    emergency_type = :emergency_type,
                    phone = :phone,
                    area = :area,
                    description = :description,
                    display_order = :display_order,
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP

                WHERE id = :id
            ");


        $updateStatement->execute([

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
                $isActive,

            ':id' =>
                $id

        ]);


        header(
            'Location: admin-emergency.php?updated=1'
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
        Edit Emergency Contact | Day With Explore
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

                    EDIT EMERGENCY CONTACT

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $contact['title']
                    ) ?>

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


                        <div class="col-md-7">

                            <label class="form-label admin-form-label">
                                Service Name *
                            </label>

                            <input
                                type="text"
                                name="title"
                                class="form-control admin-form-control"
                                value="<?= htmlspecialchars($title) ?>"
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

                                    Show on public Emergency page

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

                            Update Contact

                        </button>


                    </div>


                </form>


            </div>

        </div>

    </div>

</section>


</body>
</html>