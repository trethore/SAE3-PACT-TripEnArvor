<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);

require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();
if (!isset($_SESSION["id"])) {
    header("Location: /se-connecter/");
}
$id_compte = $_SESSION["id"];

require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/compte-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
startSession();
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre NATURAL JOIN sae._compte WHERE id_compte = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
       
    
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur à la ligne " . $e->getLine() . " : " . $e->getMessage();
    die();
}


$typeCompte = getTypeCompte($id_compte);

switch ($typeCompte) {
    case 'membre':
        $reqCompte = "SELECT * from sae.compte_membre
            where id_compte = :id_compte";
        break;
    default:
        break;
}

// Préparation et exécution de la requête
$stmt = $conn->prepare($reqCompte);
$stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
$stmt->execute();
$detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);


$reqAvis = 'SELECT _avis.id_offre, _offre.titre titre_offre, note, _avis.titre titre_avis, commentaire, nb_pouce_haut, nb_pouce_bas, contexte_visite, date_visite, date_publication
FROM sae._avis 
INNER JOIN sae._date date_publication
ON date_publication.id_date = _avis.publie_le
INNER JOIN sae._date date_visite
ON date_visite.id_date = _avis.visite_le
INNER JOIN sae._offre
ON _avis.id_offre = _offre.id_offre
WHERE id_membre = ?;';
$stmt = $conn->prepare($reqAvis);
$stmt->execute([$id_compte]);
$mesAvis = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($mesAvis as $key => $avis) {
    if (avisEstDetaille($avis['id_offre'], $id_compte)) {
        $mesAvis[$key]['note_detaillee'] = getNotesDetailleeAvis($avis['id_offre'], $id_compte);
    }
}

