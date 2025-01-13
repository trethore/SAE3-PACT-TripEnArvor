<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);

require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT * from sae._offre where id_compte_professionnel = ?');
    $stmt->execute([$_SESSION['id']]);
    $of = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}


date_default_timezone_set('Europe/Paris');

if (isset($_POST['reponse'])) {

    $reponse = htmlentities($_POST['reponse']);
    print_r($reponse); 

    $publie_le = date('Y-m-d H:i:s');  

    try {

        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insérer la date de publication
        $reqInsertionDateReponse = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
        $stmtInsertionDateReponse = $dbh->prepare($reqInsertionDateReponse);
        $stmtInsertionDateReponse->execute([$publie_le]);
        $idDateReponse = $stmtInsertionDateReponse->fetch(PDO::FETCH_ASSOC)['id_date'];

        // Insérer la réponse liée à l'avis
        $reqInsertionReponse = "INSERT INTO sae._reponse(id_membre, id_offre, texte, publie_le) VALUES (?, ?, ?, ?)";
        $stmtInsertionReponse = $dbh->prepare($reqInsertionReponse);
        $stmtInsertionReponse->execute([$a['id_membre'], $id_offre_cible, $reponse, $idDateReponse]);

    } catch (PDOException $e) {

        echo "Erreur lors de l'insertion de la réponse : " . $e->getMessage();

    }

}

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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

// ===== GESTION DES MISES HORS LIGNE ET EN LIGNE ===== //

    // ===== Requête SQL pour vérifier si une offre est hors ligne ===== //
    $dateMiseHorsLigne = isOffreHorsLigne($id_offre_cible);

// ===== GESTION DU NOMBRE DE DATE (EN LIGNE / HORS LIGNE) ===== //

    // ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise hors ligne existe pour une offre ===== //
    $countDateMHL = countDatesOffreHorsLigne($id_offre_cible);

    // ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise en ligne existe pour une offre ===== //
    $countDateMEL = countDatesOffreEnLigne($id_offre_cible);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>

<html> 

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/style.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="back consulter-offre-back">
    
