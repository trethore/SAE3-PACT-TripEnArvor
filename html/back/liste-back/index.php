<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
if (isset($id_compte)) {
    redirectToConnexionIfNecessaryPro($id_compte);
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
    <link rel="stylesheet" href="/style/style.css">

    <title>Liste de vos offres</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>
<body class="back liste-back">
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
startSession();
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT * from sae._offre where id_compte_professionnel = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}
?>

    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input  autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <?php
            $reqOffre = "SELECT * from sae._offre where id_compte_professionnel = :id_compte;";
            $stmtOffre = $conn->prepare($reqOffre);
            $stmtOffre->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
            $stmtOffre->execute();

            $remainingAvis = 0;

            while ($row = $stmtOffre->fetch(PDO::FETCH_ASSOC)) {
                $avisNonLus = getLu($row['id_offre']);

                foreach ($avisNonLus as $avis) {
                    if (!empty($avis) && empty($avis['lu'])) {
                        $remainingAvis++;
                    }
                }
            }
        ?>
        <a href="/back/mon-compte" class="icon-container">
            <img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" />
            <?php if ($remainingAvis > 0) { ?>
                <span class="notification-badge"><?php echo $remainingAvis; ?></span>
            <?php } ?>
        </a>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const inputSearch = document.querySelector(".input-search");
                const datalist = document.querySelector("#cont");
                // Événement sur le champ de recherche
                inputSearch.addEventListener("input", () => {
                    // Rechercher l'option correspondante dans le datalist
                    const selectedOption = Array.from(datalist.options).find(
                        option => option.value === inputSearch.value
                    );
                    if (selectedOption) {
                        const idOffre = selectedOption.getAttribute("data-id");
                        //console.log("Option sélectionnée :", selectedOption.value, "ID:", idOffre);
                        // Rediriger si un ID valide est trouvé
                        if (idOffre) {
                            // TD passer du back au front quand fini
                            window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
                        }
                    }
                });
                // Debugging pour vérifier les options disponibles
                const options = Array.from(datalist.options).map(option => ({
                    value: option.value,
                    id: option.getAttribute("data-id")
                }));
                //console.log("Options disponibles dans le datalist :", options);
            });
        </script>
    </header>
    <main>
        <div class="toast-container"></div>

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
                                <option value="create-desc">Créé récemment</option>
                                <option value="note-asc">Meilleure note</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Localisation -->
                    <div class="localisation">
                        <h3>Localisation</h3>
                        <div>
                            <label style="display: none;"><input type="radio" name="localisation"> Autour de moi</label>
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
                    <div class="date" style="display: none;">
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
            <article class="<?php if (getDateOffreHorsLigne($row['id_offre']) > getDateOffreEnLigne($row['id_offre'])) { echo 'hors-ligne-offre'; } else { echo 'offre'; } ?>">
                <a href="/back/consulter-offre/index.php?id=<?php echo urlencode($row['id_offre']); ?>">
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>

                    <?php $horaire = getHorairesOuverture($row['id_offre']);
                                    setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                                    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                    $jour_actuel = $jours[date('w')];
                                    $ouverture = "Indét.";
                                    foreach ($horaire as $h) {
                                        if (!empty($horaire)) {
                                            $ouvert_ferme = date('H:i');
                                            $fermeture_bientot = date('H:i', strtotime($h['fermeture'] . ' -1 hour')); // Une heure avant la fermeture
                                            $ouverture = "Fermé";
                                            if ($h['nom_jour'] == $jour_actuel) {
                                                if ($h['ouverture'] < $ouvert_ferme && $ouvert_ferme < $fermeture_bientot) {
                                                    $ouverture = "Ouvert";
                                                } elseif ($fermeture_bientot <= $ouvert_ferme && $ouvert_ferme < $h['fermeture']) {
                                                    $ouverture = "Ferme Bnt.";
                                                }
                                            }
                                        } 
                                    } ?>

                    <div class="ouverture-offre"><?php  echo htmlentities($ouverture) ?></div>

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
                    <img src=" <?php
                    switch ($row["abonnement"]) {
                        case 'gratuit':
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
                        ?>
                        <p class="note-avis" style="display: none;"><?php echo $note; ?></p>
                        <?php
                            if ($note != 0) {
                                $etoilesPleines = floor($note);
                                $demiEtoile = ($note - $etoilesPleines) == 0.5 ? 1 : 0;
                                $etoilesVides = 5 - $etoilesPleines - $demiEtoile;
                            } else {
                                $etoilesPleines = 0;
                                $demiEtoile = 0;
                                $etoilesVides = 5;
                            }

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
                        ?>
                        <p><?php echo getNombreNotes($row['id_offre']) ?></p>
                    </div>
                    <div>
                        <!-------------------------------------- 
                        Affichage des avis non lues
                        ---------------------------------------->
                        <?php
                            $avisNonLus = getLu($row['id_offre']);
                            $nonLusCount = 0;

                            forEach($avisNonLus as $avis) {
                                if (empty($avis['lu'])) {
                                    $nonLusCount++;
                                }
                            }
                        ?>
                        <p>Avis non lus : <span><b><?php echo $nonLusCount; ?></b></span></p>

                        <!-------------------------------------- 
                        Affichage des avis non répondues
                        ---------------------------------------->
                        <?php
                            $nbrAvis = getAvis($row['id_offre']);
                            $nbrReponses = getAllReponses($row['id_offre']);

                            $nbrAvisNonRepondus = count($nbrAvis) - count($nbrReponses);
                        ?>
                        <p>Avis non répondus : <span><b><?php echo $nbrAvisNonRepondus; ?></b></span></p>

                        <!-------------------------------------- 
                        Affichage des avis blacklistés 
                        ---------------------------------------->
                        <!-- <p>Avis blacklistés : <span><b>0</b></span></p> -->
                    </div>

                    <?php
                        if (!getDatePublicationOffre($row['id_offre'])) {
                            $row['date'] = "0-0-0 0:0:0";
                        } else {
                            $row['date'] = getDatePublicationOffre($offre['id_offre'])[0]['date'];
                        }
                        
                        if ($row['date'] == "0-0-0 0:0:0") {
                            $date = "date indisponible.";
                        } else {
                            $publication = explode(' ', $row["date"]);
                            $datePub = explode('-', $publication[0]);
                            $date = htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]);
                        }
                    ?>

                    <p class="date_publication_offre">Créée le <span><?php echo $date; ?></span></p>

                    <!-------------------------------------- 
                    Affichage du prix 
                    ---------------------------------------->
                    <?php if (getTypeOffre($row['id_offre']) == 'Restauration') { ?>
                            <p class="prix">Gamme prix <span><?php echo htmlentities(getRestaurant($row['id_offre'])["gamme_prix"]); ?><span></p>
                    <?php } else { ?>
                            <p class="prix">A partir de <span><?php
                            $prix['prix'] = getPrixPlusPetit($row['id_offre']);
                            if (getPrixPlusPetit($row['id_offre']) == null) {
                                $prix['prix'] = 0;
                            }
                            echo htmlentities($prix['prix']); ?>€</span></p>
                    <?php } ?>

                </a>
            </article>
            <?php } ?>
        </section>
        <a href="/back/creer-offre/">Créer une offre</a>

        <?php
            $reqOffre = "SELECT * from sae._offre where id_compte_professionnel = :id_compte;";
            $stmtOffre = $conn->prepare($reqOffre);
            $stmtOffre->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
            $stmtOffre->execute();

            $toastsData = [];
            $remainingAvis = 0;
            $remainingOffres = 0;

            while ($row = $stmtOffre->fetch(PDO::FETCH_ASSOC)) {
                $avisNonLus = getLu($row['id_offre']);

                $hasUnreadAvis = false; // Flag to check if the current offer has any unread reviews

                foreach ($avisNonLus as $avis) {
                    if (!empty($avis) && empty($avis['lu'])) {
                        $remainingAvis++;
                        $hasUnreadAvis = true; // Set the flag to true if an unread review is found
                    }
                }

                if ($hasUnreadAvis) {
                    $remainingOffres++; // Increment only if the offer has at least one unread review
                }
            }

            if ($remainingOffres > 3) {
                $toastsData[] = [
                    'title' => "Avis restants",
                    'message' => "Vous avez $remainingAvis avis non lus sur $remainingOffres offres.",
                    'link' => "none",
                ];
            } else {
                // Sinon, on affiche les toasts individuels
                $stmtOffre->execute(); // Réexécuter la requête pour parcourir à nouveau les résultats
                while ($row = $stmtOffre->fetch(PDO::FETCH_ASSOC)) {
                    $avisNonLus = getLu($row['id_offre']);

                    $remainingAvis = 0; // Reset the counter for each offer

                    foreach ($avisNonLus as $avis) {
                        if (!empty($avis) && empty($avis['lu'])) {
                            $remainingAvis++;
                        }
                    }

                    if ($remainingAvis == 1) {
                        $toastsData[] = [
                            'title' => $row['titre'],
                            'message' => "Vous avez $remainingAvis avis non lu.",
                            'link' => "/back/consulter-offre/index.php?id=" . $row['id_offre'],
                        ];
                    } elseif ($remainingAvis > 1) {
                        $toastsData[] = [
                            'title' => $row['titre'],
                            'message' => "Vous avez $remainingAvis avis non lu.",
                            'link' => "/back/consulter-offre/index.php?id=" . $row['id_offre'],
                        ];
                    }
                }
            }

            $toastsDataJson = json_encode($toastsData);
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
            function createToast(title, message, link) {
                const toastLink = document.createElement("a");
                toastLink.href = link;
                toastLink.classList.add("toast-link");

                const toast = document.createElement("div");
                toast.classList.add("toast");

                const toastContent = document.createElement("div");
                toastContent.classList.add("toast-content");

                const messageDiv = document.createElement("div");
                messageDiv.classList.add("message");

                const titleSpan = document.createElement("span");
                titleSpan.classList.add("message-text", "text-1");
                titleSpan.textContent = title;

                const messageSpan = document.createElement("span");
                messageSpan.classList.add("message-text", "text-2");
                messageSpan.textContent = message;

                messageDiv.appendChild(titleSpan);
                messageDiv.appendChild(messageSpan);
                toastContent.appendChild(messageDiv);

                const closeIcon = document.createElement("i");
                closeIcon.classList.add("uil", "uil-multiply", "toast-close");
                closeIcon.addEventListener("click", (e) => {
                    e.preventDefault();
                    toastLink.remove();
                });

                const progress = document.createElement("div");
                progress.classList.add("progress");
                const progressbottom = document.createElement("div");
                progress.classList.add("progress-bottom");
                const progressright = document.createElement("div");
                progress.classList.add("progress-right");

                toast.appendChild(toastContent);
                toast.appendChild(closeIcon);
                toast.appendChild(progress);
                toast.appendChild(progressbottom);
                toast.appendChild(progressright);

                toastLink.appendChild(toast);
                 
                const toastContainer = document.querySelector(".toast-container");
                toastContainer.appendChild(toastLink);

                setTimeout(() => {
                    toast.classList.add("active");
                    progress.classList.add("active");
                }, 10);

                setTimeout(() => {
                    toast.classList.remove("active");
                    setTimeout(() => {
                        toastLink.remove();
                    }, 300);
                }, 5000);

                setTimeout(() => {
                    progress.classList.remove("active");
                }, 5300);
            }

            const toastsData = <?php echo $toastsDataJson; ?>;

            toastsData.forEach((toast) => {
                createToast(toast.title, toast.message, toast.link);
            });
        });

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
                        return offerAvailability === availability || (availability === "Ouvert" && offerAvailability === "Ferme Bnt.");
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
                } if (selectedValue === "default") {
                    offers.sort((a, b) => initialOrder.indexOf(a) - initialOrder.indexOf(b));

                    offers.forEach(offer => offersContainer.appendChild(offer));

                } if (selectedValue === "note-asc") {
                    offers.sort((a, b) => {
                        let noteA = a.querySelector(".note-avis").textContent.trim();
                        let noteB = b.querySelector(".note-avis").textContent.trim();
                        return noteB - noteA;
                    });

                    offers.forEach(offer => offersContainer.appendChild(offer));
                } if (selectedValue === "create-desc") {
                    offers.sort((a, b) => {
                        let dateA = a.querySelector(".date_publication_offre span").textContent.trim();
                        if (dateA == "date indisponible.") {
                            dateA = "0";
                        } else {
                            const [day, month, year] = dateA.split("/").map(Number);

                            const dateObject = new Date(year, month - 1, day);
                            dateA = dateObject.getTime();
                        }
                        let dateB = b.querySelector(".date_publication_offre span").textContent.trim();
                        if (dateB == "date indisponible.") {
                            dateB = "0";
                        } else {
                            const [day, month, year] = dateB.split("/").map(Number);

                            const dateObject = new Date(year, month - 1, day);
                            dateB = dateObject.getTime();
                        }
                        return dateB - dateA;
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