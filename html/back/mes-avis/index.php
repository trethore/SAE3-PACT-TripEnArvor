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
    
    // ===== GESTION DES OFFRES ===== //
    
        // ===== Requête SQL pour récupérer les informations d'une offre ===== //
        

        $touteslesoffres = getToutesLesOffres($id_compte);
        
    
    // ===== GESTION DES IMAGES ===== //
    
        // ===== Requête SQL pour récuéprer les images d'une offre ===== //
        // $images = getIMGbyId($id_offre_cible);
    
    
    
    // ===== GESTION DES AVIS ===== //
    
        // ===== Requête SQL pour récupérer les avis d'une offre ===== //
    //     $avis = getAvis($id_offre_cible);
    
    //     // ===== Fonction qui exécute une requête SQL pour récupérer la note détaillée d'une offre de restauration ===== //
    //     $noteDetaillee = getAvisDetaille($id_offre_cible);
    
    //     // ===== Requête SQL pour récupérer les informations des membres ayant publié un avis sur une offre ===== //
    //     $membre = getInformationsMembre($id_offre_cible);
    
    //     // ===== Requête SQL pour récupérer la date de publication d'un avis sur une offre ===== //
    //     $dateAvis = getDatePublication($id_offre_cible);
    
    //     // ===== Requête SQL pour récupérer la date de visite d'une personne yant rédigé un avis sur une offre ===== //
    //     $datePassage = getDatePassage($id_offre_cible);
    
    // // ===== GESTION DES RÉPONSES ===== //
    
    //     // ===== Fonction qui exécute une requête SQL pour récupérer les réponses d'un avis d'une offre ===== //
    //     $reponse = getReponse($id_offre_cible);
    
    //     // ===== Fonction qui exécute une requête SQL pour récupérer la date de publication de la réponse à un avis sur une offre ===== //
    //     $dateReponse = getDatePublicationReponse($id_offre_cible);
    
    
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}


$typeCompte = getTypeCompte($id_compte);

switch ($typeCompte) {
    case 'proPublique':
        $reqCompte = "SELECT * from sae.compte_professionnel_publique c
            join sae._adresse a on c.id_adresse = a.id_adresse
            where id_compte = :id_compte";
        break;

    case 'proPrive':
        $reqCompte = "SELECT * from sae.compte_professionnel_prive c
            join sae._adresse a on c.id_adresse = a.id_adresse
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

$informationsBancaires;
if ($typeCompte === 'proPrive') {
    $query = "SELECT * FROM sae._mandat_prelevement_sepa INNER JOIN sae._compte_professionnel_prive ON _mandat_prelevement_sepa.id_compte_pro_prive = _compte_professionnel_prive.id_compte WHERE _compte_professionnel_prive.id_compte = ?;";
    $stmt = $conn->prepare($query);
    $stmt->execute([$detailCompte['id_compte']]);
    $informationsBancaires = $stmt->fetch(PDO::FETCH_ASSOC);
}
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
    <main class="mes-avis">
        <nav>
            <a href="/back/mon-compte">Mes infos</a>

            <a class="ici" href="/back/mes-avis">Mes avis</a> 

            <?php if ($typeCompte == 'proPrive') { ?>
            <a href="/back/mes-factures">Mes factures</a>
            <?php } ?>
            
            <a href="/se-deconnecter/index.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
        </nav>
            
        <section class="back consulter-offre-back">
            <h1>Mes Avis</h1>
            <div class="contenu-aligne-gauche">
            
           
                <?php
                    echo "<br>";
                    $nb_offres = 0;
                    foreach ($touteslesoffres as $offre) { ?>
                        <h2>
                        <?php
                        // $offre = getOffre($id_offre_cible);
                        $id_offre = $offre['id_offre'];
                        $avis = getAvis($id_offre);
                        if (!$avis) { //si l'offre n'a pas d'avis (vide) on pase a l'offre suivante
                            $nb_offres++;
                            if (count($touteslesoffres) == $nb_offres) {
                                print("Aucun avis n'a été laissé sur vos offres");
                            }else {
                                continue;
                            }
                        }

                        $nb_avis = count($avis);

                        echo $nb_avis . " avis sur l'offre : " . $offre['titre'];  ?> </h2>
                    <?php 

                        $categorie = getTypeOffre($id_offre);

                        // ===== GESTION DES AVIS ===== //
                        
                        $membre = getInformationsMembre($id_offre);
                        $datePassage = getDatePassage($id_offre);
                        $dateAvis = getDatePublication($id_offre);
                        $noteDetaillee = getAvisDetaille($id_offre);

                        

                    

                    
                        $compteur = 0;

                        foreach ($avis as $lavis) { ?>

                        <?php if($lavis['lu'] == false){ echo '<div role="tooltip" id="infobulle">Nouveau !</div>';} ?>
                            <div class="fond-blocs-avis <?php echo ($lavis['lu'] == false) ? 'avis-en-exergue' : ''; ?>">
                            
                                
                                
                                <div class="display-ligne-espace">
                                    <div class="display-ligne">
                                        <p class="titre-avis"><?php echo htmlentities($membre[$compteur]['pseudo']) ?></p>
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
                                <a href='/back/consulter-offre/index.php?id= <?php echo $id_offre ?>'> Acceder a l'avis</a>
                            </div>
                    <?php $compteur++; } }?>       
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