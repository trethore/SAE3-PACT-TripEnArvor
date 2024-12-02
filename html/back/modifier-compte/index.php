<?php 
require_once('../../php/connect_params.php');
require_once('../../utils/compte-utils.php');
require_once('../../utils/auth-utils.php');
require_once('../../utils/site-utils.php');
require_once('../../utils/session-utils.php');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
redirectToListOffreIfNecessary($id_compte);


$typeCompte = getTypeCompte($id_compte);

$reqCompte = "SELECT * from sae._compte_professionnel cp 
                join sae._compte c on c.id_compte = cp.id_compte 
                join sae._adresse a on c.id_adresse = a.id_adresse 
                where cp.id_compte = :id_compte;";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backCompte.css">
    <link rel="stylesheet" href="/style/style_backCompteModif.css">
    <link rel="stylesheet" href="/style/style_HFB.css">
    <link rel="stylesheet" href="/style/styleguide.css">
    <title>Modifier mon compte</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <?php 
            // Préparation et exécution de la requête
            $stmt = $conn->prepare($reqCompte);
            $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
            $stmt->execute();
            $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC)
        ?>
        <h1>Détails du compte</h1>
        <h2>Vue d'ensemble</h2>
        <form method="POST" id="myForm">
            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><input type="text" name="denomi" id="denomi" placeholder="<?php echo htmlentities($detailCompte["denomination"]);?>"></td>
                </tr>
                <tr>
                    <td>A propos</td>
                    <td><input type="text" name="a_propos" id="a_propos" placeholder="<?php echo htmlentities($detailCompte["a_propos"]);?>"></td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><input type="url" name="site" id="site" placeholder="<?php echo htmlentities($detailCompte["site_web"]);?>"></td>
                </tr>
            </table>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>
                        <input type="text" name="nom" id="nom" placeholder="<?php 
                                if (isset($detailCompte["nom_compte"])) {
                                    echo htmlentities($detailCompte["nom_compte"]);} ?>">
                    </td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td>
                        <input type="text" name="prenom" id="prenom" placeholder="<?php 
                                    if (isset($detailCompte["prenom"])) {
                                        echo htmlentities($detailCompte["prenom"]);} ?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><input type="email" name="email" id="email" placeholder="<?php echo htmlentities($detailCompte["email"]);?>"></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td>
                        <input type="tel" name="tel" id="tel" placeholder="<?php 
                                        if (isset($detailCompte["tel"])) {
                                            echo htmlentities($detailCompte["tel"]);} ?>"> 
                    </td>
                </tr>
                <?php if ($typeCompte == 'proPrive') {?>
                <tr>
                    <td>N° SIREN</td>
                    <td><input type="text" name="siren" id="siren" placeholder="<?php echo htmlentities($detailCompte["siren"]);?>"></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>N° IBAN</td>
                    <td><input type="text" name="iban" id="iban" placeholder="<?php echo htmlentities("à implémenter");?>"></td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td><input type="password" name="mdp" id="mdp" placeholder="<?php echo htmlentities($detailCompte["mot_de_passe"]);?>"></td>
                </tr>
            </table>
            <?php if (isset($detailCompte["id_adresse"])) { ?>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><input type="text" name="rue" id="rue" placeholder="<?php echo htmlentities($detailCompte["num_et_nom_de_voie"]);?>"></td>
                </tr>
                <tr>
                    <td>Complément d'adresse</td>
                    <td>
                        <input type="text" name="compl_adr" id="compl_adr" placeholder="<?php
                            if (isset($detailCompte["complement_adresse"])) {
                                echo htmlentities($detailCompte["complement_adresse"]);} ?>">
                    </td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><input type="text" name="cp" id="cp" placeholder="<?php echo htmlentities($detailCompte["code_postal"]);?>"></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><input type="text" name="ville" id="ville" placeholder="<?php echo htmlentities($detailCompte["ville"]);?>"></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><input type="text" name="pays" id="pays" placeholder="<?php echo htmlentities($detailCompte["pays"]);?>"></td>
                </tr>
            </table> <?php } ?>
            <div>
            <input type="submit" value="Valider les modifications">
        </div>
        </form>
        <div id="popupOverlay"></div>
        <div id="validerModifCompte">
            <h3>Valider les modifications</h3>
            <p>Voulez-vous valider les modifications apporter à votre profil ?</p>
            <div >
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Valider </button> 
            </div>
        </div>
        <div id="annulerModifCompte">
            <h3>Annuler les modifications</h3>
            <p>Voulez-vous annuler les modifications apporter à votre compte ?</p>
            <div>
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Valider </button>
            </div>
        </div>
        <div id="quitterModifCompte">
            <h3>Valider les modifications</h3>
            <p>Si vous quittez cette page, vous annulez les modifications faites pour l'instant</p>
            <div>
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Valider </button>
            </div>
        </div> 
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
    <script src="/scripts/popup.js"></script>
</body>
</html>