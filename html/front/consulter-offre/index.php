<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);

date_default_timezone_set('Europe/Paris');

session_start();

$id_offre_cible = intval($_GET['id']);
$categorie;

if (isset($_POST['titre'])) { 
    $submitted = true;
} else {
    $submitted = false;
}

// ===== GESTION DES EXTENSIONS DES FICHIERS ===== //
function get_file_extension($type) {
    $extension = '';
    switch ($type) {
        case 'image/png':
            $extension = '.png';
            break;
        case 'image/jpeg':
            $extension = '.jpg';
            break;
        case 'image/webp':
            $extension = '.webp';
            break;
        case 'image/gif':
            $extension = '.gif';
            break;
        default:
            die("probleme extension image");
            break;
    }
    return $extension;
}

// ===== GESTION DES INSERTION DES AVIS ===== //
if ($submitted) {

    if (isset($_POST['titre'])) {
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

    $categorie = getTypeOffre($id_offre_cible);

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

        $reqInsertionAvis = "INSERT INTO sae._avis(id_membre, id_offre, note, titre, commentaire, nb_pouce_haut, nb_pouce_bas, contexte_visite, publie_le, visite_le) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertionAvis = $dbh->prepare($reqInsertionAvis);
        $stmtInsertionAvis->execute([$id_membre, $id_offre, $note, $titre, $commentaire, 0, 0, $contexte_visite, $idDatePublication, $idDateVisite]);

        if ($categorie == "Restauration") {
            $reqInsertionCuisine = "INSERT INTO sae._note_detaillee(nom_note, note, id_membre, id_offre) VALUES (?, ?, ?, ?)";
            $stmtInsertionCuisine = $dbh->prepare($reqInsertionCuisine);
            $stmtInsertionCuisine->execute(["Cuisine", $noteCuisine, $id_membre, $id_offre]);

            $reqInsertionService = "INSERT INTO sae._note_detaillee(nom_note, note, id_membre, id_offre) VALUES (?, ?, ?, ?)";
            $stmtInsertionService = $dbh->prepare($reqInsertionService);
            $stmtInsertionService->execute(["Service", $noteService, $id_membre, $id_offre]);

            $reqInsertionAmbiance = "INSERT INTO sae._note_detaillee(nom_note, note, id_membre, id_offre) VALUES (?, ?, ?, ?)";
            $stmtInsertionAmbiance = $dbh->prepare($reqInsertionAmbiance);
            $stmtInsertionAmbiance->execute(["Ambiance", $noteAmbiance, $id_membre, $id_offre]);

            $reqInsertionRapport = "INSERT INTO sae._note_detaillee(nom_note, note, id_membre, id_offre) VALUES (?, ?, ?, ?)";
            $stmtInsertionRapport = $dbh->prepare($reqInsertionRapport);
            $stmtInsertionRapport->execute(["Rapport qualité prix", $noteRapport, $id_membre, $id_offre]);
        }

        if ((isset($_FILES['photo'])) && ($_FILES['photo']['error'] == 0)) {
            $nomFichier = 'Image_Avis_' . strval(time());
            $fichier = $_FILES['photo'];
            $extension = get_file_extension($fichier['type']);
            if ($extension !== '') {
                move_uploaded_file($fichier['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/' . $nomFichier . $extension);
                $fichierImage = $nomFichier . $extension;

                $reqInsertionImage = "INSERT INTO sae._image(lien_fichier) VALUES (?)";
                $stmtInsertionImage = $dbh->prepare($reqInsertionImage);
                $stmtInsertionImage->execute([$fichierImage]);

                $reqInsertionImageAvis = "INSERT INTO sae._avis_contient_image(id_membre, id_offre, lien_fichier) VALUES (?, ?, ?)";
                $stmtInsertionImageAvis = $dbh->prepare($reqInsertionImageAvis);
                $stmtInsertionImageAvis->execute([$id_membre, $id_offre_cible, $fichierImage]);
            }
        }

        $dbh->prepare("COMMIT;")->execute();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    } 
}


try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();

    addConsultedOffer($id_offre_cible);

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
    <meta name="viewport" content="width=device-width"/>
    <link rel="stylesheet" href="/style/style.css"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="/scripts/carousel.js"></script>
    <script src="/scripts/poucesAvis.js"></script>
    <script src="/scripts/formulaireAvis.js"></script>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="front consulter-offre-front">
    
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
    $stmt->execute();
    $of = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) { 
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}
?>

