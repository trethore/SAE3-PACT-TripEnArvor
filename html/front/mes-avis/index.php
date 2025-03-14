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
<body class="front compte-front">
<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/accueil/">PACT</a></div>
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
        <a href="/front/accueil/"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte/"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
                                    <?php
                                    preg_match('/"(\d{4}-\d{2}-\d{2})/', $lavis['date_publication'], $matches);
                                    $date = DateTime::createFromFormat('Y-m-d', $matches[1])->format('d/m/Y');
                                    ?>
                                    <p><span class="titre-avis"><?php echo htmlentities(html_entity_decode(ucfirst($lavis['titre_offre']))) ?></span> - Écrit le <?php echo htmlentities($date); ?></p>
                                    <div class="display-ligne">
                                        <div class="display-ligne">
                                            <?php for ($etoileJaune = 0; $etoileJaune < $lavis['note']; $etoileJaune++) { ?>
                                                <img src="/images/universel/icones/etoile-jaune.png" class="etoile_detail">
                                            <?php }
                                            for ($etoileGrise = 0; $etoileGrise < (5 - $lavis['note']); $etoileGrise++) { ?>
                                                <img src="/images/universel/icones/etoile-grise.png" class="etoile_detail">
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="display-ligne">
                                    <span><strong><?php echo htmlentities(html_entity_decode(ucfirst($lavis['titre_avis']))) ?></strong> - <?php echo htmlentities(ucfirst($lavis['contexte_visite'])); ?></span>
                                </div>
                                <div class="display-ligne-espace">
                                    <div class="petite-mention">
                                        <?php 
                                        preg_match('/"(\d{4}-\d{2}-\d{2})/', $lavis['date_visite'], $matches);
                                        $date = DateTime::createFromFormat('Y-m-d', $matches[1])->format('d/m/Y');
                                        ?>
                                        <p><em>Visité le <?php echo htmlentities($date); ?></em></p>
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