<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/offres-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/auth-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
if (isset($id_compte)) {
    redirectToListOffreIfNecessary($id_compte);
} else {
    redirectTo('https://redden.ventsdouest.dev/front/consulter-offres/');
}

$reqPrix = "SELECT prix_offre from sae._offre where id_offre = :id_offre;";

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backListe.css">
    <link rel="stylesheet" href="/style/style_HFB.css">
    <link rel="stylesheet" href="/style/styleguide.css"/>
    <title>Liste de vos offres</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <h1>Liste de vos Offres</h1>
        <!--------------- 
        Filtrer et trier
        ----------------->
        <article class="filtre-tri">
            <h2>Filtres et tris</h2>
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
                                    <option>★★★★★</option>
                                    <option>★★★★</option>
                                    <option>★★★</option>
                                    <option>★★</option>
                                    <option>★</option>
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
                            <label><input type="radio" name="localisation"> Autour de moi</label>
                            <div>
                                <label><!--<input type="radio" name="localisation">--> Rechercher</label>
                                <input type="text" name="location" id="search-location" placeholder="Rechercher...">
                            </div>
                        </div>
                    </div>

                    <!-- Type d'offre -->
                    <div class="typeOffre">
                        <h3>Type d'offre</h3>
                        <div>
                            <label><input type="radio" name="typeOffre"> Standard</label>
                            <label><input type="radio" name="typeOffre"> Premium</label>
                        </div>
                    </div>
        
                    <!-- Date -->
                    <div class="date">
                        <h3>Date</h3>
                        <div>
                            <div>
                                <label>Date de début &nbsp;:</label>
                                <input type="date">
                            </div>
                            <div>
                                <label>Date de fin &emsp;&emsp;:</label>
                                <input type="date">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <section class="lesOffres">
            <p class="no-offers-message" style="display: none;">Aucun résultat ne correspond à vos critères.</p>
            <?php
            $reqOffre = "SELECT * from sae._offre where id_compte_professionnel = :id_compte;";
            $stmtOffre = $conn->prepare($reqOffre);
            $stmtOffre->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
            $stmtOffre->execute();
            while($row = $stmtOffre->fetch(PDO::FETCH_ASSOC)) { ?>
            <article class="offre">
                <a href="/back/consulter-offre/index.php?id=<?php echo urlencode($row['id_offre']); ?>">
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>
                    <div class="ouverture-offre"><?php  echo 'Ouvert'?></div>

                    <!---------------------------------------
                    Récuperer la premère image liée à l'offre
                    ---------------------------------------->
                    <img class="image-offre" src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($row['id_offre'])) ?>" alt="image offre">

                    <!---------------------------------------
                    Récuperer le titre liée à l'offre
                    ---------------------------------------->
                    <p class="titre-offre"><?php echo htmlentities($row["titre"]) ?></p>

                    <!--------------------------------------------------------
                    Choix du type de l'activité (Restaurant, parc, etc...
                    --------------------------------------------------------->
                    <p class="categorie-offre"> <?php echo htmlentities(getTypeOffre($row['id_offre']));?> </p>

                    <!---------------------------------------------------------------------- 
                    Choix de l'icone pour reconnaitre une offre gratuite, payante ou premium 
                    ------------------------------------------------------------------------>
                    <p class="type-offre" style="display: none"><?php echo $row["type_offre"]; ?></p>
                    <img src=" <?php
                    switch ($row["type_offre"]) {
                        case 'gratuite':
                            echo htmlentities("/images/backOffice/icones/gratuit.png");
                            break;
                        
                        case 'standard':
                            echo htmlentities("/images/backOffice/icones/payant.png");
                            break;
                            
                        case 'premium':
                            echo htmlentities("/images/backOffice/icones/premium.png");
                            break;
                    } ?>">

                    <!-------------------------------------- 
                    Affichage de la note globale de l'offre 
                    ---------------------------------------->
                    <div class="etoiles">
                        <?php 
                            $note = getNoteMoyenne($row['id_offre']);
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
                        ?>
                        <p><?php echo getNombreNotes($row['id_offre']) ?></p>
                    </div>
                    <div style="display: none;">
                        <!-------------------------------------- 
                        Affichage des avis non lues
                        ---------------------------------------->
                        <p>Avis non lus : <span><b>4</b></span></p>

                        <!-------------------------------------- 
                        Affichage des avis non répondues
                        ---------------------------------------->
                        <p>Avis non répondus : <span><b>1</b></span></p>

                        <!-------------------------------------- 
                        Affichage des avis blacklistés 
                        ---------------------------------------->
                        <p>Avis blacklistés : <span><b>0</b></span></p>
                    </div>

                    <!-------------------------------------- 
                    Affichage du prix 
                    ---------------------------------------->  
                    <p class="prix">A partir de <span><?php echo htmlentities($row["prix_offre"]) ?>€</span></p>
                </a>
            </article>
            <?php } ?>
            <!-------------------------------------- 
            Pagination
            ---------------------------------------->
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
            const offersContainer = document.querySelector(".lesOffres");
            const offers = Array.from(document.querySelectorAll(".offre"));

            const noOffersMessage = document.querySelector(".no-offers-message");

            const locationInput = document.getElementById("search-location");

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

                // Filter by Type
                const typeInput = document.querySelector(".typeOffre input[type='radio']:checked");
                if (typeInput) {
                    const type = typeInput.parentElement.textContent.trim().toLowerCase();
                    visibleOffers = visibleOffers.filter(offer => {
                        const typeAvailability = offer.querySelector(".type-offre").textContent.trim().toLowerCase();
                        return typeAvailability === type;
                    });
                }

                // Filter by Note
                const minNoteSelect = document.querySelector(".note");
                const selectedNote = minNoteSelect.value ? minNoteSelect.selectedIndex : null;
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

                console.log(visibleOffers);

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
                }
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