$reqMembre = 'SELECT * FROM sae.compte_membre WHERE id_compte = ?';
$stmt = $conn->prepare($reqMembre);
$stmt->execute([$id_compte]);
$membre = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style.css">
    <title>Mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="back compte-back">
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <!-- <pre>
<?php
// print_r($mesAvis);
?>
    </pre> -->
    <main class="mes-avis">
        <nav>
            <a href="/front/mon-compte">Mes infos</a>

            <a class="ici" href="/front/mes-avis">Mes avis</a> 

            <?php if ($typeCompte == 'proPrive') { ?>
            <a href="/back/mes-factures">Mes factures</a>
            <?php } ?>
            
            <a href="/se-deconnecter/" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
        </nav>
            
        <section class="front consulter-offre-front">
            <h1>Mes Avis</h1>
            <div class="contenu-aligne-gauche">
        

                <?php

                        $compteur = 0;

                        foreach ($mesAvis as $lavis) { ?>

                        
                            <div class="fond-blocs-avis">                                
                                
                                <div class="display-ligne-espace">
                                    <div class="display-ligne">
                                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']);
                                                                echo ' '; ?></p>
                                        <div class="display-ligne">
                                            <?php for ($etoileJaune = 0; $etoileJaune != $lavis['note']; $etoileJaune++) { ?>
                                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                            <?php }
                                                        for ($etoileGrise = 0; $etoileGrise != (5 - $lavis['note']); $etoileGrise++) { ?>
                                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="display-ligne">
                                    <?php $passage = explode(' ', $datePassage[$compteur]['date']);
                                                        $datePass = explode('-', $passage[0]); ?>
                                    <p><strong><?php echo htmlentities(html_entity_decode(ucfirst($lavis['titre']))) ?> - Visité le <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> - <?php echo htmlentities(ucfirst($lavis['contexte_visite'])); ?></strong></p>
                                </div>

                                <?php  ?>


                                <div class="display-ligne-espace">
                                    <div class="petite-mention">
                                        <?php $publication = explode(' ', $dateAvis[$compteur]['date']);
                                                        $datePub = explode('-', $publication[0]); ?>
                                        <p><em>Écrit le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?></em></p>
                                    </div>
                                </div>
                                <br>
                                <a href="/back/consulter-offre/index.php?id= <?php echo $id_offre . '#avis' ?>"> Voir à l&#39;avis </a>
                            </div>
                            </article>
                        <?php $compteur++;
                        } ?>
                    <br>
            <?php       }
                        } ?>

            <?php
            foreach ($touteslesoffres as $offre) {
                $id_offre = $offre['id_offre'];
                $categorie = getTypeOffre($id_offre);

                // ===== GESTION DES AVIS ===== //

                $membre = getInformationsMembre($id_offre);
                $datePassage = getDatePassage($id_offre);
                $dateAvis = getDatePublication($id_offre);
                $noteDetaillee = getAvisDetaille($id_offre);

                $reponses = getAllReponses($id_offre);
                $compteur = 0;
                echo "avis non lu";
                foreach ($avis as $lavis) {
                    while ($compteur != count($avis)) {
                        if (in_array($lavis['id_membre'], $avis_non_lu['id_membre'])) {
                            echo "avis non lus";
            ?>
                            <div class="fond-blocs-avis <?php echo ($lavis['lu'] == false) ? 'avis-en-exergue' : ''; ?>">


                                <div class="display-ligne-espace">
                                    <div class="display-ligne">
                                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']);
                                                                echo ' '; ?></p>
                                        <div class="display-ligne">
                                            <?php for ($etoileJaune = 0; $etoileJaune != $lavis['note']; $etoileJaune++) { ?>
                                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                            <?php }
                                            for ($etoileGrise = 0; $etoileGrise != (5 - $lavis['note']); $etoileGrise++) { ?>
                                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="display-ligne">
                                    <?php $passage = explode(' ', $datePassage[$compteur]['date']);
                                    $datePass = explode('-', $passage[0]); ?>
                                    <p><strong><?php echo htmlentities(html_entity_decode(ucfirst($lavis['titre']))) ?> - Visité le <?php echo htmlentities($datePass[2] . "/" . $datePass[1] . "/" . $datePass[0]); ?> - <?php echo htmlentities(ucfirst($lavis['contexte_visite'])); ?></strong></p>
                                </div>

                                <?php  ?>


                                <div class="display-ligne-espace">
                                    <div class="petite-mention">
                                        <?php $publication = explode(' ', $dateAvis[$compteur]['date']);
                                        $datePub = explode('-', $publication[0]); ?>
                                        <p><em>Écrit le <?php echo htmlentities($datePub[2] . "/" . $datePub[1] . "/" . $datePub[0]); ?></em></p>
                                    </div>
                                </div>
                                <br>
                                <a href="/back/consulter-offre/index.php?id= <?php echo $id_offre . '#avis' ?>"> Accéder à l&#39;avis </a>
                            </div>
                        <?php $compteur++;
                        }
                    }
                }
                echo "avis non repondu";
                foreach ($avis as $lavis) {
                    while ($compteur != count($avis)) {
                        if (in_array($lavis['id_membre'], $avis_non_repondu['id_membre'])) {
                            echo "avis non lus";
                        ?>
                            <div class="fond-blocs-avis <?php echo ($lavis['lu'] == false) ? 'avis-en-exergue' : ''; ?>">


                                <div class="display-ligne-espace">
                                    <div class="display-ligne">
                                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']);
                                                                echo ' '; ?></p>
                                        <div class="display-ligne">
                                            <?php for ($etoileJaune = 0; $etoileJaune != $lavis['note']; $etoileJaune++) { ?>
                                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                            <?php }
                                            for ($etoileGrise = 0; $etoileGrise != (5 - $lavis['note']); $etoileGrise++) { ?>
                                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="display-ligne">
                                    <p><?php echo($lavis['commentaire']); ?></p>
                                </div>

                                <br>
                                <a href="/front/consulter-offre/index.php?id=<?php echo $lavis['id_offre'] . '#avis' ?>">Accéder à l&#39;avis</a>
                            </div>
                    <?php $compteur++; }
                echo "<br>";
                ?>       
            </div>
            
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
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>
</body>
</html>