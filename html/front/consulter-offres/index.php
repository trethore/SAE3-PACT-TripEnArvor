<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

startSession();

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare('
        SELECT o.*, 
        c.nom_compte, 
        c.prenom AS prenom_compte,
        a.num_et_nom_de_voie, 
        a.complement_adresse, 
        a.code_postal, 
        a.ville, 
        a.pays
    FROM sae._offre o
    JOIN sae._compte c ON o.id_compte_professionnel = c.id_compte
    LEFT JOIN sae._adresse a ON o.id_adresse = a.id_adresse;');
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
        if ($offre["categorie"] == "Restauration") {
            $offre["gammedeprix"] = getRestaurant($offre['id_offre'])["gamme_prix"];
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
    <meta name="viewport" content="width=device-width"/>
    <link rel="stylesheet" href="/style/style.css">
    <title>Liste de vos offres</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
    <link rel="stylesheet" href="/lib/leaflet/leaflet.css">
    <link rel="stylesheet" href="/lib/cluster/src/MarkerCluster.css"/>
    <script src="/lib/leaflet/leaflet.js"></script>
    <script src="/lib/cluster/dist/leaflet.markercluster.js"></script>
    <script src="map.js"></script>
</head>
<body class="front liste-front">
    <?php
    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();
        $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
        $stmt->execute();
        $offresNav = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
        $dbh = null;
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des titres : " . $e->getMessage();
    }
    ?>

    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offresNav as $offreNav) { ?>
                    <option value="<?php echo htmlspecialchars($offreNav['titre']); ?>" data-id="<?php echo $offreNav['id_offre']; ?>">
                        <?php echo htmlspecialchars($offreNav['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
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
                            <label><input type="checkbox" name="disponibilite"> Ouvert</label>
                            <label><input type="checkbox" name="disponibilite"> Fermé</label>
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
        
                    <!-- Date -->
                    <div class="date">
                        <!-- <h3>Date</h3>
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
                        </div> -->
                    </div>

                    <!-- Contient avis -->
                    <?php if (isset($_SESSION['id'])) { ?>
                    <div class="oui_avis">
                        <h3>Contient un de vos avis</h3>
                        <div>
                            <label><input type="checkbox" name="oui_avis"> Oui</label>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </article>
        <div id="map"></div>
        <!-- Offres -->
        <section class="section-offres">
            <p class="no-offers-message" style="display: none;">Aucun résultat ne correspond à vos critères.</p>
                <?php
                foreach ($offres as $tab) {
                    $offres[$tab['id_offre']]['avis'] = "Non";
                    if ((getDateOffreHorsLigne($tab['id_offre']) < getDateOffreEnLigne($tab['id_offre']) || getDateOffreHorsLigne($tab['id_offre']) == null)) {
                    ?>
                        <div class="<?php echo isOffreEnRelief($tab['id_offre']) ? 'en-relief-offre' : 'offre'; ?>">
                            <a href="/front/consulter-offre/index.php?id=<?php echo urlencode($tab['id_offre']); ?>">
                                <div class="sous-offre">
                                    <?php
                                        if (isOffreEnRelief($tab['id_offre'])) {
                                            echo '<img class="image-en-relief" src="/images/frontOffice/icones/en-relief-heart.png">';
                                        }
                                    ?>
                                    <div class="lieu-offre"><?php echo $tab["ville"] ?></div>
                                    <?php $horaire = getHorairesOuverture($tab['id_offre']);
                                    setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                                    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                    $jour_actuel = $jours[date('w')];
                                    $ouverture = "Indét.";
                                    foreach ($horaire as $h) {
                                        if (!empty($horaire)) {
                                            $ouvert_ferme = date('H:i');
                                            $fermeture_bientot = date('H:i', strtotime($h['fermeture'] . ' -1 hour')); // Une heure avant la fermeture
                                            $ouverture = "Fermé";
                                            $offres[$tab['id_offre']]['ouverture'] = "Fermé"; 
                                            if ($h['nom_jour'] == $jour_actuel) {
                                                if ($h['ouverture'] < $ouvert_ferme && $ouvert_ferme < $fermeture_bientot) {
                                                    $ouverture = "Ouvert";
                                                    $offres[$tab['id_offre']]['ouverture'] = "Ouvert"; 
                                                } elseif ($fermeture_bientot <= $ouvert_ferme && $ouvert_ferme < $h['fermeture']) {
                                                    $ouverture = "Ferme Bnt.";
                                                    $offres[$tab['id_offre']]['ouverture'] = "Ferme Bnt."; 

                                                }
                                            }
                                        } 
                                    } ?>

                                    <div class="ouverture-offre"><?php echo htmlentities($ouverture) ?></div>
                                    <img class="image-offre" src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($tab['id_offre'])) ?>">
                                    <p class="titre-offre"><?php echo $tab["titre"] ?></p>
                                    <p class="categorie-offre"><?php echo $tab["categorie"]; ?></p>
                                    <p class="description-offre"><?php echo $tab["resume"] . " " ?><span>En savoir plus</span></p>
                                    <p class="nom-offre"><?php echo $tab["nom_compte"] . " " . $tab["prenom_compte"] ?></p>
                                    <?php
                                    $offres[$tab['id_offre']]['avis'] = "Non";
                                    if (isset($_SESSION['id'])) {
                                        $idMembres = getIdMembresContientAvis($tab['id_offre']);
                                        $userId = intval($_SESSION['id']);

                                        $idMembresSimplified = array_column($idMembres, 'id_membre');

                                        echo '<p style="display: none;" class="contientavisspot">';
                                        if (in_array($userId, $idMembresSimplified)) {
                                            echo "Oui";
                                            $offres[$tab['id_offre']]['avis'] = "Oui";
                                        } else {
                                            echo "Non";
                                            $offres[$tab['id_offre']]['avis'] = "Non";
                                        }
                                        echo "</p>";
                                    }
                                    ?>
                                    <div class="bas-offre">
                                        <div class="etoiles">
                                            <?php
                                                if (empty($tab["note"])) {
                                                    ?>
                                                        <p style="color: var(--noir);">Pas d'avis disponibles.</p>
                                                    <?php
                                                } else {
                                                    $note = $tab["note"];
                                                    $etoilesPleines = floor($note);
                                                    $demiEtoile = ($note - $etoilesPleines) == 0.5 ? 1 : 0;
                                                    $etoilesVides = 5 - $etoilesPleines - $demiEtoile;

                                                    for ($i = 0; $i < $etoilesPleines; $i++) {
                                                        ?>
                                                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                                                        <?php
                                                    }

                                                    if ($demiEtoile) {
                                                        ?>
                                                        <img class="etoile" src="/images/frontOffice/etoile-moitie.png">
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
                            </a>
                        </div>
                <?php
                    }
                }
            ?>

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
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const h2 = document.querySelector(".filtre-tri h2");
            const fondFiltres = document.querySelector(".fond-filtres");

            const filterInputs = document.querySelectorAll(".fond-filtres input, .fond-filtres select");
            const offersContainer = document.querySelector(".section-offres");
            const normalOffers = Array.from(document.querySelectorAll(".offre"));
            const otherOffers = Array.from(document.querySelectorAll(".en-relief-offre"));
            const offers = normalOffers.concat(otherOffers);
            const noOffersMessage = document.querySelector(".no-offers-message");
            const locationInput = document.getElementById("search-location");

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
                const availabilityInput = document.querySelector(".disponibilite input[type='checkbox']:checked");
                if (availabilityInput) {
                    const availability = availabilityInput.parentElement.textContent.trim().toLowerCase();
                    visibleOffers = visibleOffers.filter(offer => {
                        const offerAvailability = offer.querySelector(".ouverture-offre").textContent.trim().toLowerCase();
                        return offerAvailability === availability || (availability === "Ouvert" && offerAvailability === "Ferme Bnt.");
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
                    if (offer.querySelector(".categorie-offre").textContent.trim() == "Restauration" && minPrice == "0" && maxPrice == "Infinity") {
                        return true;
                    } else {
                        return price >= minPrice && price <= maxPrice;
                    }
                });

                // Filter by Location
                const searchLocation = locationInput.value.trim().toLowerCase();
                if (searchLocation) {
                    visibleOffers = visibleOffers.filter(offer => {
                        const location = offer.querySelector(".lieu-offre").textContent.trim().toLowerCase();
                        return location.includes(searchLocation);
                    });
                }

                // Filtre par offre contient avis
                const avisInput = document.querySelector(".oui_avis input[type='checkbox']:checked");
                if (avisInput) {
                    const contientAvis = avisInput.parentElement.textContent.trim().toLowerCase();
                    visibleOffers = visibleOffers.filter(offer => {
                        const offerContientAvis = offer.querySelector(".contientavisspot").textContent.trim().toLowerCase();
                        return offerContientAvis === contientAvis;
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
                applyMapFilters();
            };

            // Sort Offers
            const sortOffers = () => {
                const selectElement = document.querySelector(".tris");
                const selectedValue = selectElement.value;

                if (selectedValue === "price-asc" || selectedValue === "price-desc") {
                    offers.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector(".prix span").textContent.replace('€', '0').trim());
                        const priceB = parseFloat(b.querySelector(".prix span").textContent.replace('€', '0').trim());
                        return selectedValue === "price-asc" ? priceA - priceB : priceB - priceA;
                    });

                    offers.forEach(offer => offersContainer.appendChild(offer));
                } if (selectedValue === "default") {
                    offers.sort((a, b) => initialOrder.indexOf(a) - initialOrder.indexOf(b));

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
    <div class="telephone-nav">
        <div class="nav-content">
            <a href="/front/accueil">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/accueil.png">
                </div>
            </a>
            <a href="/front/consulter-offres">
                <div class="btOn">
                    <img width="400" height="400" src="/images/frontOffice/icones/chercher.png">
                </div>
            </a>
            <a href="/front/mon-compte">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/utilisateur.png">
                </div>
            </a>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var offres = <?php echo json_encode($offres); ?>;
            addMap();
            addOffersWithAddresses(offres);
        });
    </script>

</body>


<?php $dbh = null; ?>