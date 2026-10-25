<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/admin.php';

requireAdmin();


$statement = $pdo->query("
    SELECT
        id,
        title,
        emergency_type,
        phone,
        area,
        description,
        display_order,
        is_active

    FROM emergency_contacts

    ORDER BY
        display_order ASC,
        id DESC
");


$contacts =
    $statement->fetchAll();


$message = '';


if (isset($_GET['created'])) {

    $message =
        'Emergency contact added successfully.';

} elseif (isset($_GET['updated'])) {

    $message =
        'Emergency contact updated successfully.';

} elseif (isset($_GET['deleted'])) {

    $message =
        'Emergency contact deleted successfully.';

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
        Emergency Management | Day With Explore
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
                        type="submit"
                        class="btn admin-logout-btn"
                    >

                        Logout

                    </button>

                </form>


            </div>


        </div>

    </div>

</nav>



<section class="admin-dashboard-section">

    <div class="container-fluid">


        <div class="admin-page-header">


            <div>


                <a
                    href="admin-dashboard.php"
                    class="admin-page-back"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Dashboard

                </a>


                <span class="admin-section-label d-block mt-3">

                    EMERGENCY MANAGEMENT

                </span>


                <h1>
                    Emergency Contacts
                </h1>


                <p>

                    Manage important emergency
                    numbers shown to tourists.

                </p>


            </div>



            <a
                href="admin-emergency-add.php"
                class="btn admin-add-btn"
            >

                <i class="fa-solid fa-plus me-2"></i>

                Add Contact

            </a>


        </div>



        <?php if ($message): ?>

            <div class="alert alert-success mt-4">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>



        <div class="admin-panel-card mt-4">


            <div class="admin-panel-header">


                <div>

                    <span class="admin-section-label">
                        DATABASE
                    </span>

                    <h4>
                        Emergency Information
                    </h4>

                </div>


                <span class="admin-coming-badge">

                    <?= count($contacts) ?>

                    Contacts

                </span>


            </div>



            <?php if ($contacts): ?>


                <div class="table-responsive">


                    <table
                        class="table admin-table align-middle"
                    >


                        <thead>

                            <tr>

                                <th>
                                    Emergency Service
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Phone
                                </th>

                                <th>
                                    Area
                                </th>

                                <th>
                                    Order
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php foreach ($contacts as $contact): ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $contact['title']
                                        ) ?>

                                    </strong>


                                    <small class="admin-slug-text">

                                        <?= htmlspecialchars(
                                            $contact['description']
                                            ?? 'No description'
                                        ) ?>

                                    </small>

                                </td>



                                <td>

                                    <span class="admin-table-category">

                                        <?= htmlspecialchars(
                                            $contact['emergency_type']
                                        ) ?>

                                    </span>

                                </td>



                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $contact['phone']
                                        ) ?>

                                    </strong>

                                </td>



                                <td>

                                    <?= htmlspecialchars(
                                        $contact['area']
                                        ?? '-'
                                    ) ?>

                                </td>



                                <td>

                                    <?= (int) $contact['display_order'] ?>

                                </td>



                                <td>

                                    <?php if (
                                        (int) $contact['is_active'] === 1
                                    ): ?>

                                        <span class="admin-featured-yes">
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            Hidden
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <td class="text-end">


                                    <a
                                        href="admin-emergency-edit.php?id=<?= (int) $contact['id'] ?>"
                                        class="btn admin-icon-btn"
                                        title="Edit"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>



                                    <form
                                        action="admin-emergency-delete.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this emergency contact?');"
                                    >


                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                csrfToken()
                                            ) ?>"
                                        >


                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $contact['id'] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="btn admin-icon-btn admin-delete-icon"
                                            title="Delete"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>


                                    </form>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


            <?php else: ?>


                <div class="text-center py-5">

                    <i
                        class="fa-solid fa-phone-volume fs-1 text-secondary mb-3"
                    ></i>

                    <h4>
                        No Emergency Contacts
                    </h4>

                    <p class="text-muted">

                        Add verified emergency
                        information using Add Contact.

                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>

</section>


</body>
</html>