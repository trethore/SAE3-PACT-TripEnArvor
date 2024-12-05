<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare('SELECT * from sae._offre JOIN _compte ON _offre.id_compte_professionnel = _compte.id_compte');
    $stmt->execute();
    $offres = $stmt->fetchAll();

    foreach ($offres as &$offre) {
        $offre['categorie'] = getTypeOffre($offre['id_offre']);
    }

    foreach ($offres as &$offre) {
        $offre['note'] = getNoteMoyenne($offre['id_offre']);
    }
    
    foreach ($offres as &$offre) {
        $offre['nombre_notes'] = getNombreNotes($offre['id_offre']);
    }

    foreach ($offres as &$offre) {
        $offre['prix'] = getPrixPlusPetit($offre['id_offre']);
        if (getPrixPlusPetit($offre['id_offre']) == null) {
            $offre['prix'] = 0;
        }
    }
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style-consulter-offres-front.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/styleguide.css">
    <title>Liste de vos offres</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/front/consulter-offres"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <!-- Conteneur principal -->
    <main>
        <h1>Liste des Offres Disponibles</h1>
        <!--------------- 
        Filtrer et trier
        ----------------->
        <article class="filtre-tri">
            <h2>Filtres et Tris</h2>
            <div class="fond-filtres hidden">
                <div>
                    <!-- Catégorie -->
                    <div class="categorie">
                        <h3>Catégorie</h3>
                        <div>
                            <label><input type="checkbox"> Parc attraction</label>
                            <label><input type="checkbox"> Restauration</label>
                            <label><input type="checkbox"> Visite</label>
                            <label><input type="checkbox"> Spectacle</label>
                            <label><input type="checkbox"> Activité</label>
                        </div>
                    </div>

                    <!-- Disponibilité -->
                    <div class="disponibilite">
                        <h3>Disponibilité</h3>
                        <div>
                            <label><input type="radio" name="disponibilite"> Ouvert</label>
                            <label><input type="radio" name="disponibilite"> Fermé</label>
                        </div>
                    </div>
                        
                    <!-- Trier -->
                    <div class="trier">
                        <h3>Note et prix</h3>
                        <div>
                            <div>
                                <label>Note minimum :</label>
                                <select class="note">
                                    <option></option>
                                    <option>★</option>
                                    <option>★★</option>
                                    <option>★★★</option>
                                    <option>★★★★</option>
                                    <option>★★★★★</option>
                                </select>
                            </div>
                            
                            <div>
                                <div>
                                    <div>
                                        <label>Prix minimum &nbsp;:</label>
                                        <input class="min" type="number" min="0">
                                    </div>
                                    <div>
                                        <label>Prix maximum :</label>
                                        <input class="max" type="number" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="trier2">
                        <h3>Trier</h3>
                        <div>
                            <select class="tris">
                                <option value="default">Trier par :</option>
                                <option value="price-asc">Prix croissant</option>
                                <option value="price-desc">Prix décroissant</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Localisation -->
                    <div class="localisation">
                        <h3>Localisation</h3>
                        <div>
                            <!--<label><input type="radio" name="localisation"> Autour de moi</label>-->
                            <div>
                                <label><!--<input type="radio" name="localisation">--> Rechercher</label>
                                <input type="text" name="location" id="search-location" placeholder="Rechercher...">
                            </div>
                        </div>
                    </div>

                    <!-- Type d'offre -->
                    <div class="typeOffre"></div>
        
                    <!-- Date -->
                    <div class="date">
                        <h3>Date</h3>
                        <div>
                            <div>
                                <label>Période &nbsp;: du </label>
                                <input id="start-date" type="date">
                                <label style="margin-left: 10px;"> au </label>
                                <input id="end-date" type="date">
                            </div>
                            <div>
                                <label>Date d'ouverture :</label>
                                <input id="open-date" type="date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <!-- Offres -->
        <section class="section-offres">
            <p class="no-offers-message" style="display: none;">Aucun résultat ne correspond à vos critères.</p>
                <?php
                foreach ($offres as $tab) {
                    ?>
                    <a href="/front/consulter-offre/index.php?id=<?php echo urlencode($tab['id_offre']); ?>">
                    <div class="offre">
                        <div class="sous-offre">
                                <div class="lieu-offre"><?php echo $tab["ville"] ?></div>
                                <div class="ouverture-offre"><?php /*echo $tab["ouvert"]*/ ?>Ouvert</div>
                                <img class="image-offre" src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($tab['id_offre'])) ?>">
                                <p class="titre-offre"><?php echo $tab["titre"] ?></p>
                                <p class="categorie-offre"><?php echo $tab["categorie"]; ?></p>
                                <p class="description-offre"><?php echo $tab["resume"] . " " ?><span>En savoir plus</span></p>
                                <p class="nom-offre"><?php echo $tab["nom_compte"] . " " . $tab["prenom"] ?></p>
                                <div class="bas-offre">
                                    <div class="etoiles">
                                        <?php
                                            if (empty($tab["note"])) {
                                                ?>
                                                    <p style="color: var(--noir);">Pas d'avis disponibles.</p>
                                                <?php
                                            } else {
                                                $note = $tab["note"];
                                                $etoilesPleines = $note;
                                                $etoilesVides = 5 - $note;

                                                for ($i = 0; $i < $etoilesPleines; $i++) {
                                                    ?>
                                                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                                                    <?php
                                                }

                                                for ($i = 0; $i < $etoilesVides; $i++) {
                                                    ?>
                                                        <img class="etoile" src="/images/frontOffice/etoile-vide.png">
                                                    <?php
                                                }
                                            }
                                        ?>
                                        <p class="nombre-notes">(<?php echo $tab["nombre_notes"] ?>)</p>
                                    </div>
                                    <?php if ($tab["categorie"] == "Restauration") { ?>
                                        <p class="prix">Gamme prix <span><?php echo htmlentities(getRestaurant($tab['id_offre'])["gamme_prix"]); ?><span></p>
                                    <?php } else { ?>
                                        <p class="prix">A partir de <span><?php echo htmlentities($tab["prix"]); ?>€</span></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php
                    }
            ?>
        </section>        
    </main>

    <footer>
        <div class="footer-top">
        <div class="footer-top-left">
            <span class="footer-subtitle">P.A.C.T</span>
            <span class="footer-title">TripEnArmor</span>
        </div>
        <div class="footer-top-right">
            <span class="footer-connect">Restons connectés !</span>
            <div class="social-icons">
            <a href="https://x.com/?locale=fr">
                <div class="social-icon" style="background-image: url('/images/universel/icones/x.png');"></div>
            </a>
            <a href="https://www.facebook.com/?locale=fr_FR">
                <div class="social-icon" style="background-image: url('/images/universel/icones/facebook.png');"></div>
            </a>
            <a href="https://www.youtube.com/">
                <div class="social-icon" style="background-image: url('/images/universel/icones/youtube.png');"></div>
            </a>
            <a href="https://www.instagram.com/">
                <div class="social-icon" style="background-image: url('/images/universel/icones/instagram.png');"></div>
            </a>
            </div>
        </div>


        <!-- Barre en bas du footer incluse ici -->

        </div>
        <div class="footer-bottom">
        Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        Conditions générales - ©
        Redden's, Inc.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const h2 = document.querySelector(".filtre-tri h2");
            const fondFiltres = document.querySelector(".fond-filtres");

            const filterInputs = document.querySelectorAll(".fond-filtres input, .fond-filtres select");
            const offersContainer = document.querySelector(".section-offres");
            const offers = Array.from(document.querySelectorAll(".offre"));
            const noOffersMessage = document.querySelector(".no-offers-message");
            const locationInput = document.getElementById("search-location");

            const input1 = document.getElementById('start-date');
            const input2 = document.getElementById('end-date');
            const input3 = document.getElementById('open-date');

            input1.addEventListener('focus', () => {
                input3.value = '';
            });

            input2.addEventListener('focus', () => {
                input3.value = '';
            });

            input3.addEventListener('focus', () => {
                input1.value = '';
                input2.value = '';
            });

            const initialOrder = offers.slice();

            h2.addEventListener("click", () => {
                fondFiltres.classList.toggle("hidden");
            });

            // Function to filter offers based on active inputs
            const applyFilters = () => {
                let visibleOffers = offers;

                // Filter by Category
                const categoryCheckboxes = document.querySelectorAll(".categorie input[type='checkbox']:checked");
                const selectedCategories = Array.from(categoryCheckboxes).map(cb => cb.parentElement.textContent.trim());
                if (selectedCategories.length > 0) {
                    visibleOffers = visibleOffers.filter(offer => {
                        const category = offer.querySelector(".categorie-offre").textContent.trim();
                        return selectedCategories.includes(category);
                    });
                }

                // Filter by Availability
                const availabilityInput = document.querySelector(".disponibilite input[type='radio']:checked");
                if (availabilityInput) {
                    const availability = availabilityInput.parentElement.textContent.trim().toLowerCase();
                    visibleOffers = visibleOffers.filter(offer => {
                        const offerAvailability = offer.querySelector(".ouverture-offre").textContent.trim().toLowerCase();
                        return offerAvailability === availability;
                    });
                }

                // Filter by Note
                const minNoteSelect = document.querySelector(".note");
                const selectedNote = minNoteSelect.value ? minNoteSelect.selectedIndex : null;
                console.log("selectedNote : " + selectedNote);
                if (selectedNote) {
                    visibleOffers = visibleOffers.filter(offer => {
                        const stars = offer.querySelectorAll(".etoiles .etoile[src*='etoile-pleine']").length;
                        return stars >= selectedNote;
                    });
                }

                // Filter by Price Range
                const minPrice = parseFloat(document.querySelector(".min").value || "0");
                const maxPrice = parseFloat(document.querySelector(".max").value || "Infinity");
                visibleOffers = visibleOffers.filter(offer => {
                    const price = parseFloat(offer.querySelector(".prix span").textContent.replace('€', '').trim());
                    return price >= minPrice && price <= maxPrice;
                });

                // Filter by Date (Visite et Spectacle)
                const startDateInput = document.getElementById('start-date');
                const endDateInput = document.getElementById('end-date');

                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                visibleOffers = visibleOffers.filter(offer =>{
                    const category = offer.querySelector(".categorie-offre").textContent.trim();
                    const validCategories = ['Visite', 'Spectacle'];
                    const categoryOK = validCategories.includes(category);
                    const dateOK = eventDate >= startDate && eventDate <= endDate;

                    return categoryOK && dateOK;
                });

                // Filter by Location
                const searchLocation = locationInput.value.trim().toLowerCase();
                if (searchLocation) {
                    visibleOffers = visibleOffers.filter(offer => {
                        const location = offer.querySelector(".lieu-offre").textContent.trim().toLowerCase();
                        return location.includes(searchLocation);
                    });
                }

                // Update Visibility
                offers.forEach(offer => {
                    if (visibleOffers.includes(offer)) {
                        offer.style.display = "";
                    } else {
                        offer.style.display = "none";
                    }
                });

                // Show/Hide "No Offers" Message
                noOffersMessage.style.display = visibleOffers.length > 0 ? "none" : "block";
            };

            // Sort Offers
            const sortOffers = () => {
                const selectElement = document.querySelector(".tris");
                const selectedValue = selectElement.value;

                if (selectedValue === "price-asc" || selectedValue === "price-desc") {
                    offers.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector(".prix span").textContent.replace('€', '').trim());
                        const priceB = parseFloat(b.querySelector(".prix span").textContent.replace('€', '').trim());
                        return selectedValue === "price-asc" ? priceA - priceB : priceB - priceA;
                    });

                    offers.forEach(offer => offersContainer.appendChild(offer));
                } if (selectedValue === "default") {
                    offers.sort((a, b) => initialOrder.indexOf(a) - initialOrder.indexOf(b));
                }

                offers.forEach(offer => offersContainer.appendChild(offer));
            };

            // Add Event Listeners
            filterInputs.forEach(input => input.addEventListener("input", () => {
                applyFilters();
                sortOffers();
            }));

            document.querySelector(".tris").addEventListener("change", () => {
                sortOffers();
                applyFilters();
            });

            locationInput.addEventListener("input", () => {
                applyFilters();
            });
        });
    </script>
</body>
</html>

<?php $dbh = null; ?>