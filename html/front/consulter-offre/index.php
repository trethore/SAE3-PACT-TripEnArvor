<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);

date_default_timezone_set('Europe/Paris');

session_start();

if (isset($_POST['titre'])) { 
    $submitted = true;
} else {
    $submitted = false;
}

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $id_offre_cible = intval($_GET['id']);

// ===== GESTION DES OFFRES ===== //

    // ===== Requête SQL pour récupérer les informations d'une offre ===== //
    $offre = getOffre($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est une activité ===== //
    $activite = getActivite($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est une visite ===== //
    $visite = getVisite($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un spectacle ===== //
    $spectacle = getSpectacle($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un parc d'attractions ===== //
    $attraction = getParcAttraction($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un restaurant ===== //
    $restaurant = getRestaurant($id_offre_cible);

// ===== GESTION DES ADRESSES ===== //

    // ===== Requête SQL pour récupérer les informations de l'adresse d'une offre ===== //
    $adresse = getAdresse($id_offre_cible);    

// ===== GESTION DES COMPTES PROFESSIONNELS ===== //

    // ===== Requête SQL pour récupérer les informations du compte du propriétaire de l'offre ===== //
    $compte = getCompte($id_offre_cible);

// ===== GESTION DES IMAGES ===== //

    // ===== Requête SQL pour récuéprer les images d'une offre ===== //
    $images = getIMGbyId($id_offre_cible);

// ===== GESTION DES NOTES ===== //

    // ===== Requête SQL pour récupérer le nombre de notes d'une offre ===== //
    $nombreNote = getNombreNotes($id_offre_cible);

    // ===== Requête SQL pour récupérer la note moyenne d'une offre ===== //
    $noteMoyenne = getNoteMoyenne($id_offre_cible);
    
// ===== GESTION DES TAGS ===== //

    // ===== Requête SQL pour récupérer les tags d'une offre ===== //
    $tags = getTags($id_offre_cible);

// ===== GESTION DES TARIFS ===== //

    // ===== Requête SQL pour récupérer les différents tarifs d'une offre ===== //
    $tarifs = getTarifs($id_offre_cible);

// ===== GESTION DE L'OUVERTURE ===== //
    
    // ===== Requête SQL pour récupérer les horaires d'ouverture d'une offre ===== //
    $horaire = getHorairesOuverture($id_offre_cible);

// ===== GESTION DES AVIS ===== //

    // ===== Requête SQL pour récupérer les avis d'une offre ===== //
    $avis = getAvis($id_offre_cible);

    // ===== Fonction qui exécute une requête SQL pour récupérer la note détaillée d'une offre de restauration ===== //
    $noteDetaillee = getAvisDetaille($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations des membres ayant publié un avis sur une offre ===== //
    $membre = getInformationsMembre($id_offre_cible);

    // ===== Requête SQL pour récupérer la date de publication d'un avis sur une offre ===== //
    $dateAvis = getDatePublication($id_offre_cible);

    // ===== Requête SQL pour récupérer la date de visite d'une personne yant rédigé un avis sur une offre ===== //
    $datePassage = getDatePassage($id_offre_cible);

// ===== GESTION DES RÉPONSES ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les réponses d'un avis d'une offre ===== //
    $reponse = getReponse($id_offre_cible);

    // ===== Fonction qui exécute une requête SQL pour récupérer la date de publication de la réponse à un avis sur une offre ===== //
    $dateReponse = getDatePublicationReponse($id_offre_cible);

// ===== GESTION DES TYPES ===== //

    // ===== Requête SQL pour récupérer le type d'une offre ===== //
    $categorie = getTypeOffre($id_offre_cible);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/styleguide.css"/>
    <link rel="stylesheet" href="/style/style_HFF.css"/>
    <link rel="stylesheet" href="/style/style-details-offre-visiteur.css"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>
    
    <header id="header">
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT</div>
        <div class="search-box">
        <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
        <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/front/consulter-offres"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <main id="body">

        <section id="top" class="fond-blocs bordure">
            <!-- Affichage du titre de l'offre -->
            <h1><?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible"); ?></h1>
            <div class="carousel">
                <div class="carousel-images">
                    <?php foreach ($images as $image) { ?>
                        <img src="/images/universel/photos/<?php echo htmlentities($image) ?>" alt="Image">
                    <?php } ?>
                </div>
                <div class="display-ligne-espace">
                    <div class="arrow-left">
                        <img src="/images/universel/icones/fleche-gauche.png" alt="Flèche navigation" class="prev">
                    </div>
                    <div class="arrow-right">
                        <img src="/images/universel/icones/fleche-droite.png" alt="Flèche navigation" class="next">
                    </div>
                </div>
            </div>


            <div class="display-ligne-espace information-offre">
                <!-- Affichage de la catégorie de l'offre et si cette offre est ouverte ou fermée -->
                <?php setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                $jour_actuel = $jours[date('w')];
                $ouverture = "Fermé";
                foreach ($horaire as $h) {
                    $ouvert_ferme = date('H:i');
                    $fermeture_bientot = date('H:i', strtotime($h['fermeture'] . ' -1 hour')); // Une heure avant la fermeture
                    if ($h['nom_jour'] == $jour_actuel) {
                        if ($h['ouverture'] < $ouvert_ferme && $ouvert_ferme < $fermeture_bientot) {
                            $ouverture = "Ouvert";
                        } elseif ($fermeture_bientot <= $ouvert_ferme && $ouvert_ferme < $h['fermeture']) {
                            $ouverture = "Ferme bientôt";
                        }
                    } 
                } ?>
                <p><em><?php echo htmlentities($categorie ?? "Pas de catégorie disponible") . ' - ' . $ouverture; ?></em></p>
                <!-- Affichage de l'adresse de l'offre -->
                <?php if (!empty($adresse['num_et_nom_de_voie']) || !empty($adresse['complement_adresse']) || !empty($adresse['code_postal']) || !empty($offre['ville'])) { 
                        $adresseComplete = [];
                        if (!empty($adresse['num_et_nom_de_voie'])) {
                            $adresseComplete[] = htmlentities($adresse['num_et_nom_de_voie']);
                        }
                        if (!empty($adresse['complement_adresse'])) {
                            $adresseComplete[] = htmlentities($adresse['complement_adresse']);
                        }
                        if (!empty($adresse['code_postal'])) {
                            $adresseComplete[] = htmlentities(trim($adresse['code_postal']));
                        }
                        if (!empty($offre['ville'])) {
                            $adresseComplete[] = htmlentities($offre['ville']);
                        } ?>
                        <p><?php echo implode(' ', $adresseComplete); ?>
                    <?php } else {
                        echo "Pas d'adresse disponible";
                    }?>

            </div>
                
            <div class="display-ligne-espace">
                <div class="display-ligne">
                    <?php for ($etoileJaune = 0 ; $etoileJaune != $noteMoyenne ; $etoileJaune++) { ?>
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                    <?php } 
                    for ($etoileGrise = 0 ; $etoileGrise != (5 - $noteMoyenne) ; $etoileGrise++) { ?>
                        <img src="/images/universel/icones/etoile-grise.png" class="etoile">
                    <?php } ?>
                    <!-- Affichage du nombre d'avis de l'offre -->
                    <p><?php echo htmlentities($nombreNote) . ' avis'; ?></p>
                    <a href="#avis">Voir les avis</a>
                </div>
                <!-- Affichage du nom et du prénom du propriétaire de l'offre -->
                <?php if (!empty($compte['denomination'])) { ?>
                    <p class="information-offre">Proposée par : <?php echo htmlentities($compte['denomination']); ?></p>
                <? } else {
                    echo "Pas d'information sur le propriétaire de l'offre";
                }?> 
            </div>

        </section>  

        <section class="double-blocs">

            <div class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <?php if (!empty($tags)) {
                        foreach ($tags as $tag) { ?>
                            <li><?php echo htmlentities($tag['nom_tag']); ?></li>
                        <?php }
                    } else {
                        echo "Pas de tags disponibles";
                    } ?>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <div class="display-ligne-espace">
                    <!-- Affichage le titre de l'offre -->
                    <h2>À propos de : <?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible"); ?></h2> 
                    <!-- Affichage du lien du site du propriétaire de l'offre -->
                    <a href="<?php echo htmlentities($offre['site_web']); ?>">Lien vers le site</a>
                </div>
                <!-- Affichage du résumé de l'offre -->
                <p><?php echo htmlentities($offre['resume'] ?? "Pas de résumé disponible"); ?></p>
                <!-- Affichage des informations spécifiques à un type d'offre -->
                <?php switch ($categorie) {
                    case "Activité": ?>
                        <p>Durée de l'activité : <?php echo htmlentities($activite['duree']/60) ?> heure(s)</p>
                        <p>Âge minimum : <?php echo htmlentities($activite['age_min']) ?> ans</p>
                        <?php break; ?>
                    <?php case "Visite": ?>
                        <p>Durée de la visite : <?php echo htmlentities($visite['duree']/60) ?> heure(s)</p>
                        <?php break; ?>
                    <?php case "Spectacle": ?>
                        <p>Durée du spectacle : <?php echo htmlentities($spectacle['duree']/60) ?> heure(s)</p>
                        <p>Capacité de la salle : <?php echo htmlentities($spectacle['capacite']) ?> personnes</p>
                        <?php break; ?>
                    <?php case "Parc attraction": ?>
                        <p>Nombre d'attractions : <?php echo htmlentities($attraction['nb_attractions']) ?></p>
                        <div class="display-ligne-espace">
                            <p>Âge minimum : <?php echo htmlentities($attraction['age_min']) ?> ans</p>
                            <a href="<?php echo htmlentities($attraction['plan']) ?>" download="Plan" target="blank">Télécharger le plan du parc</a>
                        </div>
                        <?php break; ?>
                    <?php case "Restauration": ?>
                        <div class="display-ligne-espace">
                            <p>Gamme de prix : <?php echo htmlentities($restaurant['gamme_prix']) ?></p>
                            <a href="<?php echo htmlentities($restaurant['carte']) ?>" download="Carte" target="blank">Télécharger la carte du restaurant</a>
                        </div>
                        <?php break;
                } ?>
                
                <!-- Affichage du numéro de téléphone du propriétaire de l'offre -->
                <p>Numéro de téléphone : <?php echo preg_replace('/(\d{2})(?=\d)/', '$1 ', htmlentities($compte['tel'] ?? "Pas de numéro de téléphone disponible")); ?></p>
            </div>
    
        </section>

        <section class="fond-blocs bordure">

            <h2>Description détaillée de l'offre :</h2>
            <!-- Affichage de la description détaillée de l'offre -->
            <p><?php echo nl2br(htmlentities($offre['description_detaille'] ?? "Pas de description détaillée disponible")); ?></p>

        </section>

        <section class="double-blocs">

            <div class="fond-blocs bloc-tarif">
                <h2>Tarifs : </h2>
                <?php if (!empty($tarifs)) { ?>
                    <table>
                        <?php foreach ($tarifs as $t) { 
                            if ($t['nom_tarif'] != "nomtarif1") { 
                                if (!empty($t['nom_tarif'])) {?>
                                    <tr>
                                        <td><?php echo htmlentities($t['nom_tarif']) ?></td>
                                        <td><?php echo htmlentities($t['prix']) . " €"?></td>
                                    </tr>
                            <?php  }
                            }
                        } ?>
                    </table>
                <?php } else {
                    echo "Pas de tarifs diponibles";
                } ?>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture :</h2>
                <?php if (!empty($horaire)) {
                    foreach ($horaire as $h) { ?>
                        <p><?php echo htmlentities($h['nom_jour'] . " : " . $h['ouverture'] . " - " . $h['fermeture'] . "\t"); ?></p>
                    <?php } 
                } else {
                    echo "Pas d'informations sur les jours et les horaires d'ouverture disponibles";
                } ?>
            </div> 
            
        </section>

        <section id="carte" class="fond-blocs">

            <h1>Localisation</h1>
            <div id="map" class="carte"></div>

        </section>

        <section id="avis" class="fond-blocs bordure-top">

            <div class="display-ligne">
                <h2>Note moyenne : </h2>
                <?php for ($etoileJaune = 0 ; $etoileJaune != $noteMoyenne ; $etoileJaune++) { ?>
                    <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <?php } 
                for ($etoileGrise = 0 ; $etoileGrise != (5 - $noteMoyenne) ; $etoileGrise++) { ?>
                    <img src="/images/universel/icones/etoile-grise.png" class="etoile">
                <?php } ?>
                <p>(<?php echo htmlentities($nombreNote) . ' avis'; ?>)</p>
            </div>
            
            <?php if (isset($_SESSION['id']) && isset($_GET['id'])) {
                $id_membre = intval($_SESSION['id']);
                $id_offre = intval($_GET['id']);

                try {
                    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $dbh->prepare("SET SCHEMA 'sae';")->execute();

                    // Vérifier si l'utilisateur a déjà publié un avis pour cette offre
                    $reqCheckAvis = "SELECT COUNT(*) AS avis_count FROM sae._avis WHERE id_membre = ? AND id_offre = ?";
                    $stmtCheckAvis = $dbh->prepare($reqCheckAvis);
                    $stmtCheckAvis->execute([$id_membre, $id_offre]);
                    $avisCount = $stmtCheckAvis->fetch(PDO::FETCH_ASSOC)['avis_count'];

                    if ($avisCount == 0) {
                        // L'utilisateur n'a pas encore publié d'avis pour cette offre
                        ?>
                        <button id="showFormButton">Publier un avis</button>

                        <!-- Formulaire d'avis -->
                        <form id="avisForm" action="index.php?id=<?php echo htmlentities($_GET['id'])?>" method="post" enctype="multipart/form-data" style="display: none;">
                            <h2 for="creation-avis">Création d'avis</h2><br>
                            <div class="display-ligne-espace">
                                <label for="titre">Saisissez le titre de votre avis</label>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <input type="text" id="titre" name="titre" required></input><br>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <label for="contexte">Contexte de visite :</label>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <select id="contexte" name="contexte" required>
                                    <option value="" disabled selected>Choisissez un contexte</option>
                                    <option value="affaires">Affaires</option>
                                    <option value="couple">Couple</option>
                                    <option value="famille">Famille</option>
                                    <option value="amis">Amis</option>
                                    <option value="solo">Solo</option>
                                </select><br>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <label for="avis">Rédigez votre avis</label>
                                <p class="transparent">.</p>
                            </div>
                            <textarea id="avis" name="avis" required></textarea><br>
                            <div class="display-ligne-espace">
                                <label for="note">Saisissez la note de votre avis</label>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <input type="number" id="note" name="note" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" required/><br>
                                <p class="transparent">.</p>
                            </div>
                            <?php if ($categorie == "Restauration") { ?>
                                <div class="display-ligne-espace">
                                    <label for="note_cuisine">Saisissez une note pour la cuisine</label>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <input type="number" id="note_cuisine" name="note_cuisine" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" required/><br>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <label for="note_service">Saisissez une note pour le service</label>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <input type="number" id="note_service" name="note_service" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" required/><br>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <label for="note_ambiance">Saisissez une note pour l'ambiance</label>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <input type="number" id="note_ambiance" name="note_ambiance" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" required/><br>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <label for="note_rapport">Saisissez une note pour le rapport qualité prix</label>
                                    <p class="transparent">.</p>
                                </div>
                                <div class="display-ligne-espace">
                                    <input type="number" id="note_rapport" name="note_rapport" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" required/><br>
                                    <p class="transparent">.</p>
                                </div>
                            <?php } ?>
                            <div class="display-ligne-espace">
                                <label for="date">Saisissez la date de votre visite</label>
                                <p class="transparent">.</p>
                            </div>
                            <div class="display-ligne-espace">
                                <input type="datetime-local" id="date" name="date" max="<?php echo date('Y-m-d\TH:i'); ?>" required/><br>
                                <p class="transparent">.</p>
                            </div>
                            <p><em>En publiant cet avis, vous certifiez qu’il reflète votre propre expérience et opinion sur cette offre, que vous n’avez aucun lien avec le professionnel de cette offre et que vous n’avez reçu aucune compensation financière ou autre de sa part pour rédiger cet avis.</em></p>
                            <button type="submit">Publier</button>
                            <button type="button" id="cancelFormButton">Annuler</button>
                        </form>

                        <? if ($submitted) { ?>

                            <?php if (isset($_POST['titre'])) {
                                $titre = htmlspecialchars($_POST['titre']);
                            }
                            if (isset($_POST['contexte'])) {
                                $contexte_visite = htmlspecialchars($_POST['contexte']);
                            }
                            if (isset($_POST['avis'])) {
                                $commentaire = htmlspecialchars($_POST['avis']);
                            } 
                            if (isset($_POST['note'])) {
                                $note = intval($_POST['note']);
                            }
                            if ($categorie == "Restauration") {
                                if (isset($_POST['note_cuisine'])) {
                                    $noteCuisine = intval($_POST['note_cuisine']);
                                }
                                if (isset($_POST['note_service'])) {
                                    $noteService = intval($_POST['note_service']);
                                }
                                if (isset($_POST['note_ambiance'])) {
                                    $noteAmbiance = intval($_POST['note_ambiance']);
                                }
                                if (isset($_POST['note_rapport'])) {
                                    $noteRapport = intval($_POST['note_rapport']);
                                }
                            }
                            if (isset($_POST['date'])) {
                                $visite_le = explode('T', $_POST['date']);
                                $dateParts = explode('-', $visite_le[0]);
                                $anneeUpdate = $dateParts[0]; 
                                $moisUpdate = $dateParts[1]; 
                                $jourUpdate = $dateParts[2]; 
                                $heureMinute = $visite_le[1]; 
                                $visite_le = $anneeUpdate . "-" . $moisUpdate . "-" . $jourUpdate . " " . $heureMinute . ":00";
                            }
                            if (isset($_SESSION['id'])) {
                                $id_membre = intval($_SESSION['id']);
                            }
                            if (isset($_GET['id'])) {
                                $id_offre = intval($_GET['id']);
                            }

                            $publie_le = date('Y-m-d H:i:s');

                            try {
                                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $dbh->prepare("SET SCHEMA 'sae';")->execute();

                                $dbh->prepare("START TRANSACTION;")->execute();
                                
                                $reqInsertionDatePublication = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                                $stmtInsertionDatePublication = $dbh->prepare($reqInsertionDatePublication);
                                $stmtInsertionDatePublication->execute([$publie_le]);
                                $idDatePublication = $stmtInsertionDatePublication->fetch(PDO::FETCH_ASSOC)['id_date'];

                                $reqInsertionDateVisite = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                                $stmtInsertionDateVisite = $dbh->prepare($reqInsertionDateVisite);
                                $stmtInsertionDateVisite->execute([$visite_le]);
                                $idDateVisite = $stmtInsertionDateVisite->fetch(PDO::FETCH_ASSOC)['id_date'];

                                $reqInsertionAvis = "INSERT INTO sae._avis(id_membre, id_offre, note, titre, commentaire, nb_pouce_haut, nb_pouce_bas, contexte_visite, publie_le, visite_le) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_avis";
                                $stmtInsertionAvis = $dbh->prepare($reqInsertionAvis);
                                $stmtInsertionAvis->execute([$id_membre, $id_offre, $note, $titre, $commentaire, 0, 0, $contexte_visite, $idDatePublication, $idDateVisite]);
                                $idAvis = $stmtInsertionAvis->fetch(PDO::FETCH_ASSOC)['id_avis'];

                                if ($categorie == "Restauration") {
                                    $reqInsertionCuisine = "INSERT INTO sae._note_detaillee(nom_note, note, id_avis) VALUES (?, ?, ?)";
                                    $stmtInsertionCuisine = $dbh->prepare($reqInsertionCuisine);
                                    $stmtInsertionCuisine->execute(["Cuisine", $noteCuisine, $idAvis]);

                                    $reqInsertionService = "INSERT INTO sae._note_detaillee(nom_note, note, id_avis) VALUES (?, ?, ?)";
                                    $stmtInsertionService = $dbh->prepare($reqInsertionService);
                                    $stmtInsertionService->execute(["Service", $noteService, $idAvis]);

                                    $reqInsertionAmbiance = "INSERT INTO sae._note_detaillee(nom_note, note, id_avis) VALUES (?, ?, ?)";
                                    $stmtInsertionAmbiance = $dbh->prepare($reqInsertionAmbiance);
                                    $stmtInsertionAmbiance->execute(["Ambiance", $noteAmbiance, $idAvis]);

                                    $reqInsertionRapport = "INSERT INTO sae._note_detaillee(nom_note, note, id_avis) VALUES (?, ?, ?)";
                                    $stmtInsertionRapport = $dbh->prepare($reqInsertionRapport);
                                    $stmtInsertionRapport->execute(["Rapport qualité prix", $noteRapport, $idAvis]);
                                }
                                $dbh->prepare("COMMIT;")->execute();
                            } catch (PDOException $e) {
                                echo "Erreur : " . $e->getMessage();
                                die();
                            } 
                        }
                    } else {
                        // Message informant que l'utilisateur a déjà publié un avis
                        echo "<p>Vous avez déjà publié un avis pour cette offre.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                    die();
                }
            } else {
                echo "Connexion requise pour publier un avis";
            }

            $compteur = 0;
            foreach ($avis as $a) { ?>
                <div class="fond-blocs-avis">
                    <div class="display-ligne-espace">
                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']) ?></p>
                        <p class="transparent"><strong>⁝</strong></p>
                    </div>
                    <div class="display-ligne-espace">
                        <div class="display-ligne">
                            <p><strong><?php echo htmlentities(html_entity_decode($a['titre'])) ?></strong></p>
                            <?php for ($etoileJaune = 0 ; $etoileJaune != $a['note'] ; $etoileJaune++) { ?>
                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                            <?php } 
                            for ($etoileGrise = 0 ; $etoileGrise != (5 - $a['note']) ; $etoileGrise++) { ?>
                                <img src="/images/universel/icones/etoile-grise.png" class="etoile">
                            <?php }
                            $publication = explode(' ', $dateAvis[$compteur]['date']);
                            $datePub = explode('-', $publication[0]); 
                            $heurePub = explode(':', $publication[1]); ?>
                            <p><strong>Publié le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?> à <?php echo htmlentities($heurePub[0] . "H"); ?></strong></p>
                        </div>
                        <p class="transparent">.</p>
                    </div>
                    <?php if ($categorie == "Restauration") { 
                        foreach ($noteDetaillee as $n) { ?>
                            <div class="display-ligne">
                                <p><strong><?php echo htmlentities($noteDetaillee[$compteur]['nom_note']) ?></strong></p>
                                <?php for ($etoileJaune = 0 ; $etoileJaune != $noteDetaillee[$compteur]['note'] ; $etoileJaune++) { ?>
                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                <?php } 
                                for ($etoileGrise = 0 ; $etoileGrise != (5 - $noteDetaillee[$compteur]['note']) ; $etoileGrise++) { ?>
                                    <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                <?php } ?>
                            </div>
                        <?php }
                    } ?>
                    <?php $passage = explode(' ', $datePassage[$compteur]['date']);
                    $datePass = explode('-', $passage[0]); ?>
                    <p>Visité le : <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> Contexte : <?php echo htmlentities($a['contexte_visite']); ?></p>
                    <p><?php echo htmlentities(html_entity_decode($a['commentaire'])); ?></p>
                    <!-- <div class="display-ligne-espace">
                        <p class="transparent">.</p>
                        <div class="display-notation">
                            <p><?php //echo htmlentities($a['nb_pouce_haut']); ?></p><img id="pouce_haut_<?php //echo $compteur; ?>" onclick="togglePouce(<?php //echo $compteur; ?>, 'haut', <?php //echo $a['id_avis'] ?>)" src="/images/universel/icones/pouce-up.png" class="pouce">
                            <p><?php //echo htmlentities($a['nb_pouce_bas']); ?></p><img id="pouce_bas_<?php //echo $compteur; ?>" onclick="togglePouce(<?php //echo $compteur; ?>, 'bas', <?php //echo $a['id_avis'] ?>)" src="/images/universel/icones/pouce-down.png" class="pouce">
                        </div>
                    </div>

                    <?php //if(!empty($reponse[$compteur]['texte'])) { ?>
                        <div class="reponse">
                            <div class="display-ligne-espace">
                                <p class="titre-avis"><?php //echo htmlentities($compte['denomination']) ?></p>
                                <p><strong>⁝</strong></p>
                            </div>
                            <div class="display-ligne-espace">
                                <div class="display-ligne">
                                    <?php /*$rep = explode(' ', $dateReponse[$compteur]['date']);
                                    $dateRep = explode('-', $rep[0]); 
                                    $heureRep = explode(':', $rep[1]); ?>
                                    <p class="indentation"><strong>Répondu le <?php echo htmlentities($dateRep[2] . "/" . $dateRep[1] . "/" . $dateRep[0]); ?> à <?php echo htmlentities($heureRep[0] . "H"); */?></strong></p>
                                    <p class="transparent">.</p>
                                </div>
                            </div>
                            <p><?php //echo htmlentities($reponse[$compteur]['texte']) ?></p>
                        </div> -->
                    <?php //} ?>
                </div>      
            <?php $compteur++;
            } ?>  

        </section>        
         
        <div class="navigation display-ligne-espace">
            <button onclick="location.href='../../front/consulter-offres/'">Retour à la liste des offres</button>
            <button id="remonte" onclick="location.href='#top'"><img src="/images/backOffice/icones/fleche-vers-le-haut.png" width="50" height="50"></button>
        </div>

    </main>

    <footer id="footer">

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
        Redden’s, Inc.
        </div>
        
    </footer>

    <script>

        let map = L.map('map').setView([47.497745757735, -2.772722737126], 13); 
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        L.marker([47.497745757735, -2.772722737126]).addTo(map)
            .bindPopup('Côté Plage<br>Sarzeau')
            .openPopup();

        // Cibler les éléments
        const showFormButton = document.getElementById('showFormButton');
        const avisForm = document.getElementById('avisForm');
        const cancelFormButton = document.getElementById('cancelFormButton');

        // Afficher le formulaire au clic sur "Publier un avis"
        showFormButton.addEventListener('click', () => {
            avisForm.style.display = 'block'; // Affiche le formulaire
            showFormButton.style.display = 'none'; // Masque le bouton
        });

        // Masquer le formulaire au clic sur "Annuler"
        cancelFormButton.addEventListener('click', () => {
            avisForm.style.display = 'none'; // Masque le formulaire
            showFormButton.style.display = 'block'; // Réaffiche le bouton
        });

        const images = document.querySelector('.carousel-images');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');

        let currentIndex = 0;

        // Gestion du clic sur le bouton "Suivant"
        nextButton.addEventListener('click', () => {
        currentIndex++;
        if (currentIndex >= images.children.length) {
            currentIndex = 0; // Revenir au début
        }
        updateCarousel();
        });

        // Gestion du clic sur le bouton "Précédent"
        prevButton.addEventListener('click', () => {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = images.children.length - 1; // Revenir à la fin
        }
        updateCarousel();
        });

        // Met à jour l'affichage du carrousel
        function updateCarousel() {
        const width = images.clientWidth;
        images.style.transform = `translateX(-${currentIndex * width}px)`;
        }

        function togglePouce(index, type, id) {
            console.log("index : " + index + ", type : " + type + "id :" + id);
            const pouceHaut = document.getElementById(`pouce_haut_${index}`);
            const pouceBas = document.getElementById(`pouce_bas_${index}`);

            if (type === 'haut') {
                if (pouceHaut.src.endsWith("/images/universel/icones/pouce-up.png")) {
                    // Activate pouce haut and deactivate pouce bas
                    pouceHaut.src = "/images/universel/icones/pouce-up-hover.png";
                    pouceBas.src = "/images/universel/icones/pouce-down.png";
                    <?php
                        // updatePouce(id, 'haut', 'add');
                    ?>
                } else {
                    // Deactivate pouce haut
                    pouceHaut.src = "/images/universel/icones/pouce-up.png";
                    <?php
                        // updatePouce(id, 'haut', 'sub');
                    ?>
                }
            } else if (type === 'bas') {
                if (pouceBas.src.endsWith("/images/universel/icones/pouce-down.png")) {
                    // Activate pouce bas and deactivate pouce haut
                    pouceBas.src = "/images/universel/icones/pouce-down-hover.png";
                    pouceHaut.src = "/images/universel/icones/pouce-up.png";
                    <?php
                        // updatePouce(id, 'bas', 'add');
                    ?>
                } else {
                    // Deactivate pouce bas
                    pouceBas.src = "/images/universel/icones/pouce-down.png";
                    <?php
                        // updatePouce(id, 'bas', 'sub');
                    ?>
                }
            }
        }

    </script>

</body>

</html>