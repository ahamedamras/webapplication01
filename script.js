console.log("Day With Explore loaded successfully.");


/* ==================================================
   DESTINATION SEARCH + FILTER
================================================== */

document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("destinationSearch");

    const filterButtons =
        document.querySelectorAll(".filter-btn");

    const placeCards =
        document.querySelectorAll(".place-card");

    let selectedCategory = "all";


    // Run only on destinations page
    if (
        searchInput &&
        filterButtons.length > 0 &&
        placeCards.length > 0
    ) {

        const destinationContainer =
            document.querySelector(
                ".destinations-page-section .container"
            );


        /* ==============================
           NO RESULTS MESSAGE
        ============================== */

        const noResultsMessage =
            document.createElement("div");

        noResultsMessage.id = "noResultsMessage";

        noResultsMessage.className =
            "text-center py-5 d-none";

        noResultsMessage.innerHTML = `
            <div style="
                width:70px;
                height:70px;
                margin:0 auto 18px;
                border-radius:20px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:rgba(22,138,173,0.10);
                color:#168aad;
                font-size:1.8rem;
            ">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <h4 style="font-weight:700;">
                No destinations found
            </h4>

            <p style="color:#6c7b82;">
                Try another destination name or category.
            </p>
        `;

        destinationContainer.appendChild(
            noResultsMessage
        );


        /* ==============================
           CATEGORY NORMALIZER
        ============================== */

        function normalizeCategory(category) {

            category =
                category
                    .toLowerCase()
                    .trim();


            if (category === "beaches") {
                return "beach";
            }


            return category;
        }



        /* ==============================
           FILTER FUNCTION
        ============================== */

        function filterDestinations() {

            const searchValue =
                searchInput.value
                    .toLowerCase()
                    .trim();


            let visibleCount = 0;


            placeCards.forEach(function (card) {

                const cardColumn =
                    card.closest(".col-md-6");


                /* IMPORTANT:
                   Search only place name
                */

                const titleElement =
                    card.querySelector(".place-body h4");


                const placeName =
                    titleElement
                        ? titleElement.textContent
                            .toLowerCase()
                            .trim()
                        : "";


                const categoryElement =
                    card.querySelector(".place-category");


                const cardCategory =
                    categoryElement
                        ? normalizeCategory(
                            categoryElement.textContent
                        )
                        : "";


                /* Name search */

                const matchesSearch =
                    placeName.includes(searchValue);


                /* Category search */

                const matchesCategory =
                    selectedCategory === "all" ||
                    cardCategory === selectedCategory;


                /* Both must match */

                if (
                    matchesSearch &&
                    matchesCategory
                ) {

                    cardColumn.style.display = "";

                    visibleCount++;

                } else {

                    cardColumn.style.display = "none";

                }

            });



            /* ==============================
               NO RESULTS
            ============================== */

            if (visibleCount === 0) {

                noResultsMessage.classList.remove(
                    "d-none"
                );

            } else {

                noResultsMessage.classList.add(
                    "d-none"
                );

            }

        }



        /* ==============================
           LIVE SEARCH
        ============================== */

        searchInput.addEventListener(
            "input",
            function () {

                filterDestinations();

            }
        );



        /* ==============================
           CATEGORY FILTER
        ============================== */

        filterButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {


                        /* Remove active */

                        filterButtons.forEach(
                            function (btn) {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /* Add active */

                        button.classList.add(
                            "active"
                        );


                        selectedCategory =
                            normalizeCategory(
                                button.textContent
                            );


                        filterDestinations();

                    }
                );

            }
        );

    }

});



/* ==================================================
   ADD DESTINATION TO TRIP
================================================== */

document.addEventListener("DOMContentLoaded", function () {

    const addToTripButton =
        document.getElementById(
            "addToTripBtn"
        );


    // Only destination details page
    if (!addToTripButton) {

        return;

    }


    const destinationId =
        addToTripButton.dataset.id;


    const destinationName =
        addToTripButton.dataset.name;



    /* ==============================
       GET SAVED TRIP
    ============================== */

    let tripPlaces = [];


    try {

        tripPlaces =
            JSON.parse(
                localStorage.getItem(
                    "dayWithExploreTrip"
                )
            ) || [];

    } catch (error) {

        tripPlaces = [];

    }



    /* ==============================
       ALREADY ADDED CHECK
    ============================== */

    const alreadyAdded =
        tripPlaces.some(
            function (place) {

                return place.id ===
                    destinationId;

            }
        );


    if (alreadyAdded) {

        addToTripButton.innerHTML =
            '<i class="fa-solid fa-check me-2"></i> Added to Trip';


        addToTripButton.classList.add(
            "trip-added"
        );

    }



    /* ==============================
       ADD TO TRIP
    ============================== */

    addToTripButton.addEventListener(
        "click",
        function () {


            let currentTripPlaces = [];


            try {

                currentTripPlaces =
                    JSON.parse(
                        localStorage.getItem(
                            "dayWithExploreTrip"
                        )
                    ) || [];

            } catch (error) {

                currentTripPlaces = [];

            }



            const exists =
                currentTripPlaces.some(
                    function (place) {

                        return place.id ===
                            destinationId;

                    }
                );


            if (exists) {

                addToTripButton.innerHTML =
                    '<i class="fa-solid fa-check me-2"></i> Already Added';


                addToTripButton.classList.add(
                    "trip-added"
                );


                return;

            }



            const place = {

                id: destinationId,

                name: destinationName

            };


            currentTripPlaces.push(place);



            localStorage.setItem(

                "dayWithExploreTrip",

                JSON.stringify(
                    currentTripPlaces
                )

            );



            addToTripButton.innerHTML =
                '<i class="fa-solid fa-check me-2"></i> Added to Trip';



            addToTripButton.classList.add(
                "trip-added"
            );

        }
    );

});