<header>
    <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
    <div class="text-wrapper-17"><a href="/front/accueil">PACT</a></div>
    <div class="search-box">
        <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
        <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
        <datalist id="cont">
            <?php foreach ($of as $o) { ?>
                <option value="<?php echo htmlspecialchars($o['titre']); ?>" data-id="<?php echo $o['id_offre']; ?>">
                    <?php echo htmlspecialchars($o['titre']); ?>
                </option>
            <?php } ?>
        </datalist>

    </div>
    <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
    <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
                        window.location.href = `/front/consulter-offre/index.php?id=${idOffre}`;
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

    <main id="body">

        <section class="fond-blocs bordure">
            
            <!-- AFFICHAGE DES TITRES ET DES IMAGES DES OFFRES -->
            <h1><?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible") ?></h1>

            <div class="carousel">
            <div class="carousel-slides">
<?php
foreach ($images as $image) {
?>
                <div class="slide">
                    <img src="/images/universel/photos/<?php echo htmlentities($image) ?>">
                </div>
<?php
}
?>
            </div>
            <button type="button" class="prev-slide"><img src="/images/universel/icones/fleche-gauche.png" alt="←"></button>
            <button type="button" class="next-slide"><img src="/images/universel/icones/fleche-droite.png" alt="→"></button>
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
            <div class="bloc-caracteristique">
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

            <h2>Description détaillée de l'offre</h2>
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

        <!-- GESTION DE L'AFFICHAGE DES AVIS -->
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

            <div class="petite-mention margin-0">
                <p><em>Ces avis sont l'opinion subjective des membre de la PACT et non les avis de la PACT. Les avis sont soumis à des vérifications de la part de la PACT.</em></p>
            </div> 

            <!-- GESTION DE LA PUBLICTION DES AVIS -->

            <!-- VÉRIFICATION DU COMPTE -->
            <?php if (isset($_SESSION['id']) && isset($_GET['id'])) {

                $id_membre = intval($_SESSION['id']);
                $id_offre = intval($_GET['id']);

                try {

                    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $dbh->prepare("SET SCHEMA 'sae';")->execute();

                    $reqCheckAvis = "SELECT COUNT(*) AS avis_count FROM sae._avis WHERE id_membre = ? AND id_offre = ?";
                    $stmtCheckAvis = $dbh->prepare($reqCheckAvis);
                    $stmtCheckAvis->execute([$id_membre, $id_offre]);
                    $avisCount = $stmtCheckAvis->fetch(PDO::FETCH_ASSOC)['avis_count']; ?>

                    <!-- VÉRIFICATION POUR LA PUBLICATION DES AVIS -->
                    <?php if ($avisCount == 0) { ?>

                        <button id="showFormButton">Publier un avis</button>
                        <form id="avisForm" action="index.php?id=<?php echo htmlentities($_GET['id'])?>" method="post" enctype="multipart/form-data" style="display: none;">
                            <h2 for="creation-avis">Création d'avis</h2><br>

                            <div class="display-ligne-espace">
                                <!-- CHAMP DE RÉDACTION DES TITRES DES AVIS --> 
                                <div>
                                    <label for="titre">Saisissez un titre <span>*</span></label>
                                    <input type="text" id="titre" name="titre" placeholder="Super expérience ..."required></input><br>
                                </div>

                                <!-- CHAMP DE SÉLECTION DES CONTEXTES DES L'AVIS -->
                                <div>
                                    <label for="contexte">Saisissez un contexte <span>*</span></label>
                                    <select id="contexte" name="contexte" required>
                                        <option value="" disabled selected>Choisissez un contexte</option>
                                        <option value="affaires">Affaires</option>
                                        <option value="couple">Couple</option>
                                        <option value="famille">Famille</option>
                                        <option value="amis">Amis</option>
                                        <option value="solo">Solo</option>
                                    </select><br>
                                </div>

                                <!-- CHAMP DE SÉLECTION DES NOTES DES AVIS -->
                                <div>
                                    <label for="note">Saisissez une note générale <span>*</span></label>
                                    <input type="number" id="note" name="note" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" placeholder="1 à 5" required/><br>
                                </div>
                            </div>

                            <!-- CHAMP DE SÉLECTION DES NOTES DES AVIS POUR LES OFFRES DE RESTAURATION -->
                            <?php if ($categorie == "Restauration") { ?>
                                
                                <div class="display-ligne">
                                    <div>
                                        <label for="note_cuisine">Saisissez une note pour la cuisine <span>*</span></label>
                                        <input type="number" id="note_cuisine" name="note_cuisine" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" placeholder="1 à 5" required/><br>
                                    </div>

                                    <div>
                                        <label for="note_service">Saisissez une note pour le service <span>*</span></label>
                                        <input type="number" id="note_service" name="note_service" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" placeholder="1 à 5" required/><br>
                                    </div>
                                </div>

                                <div class="display-ligne">
                                    <div>
                                        <label for="note_ambiance">Saisissez une note pour l'ambiance <span>*</span></label>
                                        <input type="number" id="note_ambiance" name="note_ambiance" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" placeholder="1 à 5" required/><br>
                                    </div>

                                    <div>
                                        <label for="note_rapport">Saisissez une note pour le rapport qualité prix <span>*</span></label>
                                        <input type="number" id="note_rapport" name="note_rapport" min="1" max="5" oninvalid="this.setCustomValidity('Veuillez saisir un nombre entre 1 et 5.')" oninput="this.setCustomValidity('')" placeholder="1 à 5" required/><br>
                                    </div>
                                </div>

                                <?php } ?>
                
                            <!-- CHAMP DE RÉDACTION DES AVIS -->
                            <div>
                                <label for="avis">Décrivez votre expérience <span>*</span></label>
                                <textarea id="avis" name="avis" placeholder="J'ai vraiment adoré ..." required></textarea><br>
                            </div>
                            
                            <div class="display-ligne-espace">
                                <!-- CHAMP DE SÉLECTION DES DATES DE VISITE DES AVIS -->
                                <div> 
                                    <label for="date">Saisissez la date de votre visite <span>*</span></label>
                                    <input type="datetime-local" id="date" name="date" max="<?php echo date('Y-m-d\TH:i'); ?>" required/><br>
                                </div>

                                <!-- CHAMP D'IMPORTATION DES PHOTOS DES AVIS -->
                                <div>
                                    <label id="photo" for="photo">Importez une photo</label> 
                                    <input type="file" id="photo" name="photo"/><br>
                                </div>
                            </div>

                            <p><em>En publiant cet avis, vous certifiez qu’il reflète votre propre expérience et opinion sur cette offre, que vous n’avez aucun lien avec le professionnel de cette offre et que vous n’avez reçu aucune compensation financière ou autre de sa part pour rédiger cet avis.</em></p>
                            <button type="submit">Publier</button>
                            <button type="button" id="cancelFormButton">Annuler</button>
                        </form>

                    <? } else { ?>

                        <p>Vous avez déjà publié un avis pour cette offre.</p>

                    <? } 

                } catch (PDOException $e) {

                    echo "Erreur : " . $e->getMessage();
                    die();
                }

            } else { ?>

                <p><a href="/se-connecter">Connexion</a> requise pour publier un avis</p>

            <?php }

            $compteur = 0;

            foreach ($avis as $a) { ?>

                <div class="fond-blocs-avis">
                    <!-- AFFICHAGE DES PSEUDONYMES DES AVIS -->
                    <div class="display-ligne">
                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']) ?></p>
                        <!--AFFICHAGE DES TITRES, DES NOTES ET DES DATES DE PUBLICATION DES AVIS -->
                        <div class="display-ligne">

                            <?php for ($etoileJaune = 0 ; $etoileJaune != $a['note'] ; $etoileJaune++) { ?>

                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">

                            <?php } 

                            for ($etoileGrise = 0 ; $etoileGrise != (5 - $a['note']) ; $etoileGrise++) { ?>

                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">

                            <?php } ?>

                        </div>
                    </div>

                    <!-- AFFICHAGE DES DATES DE PUBLICATION DES AVIS -->
                    <div class="display-ligne">
                        <?php $passage = explode(' ', $datePassage[$compteur]['date']);
                              $datePass = explode('-', $passage[0]); ?>
                        <p><strong><?php echo htmlentities(html_entity_decode(ucfirst($a['titre']))) ?> - Visité le <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> - <?php echo htmlentities(ucfirst($a['contexte_visite'])); ?></strong></p>
                    </div>

                    <!--AFFICHAGES DES NOTES DES AVIS POUR LES OFFRES DE RESTAURATION -->
                    <?php if ($categorie == "Restauration") { ?>

                        <div class="display-ligne">

                            <?php foreach ($noteDetaillee as $n) { ?>

                                <?php if (($n['id_membre'] == $a['id_membre']) && ($n['id_offre'] == $a['id_offre'])) { ?>

                                    <p><?php echo htmlentities($n['nom_note']) . " : " ?></p>

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

                    <div class="display-ligne">                        

                        <?php if (isset(getImageAvis($id_offre_cible, $a['id_membre'])[0]['lien_fichier'])) { ?>

                            <img class="image-avis" src="/images/universel/photos/<?php echo htmlentities(getImageAvis($id_offre_cible, $a['id_membre'])[0]['lien_fichier']); ?>">

                        <?php } ?>

                        <p><?php echo htmlentities(html_entity_decode(ucfirst($a['commentaire']))); ?></p>
                    </div>

                    <!-- AFFICHAGE DES RÉACTIONS DES AVIS -->
                    <div class="display-ligne-espace">
                        <div class="petite-mention">
                            <?php $publication = explode(' ', $dateAvis[$compteur]['date']);
                                $datePub = explode('-', $publication[0]); ?>
                            <p><em>Écrit le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?></em></p>
                        </div>

                        <div class="display-ligne">
                            <p class="nbPouceHaut"><?php echo htmlentities($a['nb_pouce_haut']); ?></p>
                            <img src="/images/universel/icones/pouce-up.png" class="pouce pouceHaut" data-id="<?php echo $compteur; ?>">

                            <p class="nbPouceBas"><?php echo htmlentities($a['nb_pouce_bas']); ?></p>
                            <img src="/images/universel/icones/pouce-down.png" class="pouce pouceBas" data-id="<?php echo $compteur; ?>">
                        </div>
                    </div>

                    <?php if(!empty($reponse[$compteur]['texte'])) { ?>

                        <div class="reponse">
                            <div class="display-ligne">
                                <img src="/images/universel/icones/reponse-violet.png">
                                <p class="titre-reponse"><?php echo htmlentities($compte['denomination']) ?></p>
                            </div>

                            <p><?php echo htmlentities(html_entity_decode(ucfirst($reponse[$compteur]['texte']))) ?></p>

                            <div class="display-ligne marge-reponse petite-mention">
                                <?php $rep = explode(' ', $dateReponse[$compteur]['date']);
                                      $dateRep = explode('-', $rep[0]); 
                                      $heureRep = explode(':', $rep[1]); ?>
                                <p class="indentation"><em>Répondu le <?php echo htmlentities($dateRep[2] . "/" . $dateRep[1] . "/" . $dateRep[0]); ?></em></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>  
            <?php $compteur++; 
            } ?>  
        </section>        

        <!-- BOUTONS DE NAVIGATION -->
        <div class="navigation display-ligne-espace">
            <button onclick="location.href='../../front/consulter-offres/'">Retour à la liste des offres</button>
            <button id="remonte" onclick="location.href='#'">^</button>
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

    <div class="telephone-nav">
        <div class="nav-content">
            <a href="/front/accueil">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/accueil.png">
                </div>
            </a>
            <a href="/front/consulter-offres">
                <div class="btOff">
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
</body>

</html>