<?php

require_once __DIR__ . '/config/database.php';

$message = "";
$error = "";

try {

    /*
    |--------------------------------------------------------------------------
    | GET CATEGORY IDS
    |--------------------------------------------------------------------------
    */

    $categoryRows = $pdo
        ->query("SELECT id, slug FROM categories")
        ->fetchAll();

    $categoryIds = [];

    foreach ($categoryRows as $category) {
        $categoryIds[$category['slug']] = $category['id'];
    }


    /*
    |--------------------------------------------------------------------------
    | DESTINATION DATA
    |--------------------------------------------------------------------------
    */

    $destinations = [

        [
            'category_slug' => 'beach',
            'name' => 'Pasikudah Beach',
            'slug' => 'pasikudah',
            'description' =>
                'A famous tourist beach known for its calm, shallow waters, white sandy shoreline and water sports activities.',
            'location' => 'Pasikudah, Batticaloa Region',
            'address' => 'Pasikudah, Batticaloa',
            'distance_km' => 18,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 1
        ],

        [
            'category_slug' => 'beach',
            'name' => 'Elephant Rock Beach',
            'slug' => 'elephant-rock',
            'description' =>
                'A peaceful beach located near Pasikudah, popular for relaxation, swimming and scenic coastal views.',
            'location' => 'Near Pasikudah, Batticaloa Region',
            'address' => 'Pasikudah, Batticaloa',
            'distance_km' => 20,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'historical',
            'name' => 'Batticaloa Fort',
            'slug' => 'batticaloa-fort',
            'description' =>
                'A historic Dutch fort built in the 17th century, offering important cultural and historical significance.',
            'location' => 'Batticaloa',
            'address' => 'Batticaloa',
            'distance_km' => 15,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 1
        ],

        [
            'category_slug' => 'nature',
            'name' => 'Batticaloa Lagoon',
            'slug' => 'lagoon',
            'description' =>
                'A beautiful lagoon famous for boat rides, mangrove ecosystems and the unique Singing Fish phenomenon.',
            'location' => 'Batticaloa',
            'address' => 'Batticaloa',
            'distance_km' => 14,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 1
        ],

        [
            'category_slug' => 'religious',
            'name' => 'Majithul Aksaa Mosque',
            'slug' => 'aksaa-mosque',
            'description' =>
                'A well-known mosque in Kattankudy used for daily prayers, religious activities and Islamic education.',
            'location' => 'Kattankudy',
            'address' => 'Kattankudy, Batticaloa',
            'distance_km' => 8,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'landmark',
            'name' => 'Batticaloa Lighthouse',
            'slug' => 'lighthouse',
            'description' =>
                'A historic lighthouse offering scenic coastal views and representing the maritime heritage of Batticaloa.',
            'location' => 'Batticaloa',
            'address' => 'Batticaloa',
            'distance_km' => 16,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'community',
            'name' => 'Eastern Cancer Hospice',
            'slug' => 'hospice',
            'description' =>
                'A well-known hospice providing compassionate palliative care and support services for cancer patients and their families.',
            'location' => 'Batticaloa Region',
            'address' => 'Batticaloa',
            'distance_km' => 8,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'cultural',
            'name' => 'Kattankudy Heritage Museum',
            'slug' => 'heritage-museum',
            'description' =>
                'A museum showcasing Islamic history, culture, traditions and heritage of the Kattankudy community.',
            'location' => 'Kattankudy',
            'address' => 'Kattankudy, Batticaloa',
            'distance_km' => 9,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'beach',
            'name' => 'Kattankudy Beach',
            'slug' => 'kattankudy-beach',
            'description' =>
                'A quiet coastal destination suitable for relaxing walks, enjoying sea views and experiencing the local fishing community.',
            'location' => 'Kattankudy',
            'address' => 'Kattankudy, Batticaloa',
            'distance_km' => 10,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not listed',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ],

        [
            'category_slug' => 'food',
            'name' => 'Food Bin Court',
            'slug' => 'food-bin',
            'description' =>
                'A popular family-friendly food court offering a wide selection of local and international food options.',
            'location' => 'Kattankudy',
            'address' => 'Kattankudy, Batticaloa',
            'distance_km' => 9,
            'opening_hours' => 'Not listed',
            'admission_fee' => 'Not applicable',
            'contact' => 'Not listed',
            'image' => null,
            'latitude' => null,
            'longitude' => null,
            'is_featured' => 0
        ]

    ];


    /*
    |--------------------------------------------------------------------------
    | INSERT / UPDATE DESTINATIONS
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO destinations
        (
            category_id,
            name,
            slug,
            description,
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

        ON CONFLICT(slug) DO UPDATE SET

            category_id = excluded.category_id,
            name = excluded.name,
            description = excluded.description,
            location = excluded.location,
            address = excluded.address,
            distance_km = excluded.distance_km,
            opening_hours = excluded.opening_hours,
            admission_fee = excluded.admission_fee,
            contact = excluded.contact,
            image = excluded.image,
            latitude = excluded.latitude,
            longitude = excluded.longitude,
            is_featured = excluded.is_featured,
            updated_at = CURRENT_TIMESTAMP
    ";

    $statement = $pdo->prepare($sql);


    foreach ($destinations as $destination) {

        $categorySlug = $destination['category_slug'];

        if (!isset($categoryIds[$categorySlug])) {
            throw new Exception(
                "Category not found: " . $categorySlug
            );
        }

        $statement->execute([

            ':category_id' =>
                $categoryIds[$categorySlug],

            ':name' =>
                $destination['name'],

            ':slug' =>
                $destination['slug'],

            ':description' =>
                $destination['description'],

            ':location' =>
                $destination['location'],

            ':address' =>
                $destination['address'],

            ':distance_km' =>
                $destination['distance_km'],

            ':opening_hours' =>
                $destination['opening_hours'],

            ':admission_fee' =>
                $destination['admission_fee'],

            ':contact' =>
                $destination['contact'],

            ':image' =>
                $destination['image'],

            ':latitude' =>
                $destination['latitude'],

            ':longitude' =>
                $destination['longitude'],

            ':is_featured' =>
                $destination['is_featured']

        ]);

    }


    $count = $pdo
        ->query(
            "SELECT COUNT(*) FROM destinations"
        )
        ->fetchColumn();


    $message =
        $count . " destinations are now available in the database.";


} catch (Throwable $e) {

    $error = $e->getMessage();

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
        Seed Destinations | Day With Explore
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body class="bg-light">

<div class="container py-5">

    <div
        class="card border-0 shadow-sm mx-auto"
        style="max-width:700px;"
    >

        <div class="card-body p-5">

            <h2 class="fw-bold mb-4">

                <i class="fa-solid fa-location-dot text-primary me-2"></i>

                Destination Database Setup

            </h2>


            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <h5 class="fw-bold">
                        Database Error
                    </h5>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php else: ?>

                <div class="alert alert-success">

                    <i class="fa-solid fa-circle-check me-2"></i>

                    <?= htmlspecialchars($message) ?>

                </div>

                <p class="mb-0">

                    Tourist destination records were inserted
                    successfully.

                </p>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>