<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input  autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($of as $o) { ?>
                    <option value="<?php echo htmlspecialchars($o['titre']); ?>" data-id="<?php echo $o['id_offre']; ?>">
                        <?php echo htmlspecialchars($o['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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

    <div class="fond-bloc display-ligne-espace">
        <div class="bouton-modifier"> 
            <div id="confirm">
                <p>Voulez-vous mettre votre offre hors ligne ?</p>
                <div class="close">
                    <form method="post" enctype="multipart/form-data"><button type="submit" name="mettre_hors_ligne" onclick="showFinal()">Mettre hors ligne</button></form>

                    <?php $date = date('Y-m-d H:i:s'); 
                    if (isset($_POST['mettre_hors_ligne'])) {
                        if ($countDateMHL == 0) {
                            try {
                                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                //Insertion de la date de mise hors ligne
                                $reqInsertionDateMHL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                                $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
                                $stmtInsertionDateMHL->execute([$date]);
                                $idDateMHL = $stmtInsertionDateMHL->fetch(PDO::FETCH_ASSOC)['id_date'];
                            
                                $reqInsertionDateMHL = "INSERT INTO sae._offre_dates_mise_hors_ligne(id_offre, id_date) VALUES (?, ?)";
                                $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
                                $stmtInsertionDateMHL->execute([$id_offre_cible, $idDateMHL]);

                                //Suppression de la date de mise en ligne
                                $reqSuppressionDateMEL = "DELETE FROM sae._offre_dates_mise_en_ligne WHERE id_date IN (SELECT id_date FROM sae._date WHERE id_offre = :id_offre)";
                                $stmtSuppressionDateMEL = $dbh->prepare($reqSuppressionDateMEL);
                                $stmtSuppressionDateMEL->bindParam(':id_offre', $id_offre_cible, PDO::PARAM_INT);
                                $stmtSuppressionDateMEL->execute();
            
                            } catch (PDOException $e) {
                                echo "Erreur lors de l'insertion : " . $e->getMessage();
                            }
                        
                        } else if ($countDateMEL == 0) {
                        
                            try {
                                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                //Insertion de la date de mise en ligne
                                $reqInsertionDateMEL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                                $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
                                $stmtInsertionDateMEL->execute([$date]);
                                $idDateMEL = $stmtInsertionDateMEL->fetch(PDO::FETCH_ASSOC)['id_date'];
                            
                                $reqInsertionDateMEL = "INSERT INTO sae._offre_dates_mise_en_ligne(id_offre, id_date) VALUES (?, ?)";
                                $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
                                $stmtInsertionDateMEL->execute([$id_offre_cible, $idDateMEL]);

                                //Suppression de la date de mise hors ligne
                                $reqSuppressionDateMHL = "DELETE FROM sae._offre_dates_mise_hors_ligne WHERE id_date IN (SELECT id_date FROM sae._date WHERE id_offre = :id_offre)";
                                $stmtSuppressionDateMHL = $dbh->prepare($reqSuppressionDateMHL);
                                $stmtSuppressionDateMHL->bindParam(':id_offre', $id_offre_cible, PDO::PARAM_INT);
                                $stmtSuppressionDateMHL->execute();
            
                            } catch (PDOException $e) {
                                echo "Erreur lors de l'insertion : " . $e->getMessage();
                            }
                        }
                    } ?>

                    <button onclick="btnAnnuler()">Annuler</button>
                </div>
            </div>
            <div id="final">
                <p>Offre hors ligne !<br>Cette offre n'apparait plus</p>
                <button onclick="btnAnnuler()">Fermer</button>
            </div> 

            <?php if ($dateMiseHorsLigne != True) { ?>
                <button id="bouton1" onclick="showConfirm()">Mettre hors ligne</button>
            <?php } else { ?>
                <button id="bouton1" onclick="showConfirm()">Mettre en ligne</button>
            <?php } ?>
            <button id="bouton2" onclick="location.href='/back/modifier-offre/index.php?id=<?php echo htmlentities($id_offre_cible); ?>'">Modifier l'offre</button>
        </div>
    </div>  

    <main id="body">

        <section id="top" class="fond-blocs bordure">
            <!-- AFFICHAGE DES TITRES ET DES IMAGES DES OFFRES -->
            <h1><?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible") ?></h1>

            <div class="carousel">
                <div class="carousel-images">

                    <?php foreach ($images as $image) { ?>

                        <img src="/images/universel/photos/<?php echo htmlentities($image) ?>" alt="Image">

                    <?php } ?>

                </div>
            </div>  

            <div class="display-ligne-espace">

                <div class="display-ligne">
                    <p><?php echo htmlentities($categorie ?? "Pas de catégorie disponible") . ' - ' ?></p>

                    <div class="display-ligne">

                        <?php if ($noteMoyenne !== null) {

                            $etoilesPleines = floor($noteMoyenne);
                            $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                            $etoilesVides = 5 - $etoilesPleines - $demiEtoile;
                            
                            for ($i = 0; $i < $etoilesPleines; $i++) { ?>

                                <img class="etoile" src="/images/frontOffice/etoile-pleine.png">

                            <?php }

                            if ($demiEtoile) { ?>

                                <img class="etoile" src="/images/frontOffice/etoile-moitie.png">

                            <?php }

                            for ($i = 0; $i < $etoilesVides; $i++) { ?>

                                <img class="etoile" src="/images/frontOffice/etoile-vide.png">

                            <?php }

                        } ?>

                        <!-- AFFICHAGE DU NOMBRE D'AVIS DES OFFRES -->
                        <p><a href="#avis"><?php echo htmlentities($nombreNote) . " avis"; ?></a></p>
                    </div>
                </div>

                <!-- AFFICHAGE DES INFORMATIONS DES PROPRIÉTAIRES DES OFFRES -->
                <?php if (!empty($compte['denomination'])) { ?>

                    <p class="information-offre">Proposée par <?php echo htmlentities($compte['denomination']); ?></p>

                <?php } else { ?>

                    <p>Pas d'information sur le propriétaire de l'offre</p>

                <?php } ?> 

            </div>

            <div class="display-ligne">

                <!-- AFFICHAGE DES ADRESSES DES OFFRES -->
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

                    <p><?php echo implode(' ', $adresseComplete) . " - "; ?>

                <?php } else { ?>

                    <p>Pas d'adresse disponible</p>

                <?php } ?>

                <!-- AFFICHAGE DES CATÉGORIES ET DES INFORMATIONS DES CRÉNEAUX D'OUVERTURE --> 
                <?php setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                    $jour_actuel = $jours[date('w')];
                    $ouverture = "Pas d'information sur les créneaux d'ouverture";

                foreach ($horaire as $h) {

                    if (!empty($horaire)) {

                        $ouvert_ferme = date('H:i');
                        $fermeture_bientot = date('H:i', strtotime($h['fermeture'] . ' -1 hour'));
                        $ouverture = "Fermé";

                        if ($h['nom_jour'] == $jour_actuel) {

                            if ($h['ouverture'] < $ouvert_ferme && $ouvert_ferme < $fermeture_bientot) {

                                $ouverture = "Ouvert";

                            } elseif ($fermeture_bientot <= $ouvert_ferme && $ouvert_ferme < $h['fermeture']) {

                                $ouverture = "Ferme bientôt";

                            }

                        }

                    } 

                } ?>

                <p><?php echo htmlentities($ouverture); ?></p>

            </div>

        </section>

        <section class="double-blocs">

            <!-- AFFICHAGE DES TAGS DES OFFRES -->
            <div class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">

                    <?php if (!empty($tags)) {

                        foreach ($tags as $tag) { ?>

                            <li><?php echo htmlentities($tag['nom_tag']) ?></li>

                        <?php }

                    } else { ?>

                        <p>Pas de tags disponibles</p>

                    <?php } ?>

                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <!-- AFFICHAGE DES TITRES ET DES LIENS VERS LES SITES DES OFFRES -->
                <div class="display-ligne-espace">
                    <h2>À propos de <?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible"); ?></h2> 
                    <a href="<?php echo htmlentities($offre['site_web']); ?>">Lien vers le site</a>
                </div>

                <!-- Affichage du résumé de l'offre -->
                <p><?php echo htmlentities($offre['resume'] ?? "Pas de résumé disponible"); ?></p>

                <!-- AFFICHAGE DES INFORMTIONS SPÉCIFIQUES AU TYPE DES OFFRES -->
                <?php switch ($categorie) {

                    case "Activité": ?>
                        <p>Durée de l'activité : <?php echo htmlentities( floor($activite['duree'] / 60) . "h " . $activite['duree'] % 60) . "min"?></p>
                        <p>Âge minimum : <?php echo htmlentities($activite['age_min']) ?> ans</p>
                        <?php break; ?>

                    <?php case "Visite": ?>
                        <p>Durée de la visite : <?php echo htmlentities( floor($visite['duree'] / 60) . "h " . $visite['duree'] % 60) . "min"?></p>
                     <?php break; ?>
                    <?php case "Spectacle": ?>

                        <p>Durée du spectacle : <?php echo htmlentities( floor($spectacle['duree'] / 60) . "h " . $spectacle['duree'] % 60) . "min"?></p>
                        <p>Capacité de la salle : <?php echo htmlentities($spectacle['capacite']) ?> personnes</p>
                        <?php $event = explode(' ', $spectacle['date']);
                        $dateEvent = explode('-', $event[0]); ?>
                        <p>Date de l'évènement : <?php echo htmlentities($dateEvent[2] . "/" . $dateEvent[1] . "/" . $dateEvent[0]) ?></p>
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
                
                <!-- AFFICHAGE DES NUMÉROS DE TÉLÉPHONE DES OFFRES -->
                <p>Numéro de téléphone : <?php echo preg_replace('/(\d{2})(?=\d)/', '$1 ', htmlentities($compte['tel'] ?? "Pas de numéro de téléphone disponible")); ?></p>
            </div>
    
        </section>

        <section class="fond-blocs bordure">

            <h2>Description détaillée de l'offre :</h2>
            <!-- AFFICHAGE DES INFORMATIONS DÉTAILLÉS DES OFFRES -->
            <p><?php echo nl2br(htmlentities($offre['description_detaille'] ?? "Pas de description détaillée disponible")); ?></p>

        </section>

        <section class="double-blocs">

            <!-- AFFICHAGE DES TARIFS DES OFFRES -->
            <div class="fond-blocs bloc-tarif">
                <h2>Tarifs</h2>

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

                <?php } else { ?>

                    <p>Pas de tarifs diponibles</p>

                <?php } ?>

            </div>

            <!-- AFFICHAGE DES INFORMATIONS D'OUVERTURE DES OFFRES -->
            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture</h2>

                <?php if (!empty($horaire)) {

                    foreach ($horaire as $h) { ?>

                        <p><?php echo htmlentities($h['nom_jour'] . " : " . $h['ouverture'] . " - " . $h['fermeture'] . "\t") ?></p>

                    <?php } 

                } else { ?>

                    <p>Pas d'informations sur les jours et les horaires d'ouverture disponibles</p>

                <?php } ?>

            </div> 
            
        </section>

        <section id="carte" class="fond-blocs">

            <h1>Localisation</h1>
            <div id="map" class="carte"></div>

        </section>

        <section id="avis" class="fond-blocs bordure-top">
            <!-- AFFICHAGE DE LA NOTE MOYENNE DES AVIS -->
            <div class="display-ligne">

                <?php if ($noteMoyenne !== null) { ?>

                    <h2>Note moyenne : </h2>
                    <?php $etoilesPleines = floor($noteMoyenne);
                        $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                        $etoilesVides = 5 - $etoilesPleines - $demiEtoile;
                        
                    for ($i = 0; $i < $etoilesPleines; $i++) { ?>

                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">

                    <?php }

                    if ($demiEtoile) { ?>

                        <img class="etoile" src="/images/frontOffice/etoile-moitie.png">

                    <?php }

                    for ($i = 0; $i < $etoilesVides; $i++) { ?>

                        <img class="etoile" src="/images/frontOffice/etoile-vide.png">

                    <?php }

                } ?>

                <p>(<?php echo htmlentities($nombreNote) . ' avis'; ?>)</p>
            </div>

            <?php $compteur = 0;

            foreach ($avis as $a) { ?>

                <div class="fond-blocs-avis">
                    <!-- AFFICHAGE DES PSEUDONYMES DES AVIS -->
                    <div class="display-ligne-espace">
                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']) ?></p>
                    <!-- AFFICHAGE DES RÉACTIONS DES AVIS -->
                        <div class="display-notation">
                            <p><?php echo htmlentities($a['nb_pouce_haut']); ?></p><img src="/images/universel/icones/pouce-up.png" class="pouce">
                            <p><?php echo htmlentities($a['nb_pouce_bas']); ?></p><img src="/images/universel/icones/pouce-down.png" class="pouce"> 
                        </div>
                    </div>

                    <!--AFFICHAGE DES TITRES, DES NOTES ET DES DATES DE PUBLICATION DES AVIS -->
                    <div class="display-ligne">
                        <p><strong><?php echo htmlentities(html_entity_decode($a['titre'])) ?></strong></p>

                        <?php for ($etoileJaune = 0 ; $etoileJaune != $a['note'] ; $etoileJaune++) { ?>

                            <img src="/images/universel/icones/etoile-jaune.png" class="etoile">

                        <?php } 

                        for ($etoileGrise = 0 ; $etoileGrise != (5 - $a['note']) ; $etoileGrise++) { ?>

                            <img src="/images/universel/icones/etoile-grise.png" class="etoile">

                        <?php }

                        $publication = explode(' ', $dateAvis[$compteur]['date']);
                        $datePub = explode('-', $publication[0]); ?>
                        <p><strong>Publié le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?></strong></p>
                    </div>

                    <!--AFFICHAGES DES NOTES DES AVIS POUR LES OFFRES DE RESTAURATION -->
                    <?php if ($categorie == "Restauration") { ?>

                        <div class="display-ligne">

                            <?php foreach ($noteDetaillee as $n) { ?>

                                <?php if (($n['id_membre'] == $a['id_membre']) && ($n['id_offre'] == $a['id_offre'])) { ?>

                                    <p><strong><?php echo htmlentities($n['nom_note']) . " : " ?></strong></p>

                                    <?php for ($etoileJaune = 0 ; $etoileJaune != $n['note'] ; $etoileJaune++) { ?>

                                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">

                                    <?php } 

                                    for ($etoileGrise = 0 ; $etoileGrise != (5 - $n['note']) ; $etoileGrise++) { ?>

                                        <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">

                                    <?php } ?>

                                    <p><?php echo htmlentities("     ") ?></p>

                                <?php } ?>

                            <?php } ?>

                        </div>

                    <?php } ?>

                    <!-- AFFICHAGE DES DATES DE PUBLICATION DES AVIS -->
                    <div class="display-ligne">
                        <?php $passage = explode(' ', $datePassage[$compteur]['date']);
                            $datePass = explode('-', $passage[0]); ?>
                        <p>Visité le <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> - <?php echo htmlentities(ucfirst($a['contexte_visite'])); ?></p>
                    </div>

                    <div class="display-ligne">                        

                        <?php if (isset(getImageAvis($id_offre_cible, $a['id_membre'])[0]['lien_fichier'])) { ?>

                            <img class="image-avis" src="/images/universel/photos/<?php echo htmlentities(getImageAvis($id_offre_cible, $a['id_membre'])[0]['lien_fichier']); ?>">

                        <?php } ?>

                        <p><?php echo htmlentities(html_entity_decode($a['commentaire'])); ?></p>
                    </div>

                    <?php if(!empty($reponse[$compteur]['texte'])) { ?>

                        <div class="reponse">
                            <div class="display-ligne">
                                <img src="/images/universel/icones/reponse-orange.png">
                                <p class="titre-reponse"><?php echo htmlentities($compte['denomination']) ?></p>
                            </div>

                            <div class="display-ligne marge-reponse">
                                <?php $rep = explode(' ', $dateReponse[$compteur]['date']);
                                      $dateRep = explode('-', $rep[0]); 
                                      $heureRep = explode(':', $rep[1]); ?>
                                <p class="indentation">Répondu le <?php echo htmlentities($dateRep[2] . "/" . $dateRep[1] . "/" . $dateRep[0]); ?></p>
                            </div>

                            <p><?php echo htmlentities(html_entity_decode($reponse[$compteur]['texte'])) ?></p>
                        </div>

                    <?php } else { ?>

                        <form id="reponse" class="avis-form" action="index.php?id=<?php echo htmlentities($_GET['id'])?>" method="post" enctype="multipart/form-data">
                            <p class="titre-avis">Répondre à <?php echo htmlentities($membre[$compteur]['pseudo']); ?></p>
                            <div class="display-ligne">
                                <textarea id="reponse" name="reponse" placeholder="Merci pour votre retour ..." required></textarea><br>
                            </div>
                            <button type="submit" name="submit-reponse" value="true">Répondre</button>
                        </form>

                    <?php } ?> 

                </div>  
            <?php $compteur++; 
            } 
            ?>  

        </section>                
         
        <div class="navigation display-ligne-espace">
            <button onclick="location.href='../../back/liste-back/'">Retour à la liste des offres</button>
            <button id="remonte" onclick="location.href='#top'">^</button>
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
        <a href="/confidentialité/" target="_blank">Politique de confidentialité</a> - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        <a href="/cgu/" target="_blank">Conditions générales</a> - ©
        Redden's, Inc.
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

        let confirmDiv = document.getElementById("confirm");
        let finalDiv = document.getElementById("final");

        function showConfirm() {
            confirmDiv.style.display = "block";
            let header = document.getElementById('header');
            header.style.filter = "blur(10px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(10px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(10px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(10px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(10px)";
            let popup = document.getElementById('confirm');
            popup.style.filter = "none";
        }

        function showFinal() {
            finalDiv.style.display = "block";
            confirmDiv.style.display = "none";
            popup.style.filter = "none";
        }

        function btnAnnuler() {
            confirmDiv.style.display = "none";
            finalDiv.style.display = "none";
            let header = document.getElementById('header');
            header.style.filter = "blur(0px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(0px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(0px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(0px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(0px)";
        }

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

    </script>

</body>

</html>