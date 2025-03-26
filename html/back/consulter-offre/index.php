<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

date_default_timezone_set('Europe/Paris');
$id_offre_cible = intval($_GET['id']);

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

// ===== GESTION DU FORMULAIRE DE MISE EN LIGNE / HORS LIGNE ===== //

// ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise hors ligne existe pour une offre ===== //
    $dateMHL = getDateOffreHorsLigne($id_offre_cible);

// ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise en ligne existe pour une offre ===== //
    $dateMEL = getDateOffreEnLigne($id_offre_cible);

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

// ===== GESTION DES TYPES ===== //

    // ===== Requête SQL pour récupérer le type d'une offre ===== //
    $categorie = getTypeOffre($id_offre_cible);

// ===== GESTION DES MISES HORS LIGNE ET EN LIGNE ===== //

    // ===== Requête SQL pour vérifier si une offre est hors ligne ===== //
    $dateMiseHorsLigne = isOffreHorsLigne($id_offre_cible);

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
    <title><?php echo htmlentities(html_entity_decode(ucfirst($offre['titre'] ?? "Pas de titre disponible"))) ?></title>
    <script src="/scripts/header.js"></script>
    <script src="/scripts/carousel.js"></script>
    <script src="/scripts/popupOffreBack.js"></script>
    <script src="/scripts/blacklist.js"></script>
    <script src="/scripts/reponse.js"></script>
</head>

