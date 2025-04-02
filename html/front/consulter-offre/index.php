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

// ===== GESTION DES TYPES ===== //

    // ===== Requête SQL pour récupérer le type d'une offre ===== //
    $categorie = getTypeOffre($id_offre_cible);
    $offre['categorie'] = $categorie;

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
    <title><?php echo htmlentities(html_entity_decode(ucfirst($offre['titre'] ?? "Pas de titre disponible"))) ?></title>
    <link rel="stylesheet" href="/lib/leaflet/leaflet.css">
    <link rel="stylesheet" href="/lib/cluster/src/MarkerCluster.css" />
    <script src="/lib/leaflet/leaflet.js"></script>
    <script src="/lib/cluster/dist/leaflet.markercluster.js"></script>
    <script src="/scripts/header.js"></script>
    <script src="/scripts/carousel.js"></script>
    <script src="/scripts/poucesAvis.js"></script>
    <script src="/scripts/formulaireAvis.js"></script>
    <script src="/scripts/popupAvis.js"></script>
    <script src="/scripts/blacklist.js"></script>
    <script src="/scripts/consulter-offre.js"></script>
    <script src="/scripts/preview.js"></script>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="front consulter-offre-front">

    <div id="overlay"></div>
    
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
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
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
    </header>

    <main id="body">

        <section class="fond-blocs bordure pur">
            
            <h1><?php echo htmlentities(html_entity_decode(ucfirst($offre['titre'] ?? "Pas de titre disponible"))) ?></h1>

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
                    <p><?php echo htmlentities($categorie ?? "Pas de catégorie disponible") . ' - '; ?></p>

                    <div class="display-ligne">
                        <?php 
                        if ($noteMoyenne !== null) {
                            $etoilesPleines = floor($noteMoyenne);
                            $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
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
                        <p><a class="lien" href="#avis"><?php echo htmlentities($nombreNote) . " avis"; ?></a></p>
                    </div>

                </div>

                <?php 
                if (!empty($compte['denomination'])) { 
                ?>
                    <p class="information-offre">Proposée par <?php echo htmlentities($compte['denomination']); ?></p>
                <?php 
                } else { 
                ?>
                    <p>Pas d'information sur le propriétaire de l'offre</p>
                <?php 
                } 
                ?> 

            </div>

            <div class="display-ligne">
                <?php 
                if (!empty($adresse['num_et_nom_de_voie']) || !empty($adresse['complement_adresse']) || !empty($adresse['code_postal']) || !empty($offre['ville'])) {
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
                    <p><?php echo implode(' ', $adresseComplete) . " - "; 
                } else { ?>
                    <p>Pas d'adresse disponible</p>
                <?php 
                }  
                setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                $jour_actuel = $jours[date('w')];
                $ouverture = "Indéterminé";
                foreach ($horaire as $h) {
                    if (!empty($horaire)) {
                        $ouvert_ferme = date('H:i');
                        $fermeture_bientot = date('H:i', strtotime($h['fermeture'] . ' -1 hour'));
                        $ouverture = "Fermé";
                        $result = "F";
                        if ($h['nom_jour'] == $jour_actuel) {
                            if ($h['ouverture'] < $ouvert_ferme && $ouvert_ferme < $fermeture_bientot) {
                                $ouverture = "Ouvert";
                                $result = "O";
                            } elseif ($fermeture_bientot <= $ouvert_ferme && $ouvert_ferme < $h['fermeture']) {
                                $ouverture = "Ferme bientôt";
                                $result = "FB";
                            }
                        }
                    } 
                } 
                ?>
                <p class="<?php echo htmlentities($result) ?> ouverture-decalage"><?php echo htmlentities($ouverture); ?></p>
            </div>

        </section>  

        <section class="fond-blocs bloc-tags">
            <div class="display-ligne-tag">
                <?php 
                if (!empty($tags)) {
                    foreach ($tags as $tag) { 
                ?>
                        <div class="display-ligne tag">
                            <p><?php echo htmlentities($tag['nom_tag']); ?></p>
                            <img src="/images/universel/icones/<?php echo htmlentities($tag['nom_tag']); ?>-violet.png" alt="<?php echo htmlentities($tag['nom_tag']); ?>">
                        </div>
                <?php 
                    }
                } else { 
                ?>
                    <p>Pas de tags disponibles</p>
                <?php 
                } 
                ?>
            </div>
        </section>

        <section class="double-blocs">

            <div id="map"></div>
            <script>displayOfferOnMap(<?php echo json_encode($offre)?>);</script>

            <div class="fond-blocs bloc-a-propos">
                
                <div class="display-ligne-espace">
                    <h2>À propos de <?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible"); ?></h2> 
                    <a class="lien" href="<?php echo htmlentities($offre['site_web']); ?>">Lien vers le site</a>
                </div>

                <p><?php echo htmlentities($offre['resume'] ?? "Pas de résumé disponible"); ?></p>

                <?php 
                switch ($categorie) {
                    case "Activité": 
                ?>
                        <p>Durée de l'activité : <?php echo htmlentities( floor($activite['duree'] / 60) . "h " . $activite['duree'] % 60) . "min"; ?></p>
                        <p>Âge minimum : <?php echo htmlentities($activite['age_min']); ?> ans</p>
                        <?php 
                        break; 
                        ?>
                    <?php 
                    case "Visite": 
                    ?>
                        <p>Durée de la visite : <?php echo htmlentities( floor($visite['duree'] / 60) . "h " . $visite['duree'] % 60) . "min"; ?></p>
                    <?php 
                        break; 
                    case "Spectacle": 
                    ?>
                        <p>Durée du spectacle : <?php echo htmlentities( floor($spectacle['duree'] / 60) . "h " . $spectacle['duree'] % 60) . "min"; ?></p>
                        <p>Capacité de la salle : <?php echo htmlentities($spectacle['capacite']); ?> personnes</p>
                        <?php 
                        $event = explode(' ', $spectacle['date']);
                        $dateEvent = explode('-', $event[0]); 
                        ?>
                        <p>Date de l'évènement : <?php echo htmlentities($dateEvent[2] . "/" . $dateEvent[1] . "/" . $dateEvent[0]) ?></p>
                        <?php 
                        break; 
                        ?>

                    <?php 
                    case "Parc attraction": 
                    ?>
                        <p>Nombre d'attractions : <?php echo htmlentities($attraction['nb_attractions']); ?></p>

                        <div class="display-ligne-espace">
                            <p>Âge minimum : <?php echo htmlentities($attraction['age_min']) ?> ans</p>
                            <a class="lien" href="<?php echo htmlentities($attraction['plan']) ?>" download="Plan" target="blank">Télécharger le plan</a>
                        </div>

                        <?php 
                        break; 
                        ?>

                    <?php 
                    case "Restauration": 
                    ?>
                        <div class="display-ligne-espace">
                            <p>Gamme de prix : <?php echo htmlentities($restaurant['gamme_prix']) ?></p>
                            <a class="lien" href="<?php echo htmlentities($restaurant['carte']) ?>" download="Carte" target="blank">Télécharger la carte</a>
                        </div>

                        <?php 
                        break;
                } 
                ?>
                <p>Numéro de téléphone : <?php echo preg_replace('/(\d{2})(?=\d)/', '$1 ', htmlentities($compte['tel'] ?? "Pas de numéro de téléphone disponible")); ?></p>
            </div>
    
        </section>

        <section class="fond-blocs bordure">

            <h2>Description détaillée de l'offre</h2>
            <p><?php echo nl2br(htmlentities($offre['description_detaille'] ?? "Pas de description détaillée disponible")); ?></p>

        </section>

        <section class="double-blocs">

            <div class="fond-blocs bloc-tarif">
                <h2>Tarifs</h2>
                <?php 
                if (!empty($tarifs)) { 
                ?>
                    <table>
                        <?php 
                        foreach ($tarifs as $t) { 
                            if ($t['nom_tarif'] != "nomtarif1") { 
                                if (!empty($t['nom_tarif'])) {
                        ?>
                                    <tr>
                                        <td><?php echo htmlentities($t['nom_tarif']) ?></td>
                                        <td><?php echo htmlentities($t['prix']) . " €"?></td>
                                    </tr>
                        <?php  
                                }
                            }
                        } 
                        ?>
                    </table>
                <?php 
                } else {
                ?>
                    <p>Pas de tarifs diponibles</p>
                <?php 
                } 
                ?>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture</h2>
                <?php 
                if (!empty($horaire)) {
                    foreach ($horaire as $h) { 
                ?>
                        <p><?php echo htmlentities($h['nom_jour'] . " : " . $h['ouverture'] . " - " . $h['fermeture'] . "\t"); ?></p>
                <?php 
                    } 
                } else { 
                ?>
                    <p>Pas d'informations sur les jours et les horaires d'ouverture disponibles</p>
                <?php 
                } 
                ?>
            </div> 
            
        </section>

        <section id="avis" class="fond-blocs bordure-top">

            <div class="display-ligne">
                <?php 
                if ($noteMoyenne !== null) { 
                ?>
                    <h2>Note moyenne     </h2>
                    <?php 
                    $etoilesPleines = floor($noteMoyenne);
                        $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                        $etoilesVides = 5 - $etoilesPleines - $demiEtoile;
                    for ($i = 0; $i < $etoilesPleines; $i++) { 
                    ?>
                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                    <?php }
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
                <p> (<?php echo htmlentities($nombreNote) . ' avis'; ?>)</p>
            </div>

            <div class="petite-mention margin-0">
                <p><em>Ces avis sont l'opinion subjective des membre de la PACT et non les avis de la PACT. Les avis sont soumis à des vérifications de la part de la PACT.</em></p>
            </div> 

            <?php 
            if (isset($_SESSION['id']) && isset($_GET['id'])) {
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
                    <?php 
                    if ($avisCount == 0) { 
                    ?>
                        <button id="showFormButton">Publier un avis</button>
                        <form id="avisForm" class="fond-blocs-avis" action="index.php?id=<?php echo htmlentities($_GET['id']); ?>" method="post" enctype="multipart/form-data" style="display: none;">
                            <h2 for="creation-avis">Création d'avis</h2><br>

                            <div class="display-ligne-espace">
                                
                                <div class="display-ligne">
                                    <p class="titre-avis"><?php echo htmlentities(getCompteMembre($_SESSION['id'])['pseudo']); ?></p>
                                    <div class="display-ligne">
                                        <label class="label-avis-champs">Note générale <span>*</span></label>
                                        <div class="rating">
                                            <input type="radio" name="note" id="star5_g" value="5" required><label for="star5_g"></label>
                                            <input type="radio" name="note" id="star4_g" value="4" required><label for="star4_g"></label>
                                            <input type="radio" name="note" id="star3_g" value="3" required><label for="star3_g"></label>
                                            <input type="radio" name="note" id="star2_g" value="2" required><label for="star2_g"></label>
                                            <input type="radio" name="note" id="star1_g" value="1" required><label for="star1_g"></label>
                                        </div>
                                    </div>
                                        
                                </div>
                                <img src="/images/universel/icones/trois-points-violet.png">
                                
                            </div>

                            <div class="display-ligne">

                                <div class="display-ligne">
                                    <label class="label-avis" for="titre">Titre <span>*</span></label>
                                    <input type="text" id="titre" name="titre" placeholder="Super expérience ..."required></input><br>
                                </div>

                                <div class="display-ligne"> 
                                    <label class="label-avis-champs" for="date">Date de visite <span>*</span></label>
                                    <input type="datetime-local" id="date" name="date" max="<?php echo date('Y-m-d\TH:i'); ?>" required/><br>
                                </div>

                                <div class="display-ligne">
                                    <label class="label-avis-champs" for="contexte">Contexte <span>*</span></label>
                                    <select id="contexte" name="contexte" required>
                                        <option value="" disabled selected>Contexte</option>
                                        <option value="affaires">Affaires</option>
                                        <option value="couple">Couple</option>
                                        <option value="famille">Famille</option>
                                        <option value="amis">Amis</option>
                                        <option value="solo">Solo</option>
                                    </select><br>
                                </div>

                            </div>

                            <?php 
                            if ($categorie == "Restauration") { 
                            ?>
                                <div class="display-ligne-note-detaille">

                                    <div class="display-ligne">
                                        <label class="label-avis" for="note_cuisine">Cuisine<span>*</span></label>
                                        <div class="rating">
                                            <input type="radio" name="note_cuisine" id="star5_c" value="5" required><label for="star5_c"></label>
                                            <input type="radio" name="note_cuisine" id="star4_c" value="4" required><label for="star4_c"></label>
                                            <input type="radio" name="note_cuisine" id="star3_c" value="3" required><label for="star3_c"></label>
                                            <input type="radio" name="note_cuisine" id="star2_c" value="2" required><label for="star2_c"></label>
                                            <input type="radio" name="note_cuisine" id="star1_c" value="1" required><label for="star1_c"></label>
                                        </div>
                                    </div>

                                    <div class="display-ligne">
                                        <label class="label-avis" for="note_service">Service<span>*</span></label>
                                        <div class="rating">
                                            <input type="radio" name="note_service" id="star5_s" value="5" required><label for="star5_s"></label>
                                            <input type="radio" name="note_service" id="star4_s" value="4" required><label for="star4_s"></label>
                                            <input type="radio" name="note_service" id="star3_s" value="3" required><label for="star3_s"></label>
                                            <input type="radio" name="note_service" id="star2_s" value="2" required><label for="star2_s"></label>
                                            <input type="radio" name="note_service" id="star1_s" value="1" required><label for="star1_s"></label>
                                        </div>                                    
                                    </div>

                                    <div class="display-ligne">
                                        <label class="label-avis" for="note_ambiance">Ambiance<span>*</span></label>
                                        <div class="rating">
                                            <input type="radio" name="note_ambiance" id="star5_a" value="5" required><label for="star5_a"></label>
                                            <input type="radio" name="note_ambiance" id="star4_a" value="4" required><label for="star4_a"></label>
                                            <input type="radio" name="note_ambiance" id="star3_a" value="3" required><label for="star3_a"></label>
                                            <input type="radio" name="note_ambiance" id="star2_a" value="2" required><label for="star2_a"></label>
                                            <input type="radio" name="note_ambiance" id="star1_a" value="1" required><label for="star1_a"></label>
                                        </div>              
                                    </div>

                                    <div class="display-ligne">
                                        <label class="label-avis" for="note_rapport">Rapport qualité prix<span>*</span></label>
                                        <div class="rating">
                                            <input type="radio" name="note_rapport" id="star5_r" value="5" required><label for="star5_r"></label>
                                            <input type="radio" name="note_rapport" id="star4_r" value="4" required><label for="star4_r"></label>
                                            <input type="radio" name="note_rapport" id="star3_r" value="3" required><label for="star3_r"></label>
                                            <input type="radio" name="note_rapport" id="star2_r" value="2" required><label for="star2_r"></label>
                                            <input type="radio" name="note_rapport" id="star1_r" value="1" required><label for="star1_r"></label>
                                        </div>                                                  
                                    </div>

                                </div>
                                <br>
                            <?php 
                            } 
                            ?>
                                        
                            <div>
                                <label for="avis">Décrivez votre expérience <span>*</span></label>
                                <textarea id="avis" name="avis" placeholder="J'ai vraiment adoré ..." required></textarea><br>
                            </div>
                                
                            <div>
                                <label id="photo" for="photo">Importez une photo</label> 
                                <input type="file" id="photo" name="photo"/><br>

                                <div id="imagePreview" style="margin-top: 10px; display: none;">
                                    <img id="preview" src="#" alt="Preview de l'image ajoutée." style="max-width: 200px; max-height: 200px;" />
                                </div>
                            </div>

                            <div class="petite-mention">
                                <p><em>En publiant cet avis, vous certifiez qu’il reflète votre propre expérience et opinion sur cette offre, que vous n’avez aucun lien avec le professionnel de cette offre et que vous n’avez reçu aucune compensation financière ou autre de sa part pour rédiger cet avis.</em></p>
                            </div>

                            <button type="submit">Publier</button>
                            <button type="button" id="cancelFormButton">Annuler</button>

                        </form>

                    <?php 
                    } else { 
                    ?>
                        <p>Vous avez déjà publié un avis pour cette offre.</p>
                    <?php
                    } 
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                    die();
                }
            } else { 
            ?>
                <p><a class="lien" href="/se-connecter">Connexion</a> requise pour publier un avis</p>
            <?php 
            }
            $identifiant = 0;
            foreach ($avis as $unAvis) {
                if (empty(getDateBlacklistage($unAvis['id_offre'], $unAvis['id_membre'])) || getDateBlacklistage($unAvis['id_offre'], $unAvis['id_membre']) == $_SESSION['id']) { 
            ?>

                    <div class="fond-blocs-avis">

                        <div class="display-ligne-espace">
                            
                            <div class="display-ligne">
                                <p class="titre-avis"><?php echo htmlentities(getPseudoFromId($unAvis['id_membre'])); ?></p>

                                <div class="display-ligne">
                                    <?php 
                                    for ($etoileJaune = 0 ; $etoileJaune != $unAvis['note'] ; $etoileJaune++) { 
                                    ?>
                                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                    <?php 
                                    } 
                                    for ($etoileGrise = 0 ; $etoileGrise != (5 - $unAvis['note']) ; $etoileGrise++) {
                                    ?>
                                        <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                    <?php 
                                    } 
                                    ?>
                                </div>

                            </div>
                            
                            <button class="menu-button" onclick="afficherMenu(event, this, <?php echo $identifiant; ?>)">
                                <img src="/images/universel/icones/trois-points-violet.png">
                            </button>

                            <div class="popup-menu" id="popup-menu-<?php echo $identifiant; ?>">
                                <ul>
                                    <?php 
                                    if (isset($_SESSION['id']) && $unAvis['id_membre'] == $_SESSION['id']) { 
                                    ?>
                                        <li onclick="confirmerSupprimer()" id="bouton-supprimer-avis">Supprimer</li>
                                    <?php 
                                    } else if (isset($_SESSION['id']) && !in_array($_SESSION['id'], getSignaler($id_offre_cible, $unAvis['id_membre']))) { 
                                    ?>
                                        <li onclick="confirmerSignaler(this, <?php echo $identifiant; ?>)" data-id-offre="<?php echo htmlentities($id_offre_cible); ?>" data-id-signale="<?php echo htmlentities($unAvis['id_membre']); ?>" data-id-signalant="<?php echo htmlentities($_SESSION['id']); ?>">Signaler</li>
                                    <?php 
                                    } 
                                    ?>
                                </ul>
                            </div>

                            <div class="confirmation-popup-signaler" id="confirmation-popup-signaler-<?php echo $identifiant; ?>" style="display: none;">

                                <div>
                                    <p>Quel est le problème avec l'avis de <strong><?php echo htmlentities(getPseudoFromId($unAvis['id_membre'])) ?></strong> ?</p>
                                    <form id="signalement-form">
                                        <label>
                                            <input type="radio" name="motif" value="Il contient des propos inappropriés"> Il contient des propos inappropriés
                                        </label><br>
                                        <label> 
                                            <input type="radio" name="motif" value="Il ne décrit pas une expérience personnelle"> Il ne décrit pas une expérience personnelle
                                        </label><br>
                                        <label>
                                            <input type="radio" name="motif" value="Il s'agit d'un doublon publié par le même membre"> Il s'agit d'un doublon publié par le même membre
                                        </label><br>
                                        <label>
                                            <input type="radio" name="motif" value="Il contient des informations fausses ou trompeuses"> Il contient des informations fausses ou trompeuses
                                        </label><br>
                                        <label for="justification-<?php echo $identifiant; ?>">Pouvez-vous décrire davantage le problème (facultatif) ?</label><br>
                                            <textarea id="justification-<?php echo $identifiant; ?>" name="justification"></textarea><br>
                                    </form>
                                    <button id="confirmer-signaler-<?php echo $identifiant; ?>" onclick="validerSignaler(<?php echo $identifiant; ?>)">Signaler</button>
                                    <button onclick="annulerSignaler(<?php echo $identifiant; ?>)">Annuler</button>
                                </div>

                            </div>

                            <div class="confirmation-popup" id="popup-supprimer-avis" style="display: none;">
                                <form action="/front/supprimer-avis/" method="post">
                                    <input type="hidden" name="id-offre" value="<?php echo($_GET['id']); ?>">
                                    <p>Êtes-vous sûr de vouloir supprimer votre avis ?</p>
                                    <p>Cette action ne peut pas être annulée.</p>
                                    <div>
                                        <button onclick="validerSupprimer()" type="submit" id="bouton-confirmer-supprimer-avis">Supprimer</button>
                                        <button onclick="annulerSupprimer()"type="button" id="bouton-fermer-popup">Annuler</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                        <div class="display-ligne">
                            <?php 
                            $passage = explode(' ', $datePassage[$identifiant]['date']);
                            $datePass = explode('-', $passage[0]); 
                            ?>
                            <p><strong><?php echo htmlentities(html_entity_decode(ucfirst($unAvis['titre']))); ?> - Visité le <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> - <?php echo htmlentities(ucfirst($unAvis['contexte_visite'])); ?></strong></p>
                        </div>

                        <?php 
                        if ($categorie == "Restauration") { 
                        ?>
                            <div class="display-ligne-note-detaille">
                                <?php 
                                foreach ($noteDetaillee as $n) {
                                    if (($n['id_membre'] == $unAvis['id_membre']) && ($n['id_offre'] == $unAvis['id_offre'])) { 
                                ?>
                                        <div class="display-ligne">
                                            <p><?php echo htmlentities($n['nom_note']) . " "; ?></p>
                                            <?php 
                                            for ($etoileJaune = 0; $etoileJaune != $n['note']; $etoileJaune++) { 
                                            ?>
                                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                            <?php 
                                            }
                                            for ($etoileGrise = 0; $etoileGrise != (5 - $n['note']); $etoileGrise++) { 
                                            ?>
                                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                            <?php 
                                            } 
                                            ?>
                                        </div>
                                <?php 
                                    }
                                } 
                                ?>
                            </div>
                        <?php 
                        } 
                        ?>

                        <div class="display-ligne">                        
                            <?php 
                            if (isset(getImageAvis($id_offre_cible, $unAvis['id_membre'])[0]['lien_fichier'])) { 
                            ?>
                                <img class="image-avis" src="/images/universel/photos/<?php echo htmlentities(getImageAvis($id_offre_cible, $unAvis['id_membre'])[0]['lien_fichier']); ?>">
                            <?php 
                            } 
                            ?>
                            <p><?php echo htmlentities(html_entity_decode(ucfirst($unAvis['commentaire']))); ?></p>
                        </div>

                        <div class="display-ligne-espace">
                            <div class="petite-mention">
                                <?php 
                                $publication = explode(' ', $dateAvis[$identifiant]['date']);
                                $datePub = explode('-', $publication[0]); 
                                ?>
                                <p><em>Écrit le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?></em></p>
                            </div>

                            <div class="display-ligne">
                                <?php
                                if (isset($_SESSION['id'])) {
                                    $userReaction = getReactionAvis($id_offre_cible, $unAvis['id_membre'], $_SESSION['id']);
                                ?>
                                    <p class="nbPouceHaut"><?php echo htmlentities($unAvis['nb_pouce_haut']); ?></p>
                                    <img src="/images/universel/icones/pouce-up<?= ($userReaction && $userReaction['nb_pouce_haut'] == 1) ? '-hover' : '' ?>.png" 
                                        class="pouce pouceHaut" 
                                        data-id-offre="<?= $id_offre_cible ?>" 
                                        data-id-membre-avis="<?= $unAvis['id_membre'] ?>" 
                                        data-id-membre-reaction="<?= $_SESSION['id'] ?>">
                                    
                                    <p class="nbPouceBas"><?php echo htmlentities($unAvis['nb_pouce_bas']); ?></p>
                                    <img src="/images/universel/icones/pouce-down<?= ($userReaction && $userReaction['nb_pouce_bas'] == 1) ? '-hover' : '' ?>.png" 
                                        class="pouce pouceBas" 
                                        data-id-offre="<?= $id_offre_cible ?>" 
                                        data-id-membre-avis="<?= $unAvis['id_membre'] ?>" 
                                        data-id-membre-reaction="<?= $_SESSION['id'] ?>">
                                <?php } else { ?>
                                    <p class="nbPouceHaut"><?php echo htmlentities($unAvis['nb_pouce_haut']); ?></p>
                                    <img src="/images/universel/icones/pouce-up.png" class="pouce pouceHaut">
                                    <p class="nbPouceBas"><?php echo htmlentities($unAvis['nb_pouce_bas']); ?></p>
                                    <img src="/images/universel/icones/pouce-down.png" class="pouce pouceBas">
                                <?php } ?>
                            </div>

                        </div>

                        <?php 
                        if(!empty(getReponse($unAvis['id_offre'], $unAvis['id_membre']))) { 
                        $reponse = getReponse($unAvis['id_offre'], $unAvis['id_membre'])
                        ?>
                            <div class="reponse">

                                <div class="display-ligne">
                                    <img src="/images/universel/icones/reponse-violet.png">
                                    <p class="titre-reponse"><?php echo htmlentities($compte['denomination']); ?></p>
                                </div>

                                <p><?php echo htmlentities(html_entity_decode(ucfirst($reponse['texte']))) ?></p>

                                <div class="display-ligne marge-reponse petite-mention">
                                    <?php 
                                    $rep = explode(' ', $reponse['date']);
                                    $dateRep = explode('-', $rep[0]); 
                                    $heureRep = explode(':', $rep[1]); 
                                    ?>
                                    <p class="indentation"><em>Répondu le <?php echo htmlentities($dateRep[2] . "/" . $dateRep[1] . "/" . $dateRep[0]); ?></em></p>
                                </div>

                            </div>

                        <?php 
                        } 
                        ?>

                    </div>  

            <?php 
                }
                $identifiant++; 
            } 
            ?>

        </section>        

        <div class="navigation display-ligne-espace">
            <button onclick="location.href='../../front/consulter-offres/'">Retour à la liste des offres</button>
            <button id="remonte" onclick="location.href='#'"><img src="/images/universel/icones/fleche-haut.png"></button>
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

        </div>
        
        <div class="footer-bottom">
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
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