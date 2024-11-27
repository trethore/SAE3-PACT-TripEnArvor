<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/offres-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/auth-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

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
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
                            <label><input type="checkbox"> Parc d'Attraction</label>
                            <label><input type="checkbox"> Restaurant</label>
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
                        <h3>Trier</h3>
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
                                <div>
                                    <select class="tris">
                                        <option value="default">Trier par :</option>
                                        <option value="price-asc">Prix croissant</option>
                                        <option value="price-desc">Prix décroissant</option>
                                    </select>
                                </div>
                            </div>
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
                                <input id="search-location" type="text" placeholder="Rechercher...">
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

        <!-- Offres -->
        <section class="section-offres">
            <p id="no-offers-message" style="display: none; text-align: center; font-size: 18px; color: gray;">
                Aucun résultat ne correspond à vos critères.
            </p>
                <?php
                foreach ($offres as $tab) {
                    ?>
                    <div class="offre">
                        <div class="sous-offre">
                            <a href="/back/consulter-offre/index.php?id=<?php echo urlencode($tab['id_offre']); ?>">
                                <div class="lieu-offre"><?php echo $tab["ville"] ?></div>
                                <div class="ouverture-offre"><?php /*echo $tab["ouvert"]*/ ?>Ouvert</div>
                                <img class="image-offre" style="background: url(/images/universel/photos/<?php echo htmlentities(getFirstIMG($tab['id_offre'])) ?>) center;">
                                <p class="titre-offre"><?php echo $tab["titre"] ?></p>
                                <p class="categorie-offre"><?php echo $tab["categorie"]; ?></p>
                                <p class="description-offre"><?php echo $tab["resume"] . " " ?><span>En savoir plus</span></p>
                                <p class="nom-offre"><?php echo $tab["nom_compte"] . " " . $tab["prenom"] ?></p>
                                <div class="bas-offre">
                                    <div class="etoiles">
                                        <?php
                                            if (empty($tab["note"])) {
                                                ?>
                                                    <p>Pas d'avis disponibles.</p>
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
                                    <p class="prix">A partir de <span><?php echo $tab["prix_offre"] ?>€</span></p>
                                </div>
                            </a>
                        </div>
                    </div>
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
            const offers = document.querySelectorAll('.offre');
            const allOffers = Array.from(offers);

            // Create the "no offers" message element
            const noOffersMessage = document.createElement("div");
            noOffersMessage.classList.add("no-offers-message");
            noOffersMessage.textContent = "Aucune offre ne correspond à vos critères.";
            offersContainer.parentNode.insertBefore(noOffersMessage, offersContainer);
            noOffersMessage.style.display = "none";

            h2.addEventListener("click", () => {
                fondFiltres.classList.toggle("hidden");
            });

            const selectElement = document.querySelector('.tris');

            const rebuildOfferHTML = (offerElement) => {
                const title = offerElement.querySelector('.titre-offre').textContent.trim();
                const city = offerElement.querySelector('.lieu-offre').textContent.trim();
                const price = offerElement.querySelector('.prix span').textContent.trim();
                const category = offerElement.querySelector('.categorie-offre').textContent.trim();
                const image = offerElement.querySelector('.image-offre').style.backgroundImage;
                const description = offerElement.querySelector('.description-offre').textContent.trim();
                const profile = offerElement.querySelector('.nom-offre').textContent.trim();
                const nombre_notes = offerElement.querySelector('.nombre-notes').textContent.trim();

                return `
                    <div class="offre">
                        <div class="sous-offre">
                            <a href="#">
                                <div class="lieu-offre">${city}</div>
                                <div class="ouverture-offre">Ouvert</div>
                                <img class="image-offre" style=background: center; background-image: ${image}">
                                <p class="titre-offre">${title}</p>
                                <p class="categorie-offre">${category}</p>s
                                <p class="description-offre">${description}</p>
                                <p class="nom-offre">${profile}</p>
                                <div class="bas-offre">
                                    <div class="etoiles">
                                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                                        <img class="etoile" src="/images/frontOffice/etoile-vide.png">
                                        <img class="etoile" src="/images/frontOffice/etoile-vide.png">
                                        <p class="nombre-notes">(${nombre_notes})</p>
                                    </div>
                                    <p class="prix">A partir de <span>${price}</span></p>
                                </div>
                            </a>
                        </div>
                    </div>`;
            };

            selectElement.addEventListener('change', () => {
                const selectedValue = selectElement.value; // Récupère la valeur de l'option sélectionnée
                
                if (selectedValue == "price-asc") {
                    allOffers.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector('.prix span').textContent.replace('€', '').trim());
                        const priceB = parseFloat(b.querySelector('.prix span').textContent.replace('€', '').trim());
                        return priceA - priceB;
                    });
                } else if (selectedValue == "price-desc") {
                    allOffers.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector('.prix span').textContent.replace('€', '').trim());
                        const priceB = parseFloat(b.querySelector('.prix span').textContent.replace('€', '').trim());
                        return priceB - priceA;
                    });
                }

                offersContainer.innerHTML = '';

                allOffers.forEach(offerElement => {
                    const offerHTML = rebuildOfferHTML(offerElement);
                    offersContainer.insertAdjacentHTML('beforeend', offerHTML);
                });
            });

            const applyFilters = () => {
                const filters = {
                    categories: Array.from(document.querySelectorAll(".categorie input:checked")).map(input => input.parentNode.textContent.trim()),
                    availability: document.querySelector(".disponibilite input:checked")?.parentNode.textContent.trim() || null,
                    minRating: document.querySelector(".trier .note")?.value || null,
                    minPrice: parseFloat(document.querySelector(".trier .min")?.value) || null,
                    maxPrice: parseFloat(document.querySelector(".trier .max")?.value) || null,
                };

                // Treat no categories checked as all categories selected
                if (filters.categories.length === 0) {
                    filters.categories = Array.from(document.querySelectorAll(".categorie label")).map(label => label.textContent.trim());
                }

                let visibleOffers = 0;

                allOffers.forEach(offer => {
                    const category = offer.querySelector(".categorie-offre")?.textContent.trim();
                    const priceText = offer.querySelector(".prix span")?.textContent.replace("€", "").trim();
                    const price = parseFloat(priceText) || 0;
                    const isAvailable = offer.querySelector(".ouverture-offre")?.textContent.trim() === "Ouvert";
                    const etoilesPleinesOffre = offer.querySelectorAll('.etoile[src="/images/frontOffice/etoile-pleine.png"]');
                    const numberOfStarsWanted = filters.minRating;
                    const filtreNote =  numberOfStarsWanted.length;
                    const note = etoilesPleinesOffre.length;

                    let matches = true;

                    // Filter by stars
                    if (filtreNote > note) {
                        matches = false;
                    }

                    // Filter by category
                    if (!filters.categories.includes(category)) {
                        matches = false;
                    }

                    // Filter by availability
                    if (filters.availability === "Ouvert" && !isAvailable) {
                        matches = false;
                    } else if (filters.availability === "Fermé" && isAvailable) {
                        matches = false;
                    }

                    // Filter by price
                    if ((filters.minPrice !== null && price < filters.minPrice) || 
                        (filters.maxPrice !== null && price > filters.maxPrice)) {
                        matches = false;
                    }

                    // Show or hide the offer
                    if (matches) {
                        offer.style.display = "block";
                        visibleOffers++;
                    } else {
                        offer.style.display = "none";
                    }
                });

                // Show or hide the "no offers" message
                noOffersMessage.style.display = visibleOffers === 0 ? "block" : "none";
            };

            // Add change event listeners to filter inputs
            filterInputs.forEach(input => {
                input.addEventListener("change", applyFilters);
            });

            const locationInput = document.getElementById("search-location");
            const searchInput = document.querySelector(".input-search");

            locationInput.addEventListener("input", () => {
                const searchValue = locationInput.value.trim().toLowerCase();

                // Filtrer les offres en fonction de la localisation
                allOffers.forEach(offer => {
                    const location = offer.querySelector(".lieu-offre").textContent.trim().toLowerCase();
                    if (location.includes(searchValue)) {
                        offer.style.display = ""; // Affiche l'offre
                    } else {
                        offer.style.display = "none"; // Cache l'offre
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php $dbh = null; ?>