<body class="back consulter-offre-back">
    
    <header id="header" data-id-offre="<?php echo htmlentities($id_offre_cible); ?>">
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" alt="Logo de la PACT">
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" alt="Rechercher"></button>
            <input  autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($of as $o) { ?>
                    <option value="<?php echo htmlspecialchars($o['titre']); ?>" data-id="<?php echo $o['id_offre']; ?>">
                        <?php echo htmlspecialchars($o['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" alt="Accueil"></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" alt="Mon compte"></a>
    </header>

    <main id="body">

        <section class="fond-blocs zone-boutons">

            <?php
            if (getCompteTypeAbonnement(intval($_GET['id'])) == 'premium') {
            ?>
                <div class="display-ligne-espace">
            <?php
            }
            ?>
            
                <div class="display-ligne">
                    <?php 
                    $isMiseEnLigne = ($dateMEL > $dateMHL) || is_null($dateMHL);
                    $actionText = $isMiseEnLigne ? "Mettre hors ligne" : "Mettre en ligne";
                    ?>

                    <form method="post" enctype="multipart/form-data" class="bouton-modif-mise" onsubmit="validerDate(event, <?php echo $id_offre_cible; ?>)">
                        <button id="boutonMHL-MEL" type="submit" name="action"><?php echo $actionText; ?></button>
                    </form>

                    <div class="bouton-modif-mise">
                        <button onclick="location.href='/back/modifier-offre/index.php?id=<?php echo htmlentities($id_offre_cible); ?>'">Modifier l'offre</button>
                    </div>
                </div>

                <?php
                if (getCompteTypeAbonnement(intval($_GET['id'])) == 'premium') {
                ?>
                    <div class="display-ligne">
                        <?php
                        for ($nb_jetons_pleins = 0 ; $nb_jetons_pleins != $offre['nb_jetons'] ; $nb_jetons_pleins++) {
                        ?>
                            <img class="jeton" src="/images/universel/icones/jeton-plein.png" alt="Jeton plein">
                        <?php
                        }
                        for ($nb_jetons_vides = 0 ; $nb_jetons_vides != (3 - $offre['nb_jetons']) ; $nb_jetons_vides++) {
                        ?>
                            <img class="jeton" src="/images/universel/icones/jeton-vide.png" alt="Jeton vide">
                        <?php
                            }
                        ?>
                        <div>
                            <p class="petite-mention"><em><?php echo htmlentities($offre['nb_jetons']); ?> jeton(s) de blacklistage restant(s)</em></p>
                            <?php
                            if (getOffre($id_offre_cible)['nb_jetons'] < 3) {
                            ?>
                                <p>prochain jeton dans <?php echo htmlentities(ceil(max(0, (strtotime($offre['jeton_perdu_le']) + 30 * 86400 - time()) / 86400))); ?> jour(s)</p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
            <?php
                }
            if (getCompteTypeAbonnement(intval($_GET['id'])) == 'premium') {
            ?>
                </div>
            <?php
            }
            ?>

        </section> 

        <section class="fond-blocs bordure pur">

            <h1><?php echo htmlentities(html_entity_decode(ucfirst($offre['titre'] ?? "Pas de titre disponible"))) ?></h1>

            <div class="carousel">

                <div class="carousel-slides">
                    <?php
                    foreach ($images as $image) {
                    ?>
                        <div class="slide">
                            <img src="/images/universel/photos/<?php echo htmlentities($image) ?>" alt="Photo de l'offre">
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
                                <img class="etoile" src="/images/frontOffice/etoile-pleine.png" alt="Étoile jaune">
                            <?php 
                            }
                            if ($demiEtoile) {      
                            ?>
                                <img class="etoile" src="/images/frontOffice/etoile-moitie.png" alt="Demi étoile">
                            <?php 
                            }
                            for ($i = 0; $i < $etoilesVides; $i++) { 
                            ?>
                                <img class="etoile" src="/images/frontOffice/etoile-vide.png" alt="Étoile grise">
                        <?php 
                            }
                        } 
                        ?>
                        <p><a href="#avis"><?php echo htmlentities($nombreNote) . " avis"; ?></a></p>
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
                    } 
                ?>
                    <p><?php echo implode(' ', $adresseComplete) . " - "; ?>
                <?php 
                } else { 
                ?>
                    <p>Pas d'adresse disponible</p>
                <?php 
                } 
                setlocale(LC_TIME, 'fr_FR.UTF-8'); 
                $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                $jour_actuel = $jours[date('w')];
                $ouverture = "Pas d'information sur les créneaux d'ouverture";
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

        <section class="double-blocs">

            <div class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <?php 
                    if (!empty($tags)) {
                        foreach ($tags as $tag) { 
                    ?>
                            <li><?php echo htmlentities($tag['nom_tag']) ?></li>
                    <?php 
                        }
                    } else { 
                    ?>
                    <p>Pas de tags disponibles</p>
                    <?php 
                    } 
                    ?>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">

                <div class="display-ligne-espace">
                    <h2>À propos de <?php echo htmlentities($offre['titre'] ?? "Pas de titre disponible"); ?></h2> 
                    <a href="<?php echo htmlentities($offre['site_web']); ?>">Lien vers le site</a>
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
                        ?>
                    <?php 
                    case "Spectacle": 
                    ?>
                        <p>Durée du spectacle : <?php echo htmlentities( floor($spectacle['duree'] / 60) . "h " . $spectacle['duree'] % 60) . "min"; ?></p>
                        <p>Capacité de la salle : <?php echo htmlentities($spectacle['capacite']); ?> personnes</p>
                        <?php 
                        $event = explode(' ', $spectacle['date']);
                        $dateEvent = explode('-', $event[0]); 
                        ?>
                        <p>Date de l'évènement : <?php echo htmlentities($dateEvent[2] . "/" . $dateEvent[1] . "/" . $dateEvent[0]); ?></p>
                        <?php 
                        break; 
                        ?>
                    <?php 
                    case "Parc attraction": 
                    ?>
                        <p>Nombre d'attractions : <?php echo htmlentities($attraction['nb_attractions']); ?></p>

                        <div class="display-ligne-espace">
                            <p>Âge minimum : <?php echo htmlentities($attraction['age_min']); ?> ans</p>
                            <a href="<?php echo htmlentities($attraction['plan']); ?>" download="Plan" target="blank">Télécharger le plan du parc</a>
                        </div>

                        <?php 
                        break; 
                        ?>
                    <?php 
                    case "Restauration": 
                    ?>

                        <div class="display-ligne-espace">
                            <p>Gamme de prix : <?php echo htmlentities($restaurant['gamme_prix']) ?></p>
                            <a href="<?php echo htmlentities($restaurant['carte']) ?>" download="Carte" target="blank">Télécharger la carte du restaurant</a>
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
                        foreach ($tarifs as $unTarif) { 
                            if ($unTarif['nom_tarif'] != "nomtarif1") { 
                                if (!empty($unTarif['nom_tarif'])) {
                        ?>
                                    <tr>
                                        <td><?php echo htmlentities($unTarif['nom_tarif']); ?></td>
                                        <td><?php echo htmlentities($unTarif['prix']) . " €"; ?></td>
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
                    <h2>Note moyenne </h2>
                    <?php 
                    $etoilesPleines = floor($noteMoyenne);
                    $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                    $etoilesVides = 5 - $etoilesPleines - $demiEtoile;
                    for ($i = 0; $i < $etoilesPleines; $i++) { 
                    ?>
                        <img class="etoile" src="/images/frontOffice/etoile-pleine.png" alt="Étoile jaune">
                    <?php 
                    }
                    if ($demiEtoile) { 
                    ?>
                        <img class="etoile" src="/images/frontOffice/etoile-moitie.png" alt="Demi étoile">
                    <?php 
                    }
                    for ($i = 0; $i < $etoilesVides; $i++) { 
                    ?>  
                        <img class="etoile" src="/images/frontOffice/etoile-vide.png" alt="Étoile grise">
                <?php 
                    }
                } 
                ?>
                <p>(<?php echo htmlentities($nombreNote) . ' avis'; ?>)</p>
            </div>

            <div class="petite-mention margin-0">
                <p><em>Ces avis sont l'opinion subjective des membre de la PACT et non les avis de la PACT. Les avis sont soumis à des vérifications de la part de la PACT.</em></p>
            </div>

        <?php 
        $avisSansReponse = [];
        $avisAvecReponse = [];

        foreach ($avis as $index => $unAvis) {
            if (!empty($reponse[$index]['texte'])) {
                $avisAvecReponse[] = ['avis' => $unAvis, 'index' => $index];
            } else {
                $avisSansReponse[] = ['avis' => $unAvis, 'index' => $index];
            }
        }

        function afficherAvis($avisGroupe, $membre, $datePassage, $categorie, $noteDetaillee, $id_offre_cible, $dateAvis, $compte, $driver, $server, $dbname, $user, $pass){
            $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            foreach ($avisGroupe as $item) {
                $unAvis = $item['avis'];
                $identifiant = $item['index'];
                $stmt = $pdo->prepare("SELECT lu FROM sae._avis WHERE id_membre = :id_membre AND id_offre = :id_offre");
                $stmt->execute(['id_membre' => $unAvis['id_membre'], 'id_offre' => $unAvis['id_offre']]);
                $consulted = $stmt->fetchColumn();
                if (!$consulted) {
                    $updateStmt = $pdo->prepare("UPDATE sae._avis SET lu = true WHERE id_membre = :id_membre AND id_offre = :id_offre");
                    $updateStmt->execute(['id_membre' => $unAvis['id_membre'],'id_offre' => $unAvis['id_offre']]);
                }
                if (empty(getDateBlacklistage($unAvis['id_offre'], $membre[$identifiant]['id_compte']))) { 
        ?>
                    <div class="fond-blocs-avis">
                <?php 
                } else { 
                ?>
                    <div class="fond-blocs-avis-blackliste">
                <?php 
                } 
                ?>
                        <div class="display-ligne-espace">

                            <div class="display-ligne">
                                <p class="titre-avis"><?php echo htmlentities($membre[$identifiant]['pseudo']); ?></p>
                                <div class="display-ligne">
                                    <?php 
                                    for ($etoileJaune = 0; $etoileJaune != $unAvis['note']; $etoileJaune++) { 
                                    ?>
                                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail" alt="Étoile jaune">
                                    <?php 
                                    }
                                    for ($etoileGrise = 0; $etoileGrise != (5 - $unAvis['note']); $etoileGrise++) { 
                                    ?>
                                        <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail" alt="Étoile grise">
                                    <?php 
                                    } 
                                    ?>
                                </div>
                            </div>

                            <button class="menu-button" onclick="afficherMenu(event, this, <?php echo $identifiant; ?>)"  data-id-offre="<?php echo $unAvis['id_offre'] ?>"data-id-membre="<?php echo $membre[$identifiant]['id_compte']; ?>">
                                <img src="/images/universel/icones/trois-points-orange.png" alt="Menu déroulant">
                            </button>

                            <?php
                            if (empty(getDateBlacklistage($unAvis['id_offre'], $membre[$identifiant]['id_compte']))) { 
                            ?>
                                <div class="popup-menu" id="popup-menu-<?php echo $identifiant; ?>">
                                    <ul>
                                        <?php
                                        if (isset($_SESSION['id'])) {
                                        ?>
                                            <li onclick="confirmerSignaler(this, <?php echo $identifiant; ?>)" data-id-offre="<?php echo htmlentities($id_offre_cible); ?>" data-id-signale="<?php echo htmlentities($membre[$identifiant]['id_compte']); ?>" data-id-signalant="<?php echo htmlentities($_SESSION['id']); ?>">Signaler</li>
                                        <?php
                                        }
                                        if ((getCompteTypeAbonnement(intval($_GET['id'])) == 'premium') && (getOffre($id_offre_cible)['nb_jetons'] > 0)) {
                                        ?>
                                            <li onclick="confirmerBlacklister(this, <?php echo $identifiant; ?>)" data-id-offre="<?php echo htmlentities($id_offre_cible); ?>" data-id-membre="<?php echo htmlentities($membre[$identifiant]['id_compte']); ?>">Blacklister</li>
                                        <?php 
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="confirmation-popup-signaler" id="confirmation-popup-signaler" style="display: none;">

                                <div>
                                    <p>Signaler l'avis de <?php echo htmlentities($membre[$identifiant]['pseudo']) ?></p>
                                    <form id="signalement-form">
                                        <label>
                                            <input type="radio" name="motif" value="Il contient des propos inappropriés">Il contient des propos inappropriés
                                        </label><br>
                                        <label>
                                            <input type="radio" name="motif" value="Il ne décrit pas une expérience personnelle">Il ne décrit pas une expérience personnelle
                                        </label><br>
                                        <label>
                                            <input type="radio" name="motif" value="Il s'agit d'un doublon publié par le même membre">Il s'agit d'un doublon publié par le même membre
                                        </label><br>
                                        <label>
                                            <input type="radio" name="motif" value="Il contient des informations fausses ou trompeuses">Il contient des informations fausses ou trompeuses
                                        </label><br>
                                    </form>
                                    <button id="confirmer-signaler" onclick="validerSignaler(<?php echo $identifiant; ?>)">Signaler</button>
                                    <button onclick="annulerSignaler()">Annuler</button>
                                </div>

                            </div>

                            <div class="confirmation-popup" id="confirmation-popup" style="display: none;">

                                <div class="confirmation-content">
                                    <p>Êtes-vous sûr de vouloir blacklister cet avis ?</p>
                                    <button id="confirmer-blacklister" onclick="validerBlacklister(<?php echo $identifiant; ?>)">Blacklister</button>
                                    <button onclick="annulerBlacklister()">Annuler</button>
                                </div>

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
                    <div class="display-ligne">
                        <?php 
                        foreach ($noteDetaillee as $n) {
                            if (($n['id_membre'] == $unAvis['id_membre']) && ($n['id_offre'] == $unAvis['id_offre'])) { 
                        ?>
                                <p><?php echo htmlentities($n['nom_note']) . " : "; ?></p>
                                <?php 
                                for ($etoileJaune = 0; $etoileJaune != $n['note']; $etoileJaune++) { 
                                ?>
                                    <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail" alt="Étoile jaune">
                                <?php 
                                }
                                for ($etoileGrise = 0; $etoileGrise != (5 - $n['note']); $etoileGrise++) { 
                                ?>
                                    <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail" alt="Étoile grise">
                                <?php 
                                } 
                                ?>
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
                        <img class="image-avis" src="/images/universel/photos/<?php echo htmlentities(getImageAvis($id_offre_cible, $unAvis['id_membre'])[0]['lien_fichier']); ?>" alt="Image de l'avis">
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
                        <p><?php echo htmlentities($unAvis['nb_pouce_haut']); ?></p><img src="/images/universel/icones/pouce-up.png" class="pouce" alt="Pouce haut">
                        <p><?php echo htmlentities($unAvis['nb_pouce_bas']); ?></p><img src="/images/universel/icones/pouce-down.png" class="pouce" alt="Pouce bas">
                    </div>

                </div>

                <?php 
                if (!empty(getReponse($unAvis['id_offre'], $unAvis['id_membre']))) { 
                    $reponse = getReponse($unAvis['id_offre'], $unAvis['id_membre'])
                ?>
                    <div class="reponse">

                        <div class="display-ligne">
                            <img src="/images/universel/icones/reponse-orange.png" alt="">
                            <p class="titre-reponse"><?php echo htmlentities($compte['denomination']); ?></p>
                        </div>

                        <p><?php echo htmlentities(html_entity_decode(ucfirst(getReponse($unAvis['id_offre'], $unAvis['id_membre'])['texte']))); ?></p>

                        <div class="display-ligne marge-reponse petite-mention">
                            <?php 
                            $rep = explode(' ', $reponse['date']);
                            $dateRep = explode('-', $rep[0]); 
                            ?>
                            <p class="indentation"><em>Répondu le <?php echo htmlentities($dateRep[2] . "/" . $dateRep[1] . "/" . $dateRep[0]); ?></em></p>
                        </div>

                    </div>

                    <?php 
                    } else { 
                        if (empty(getDateBlacklistage($unAvis['id_offre'], $membre[$identifiant]['id_compte']))) { 
                    ?>
                            <form id="reponse-form-<?php echo $identifiant; ?>" class="avis-form" onsubmit="validerReponse(event, <?php echo $identifiant; ?>, <?php echo $id_offre_cible; ?>, <?php echo $unAvis['id_membre']; ?>)">
                                <p class="titre-avis">Répondre à <span id="pseudo-membre"><?php echo $membre[$identifiant]['pseudo']; ?></span></p>

                                <div class="display-ligne">
                                    <textarea id="texte-reponse-<?php echo $identifiant; ?>" name="reponse" placeholder="Merci pour votre retour ..." required></textarea><br>
                                </div>

                                <button type="submit">Répondre</button>
                            </form>
                    <?php 
                        }
                    } 
                    ?>

                </div>

            <?php 

            } 
        }
        afficherAvis($avisAvecReponse, $membre, $datePassage, $categorie, $noteDetaillee, $id_offre_cible, $dateAvis, $compte, $driver, $server, $dbname, $user, $pass);
        afficherAvis($avisSansReponse, $membre, $datePassage, $categorie, $noteDetaillee, $id_offre_cible, $dateAvis, $compte, $driver, $server, $dbname, $user, $pass);
        ?>

        </section>                
         
        <div class="navigation display-ligne-espace">
            <button onclick="location.href='../../back/liste-back/'">Retour à la liste des offres</button>
            <button id="remonte" onclick="location.href='#'"><img src="/images/universel/icones/fleche-haut.png" alt="Retour en haut"></button>
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

</body>

</html>