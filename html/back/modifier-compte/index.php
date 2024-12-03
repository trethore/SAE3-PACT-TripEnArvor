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
redirectToConnexionIfNecessary($id_compte);

$submitted = isset($_POST['email']);
$typeCompte = getTypeCompte($id_compte);

$reqCompte = "SELECT * from sae._compte_professionnel cp 
                join sae._compte c on c.id_compte = cp.id_compte 
                join sae._adresse a on c.id_adresse = a.id_adresse 
                where cp.id_compte = :id_compte;";
?>
<!DOCTYPE html>
<html lang="fr">
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
<?php
if (!$submitted) {
?>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back" class="retourAccueil">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/back/liste-back" class="retourAccueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte" id="retourCompte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <?php 
            // Préparation et exécution de la requête
            $stmt = $conn->prepare($reqCompte);
            $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
            $stmt->execute();
            $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <h1>Détails du compte</h1>
        <h2>Mon entreprise</h2>
        <form action="/back/modifier-compte" method="POST" id="myForm">

            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><input type="text" name="denomination" id="denomination" value="<?php echo htmlentities($detailCompte["denomination"]);?>"></td>
                </tr>
                <?php if ($typeCompte == 'proPrive') {?>
                <tr>
                    <td>N° SIREN</td>
                    <td><input type="text" name="siren" id="siren" value="<?php echo htmlentities($detailCompte["siren"]);?>"></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>A propos</td>
                    <td><input type="text" name="a_propos" id="a_propos" value="<?php echo htmlentities($detailCompte["a_propos"]);?>"></td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><input type="url" name="site" id="site" value="<?php echo htmlentities($detailCompte["site_web"]);?>"></td>
                </tr>
            </table>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>
                        <input type="text" name="nom" id="nom" value="<?php 
                                if (isset($detailCompte["nom_compte"])) {
                                    echo htmlentities($detailCompte["nom_compte"]);} ?>">
                    </td>
                </tr>
                <tr>
                    <td>Prénom</td>
                    <td>
                        <input type="text" name="prenom" id="prenom" value="<?php 
                                    if (isset($detailCompte["prenom"])) {
                                        echo htmlentities($detailCompte["prenom"]);} ?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><input type="email" name="email" id="email" value="<?php echo htmlentities($detailCompte["email"]);?>"></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td>
                        <input type="tel" name="tel" id="tel" value="<?php 
                                        if (isset($detailCompte["tel"])) {
                                            echo htmlentities($detailCompte["tel"]);} ?>"> 
                    </td>
                </tr>
                <tr style="display: none;">
                    <td>N° IBAN</td>
                    <td><input type="text" name="iban" id="iban" value="<?php echo htmlentities("à implémenter");?>"></td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td>
                        <input type="password" name="mdp" id="mdp" placeholder="Saisissez un nouveau mot de passe">
                        <input type="hidden" name="ancien_mdp" value="<?php echo htmlentities($detailCompte['mot_de_passe']); ?>">
                    </td>
                </tr>
            </table>
            <?php if (isset($detailCompte["id_adresse"])) { ?>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><input type="text" name="rue" id="rue" value="<?php echo htmlentities($detailCompte["num_et_nom_de_voie"]);?>"></td>
                </tr>
                <tr>
                    <td>Complément d'adresse</td>
                    <td>
                        <input type="text" name="compl_adr" id="compl_adr" value="<?php
                            if (isset($detailCompte["complement_adresse"])) {
                                echo htmlentities($detailCompte["complement_adresse"]);} ?>">
                    </td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><input type="text" name="cp" id="cp" value="<?php echo htmlentities($detailCompte["code_postal"]);?>"></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><input type="text" name="ville" id="ville" value="<?php echo htmlentities($detailCompte["ville"]);?>"></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><input type="text" name="pays" id="pays" value="<?php echo htmlentities($detailCompte["pays"]);?>"></td>
                </tr>
            </table> <?php } ?>
            <div>
            <input type="submit" value="Valider les modifications">
        </div>
        </form>
        <div id="popupOverlay" style="display: none;"></div>
        <div id="validerModifCompte" style="display: none;">
            <h3>Valider les modifications</h3>
            <p>Voulez-vous valider les modifications apporter à votre profil ?</p>
            <div >
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Valider </button> 
            </div>
        </div>
        <div id="annulerModifCompte" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Voulez-vous annuler les modifications apporter à votre compte ?</p>
            <div>
                <button id="boutonReprendre"> Reprendre </button>
                <button id="boutonQuitter"> Quitter </button>
            </div>
        </div>
        <div id="popupRetourAccueil" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Si vous retournez à l'accueil, vous annulez les modifications faites pour l'instant</p>
            <div>
                <button id="boutonReprendreAccueil"> Reprendre </button>
                <button id="boutonRetourAccueil"> Quitter </button>
            </div>
        </div> 
        <div id="popupRetourCompte" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Si vous retournez sur votre compte, vous annulez les modifications faites pour l'instant</p>
            <div>
                <button id="boutonReprendreCompte"> Reprendre </button>
                <button id="boutonRetourCompte"> Quitter </button>
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
    <?php
} else {
    echo "<pre>";
    echo "Formulaire soumis\n";
    print_r($_POST);
    echo "Valeur de \$ok : " . ($ok ? "true" : "false") . "\n";
    exit;
    
    $ok = true;
    switch ($typeCompte) {
        case 'proPublique':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['nom']);
            $ok = $ok && isset($_POST['prenom']);
            $ok = $ok && isset($_POST['tel']);
            $ok = $ok && isset($_POST['denomination']);
            $ok = $ok && isset($_POST['a-propos']);
            $ok = $ok && isset($_POST['site']);
            $ok = $ok && isset($_POST['rue']);
            $ok = $ok && isset($_POST["compl_adr"]);
            $ok = $ok && isset($_POST['cp']);
            $ok = $ok && isset($_POST['ville']);
            $ok = $ok && isset($_POST['pays']);
            break;

        case 'proPrive':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['nom']);
            $ok = $ok && isset($_POST['prenom']);
            $ok = $ok && isset($_POST['tel']);
            $ok = $ok && isset($_POST['denomination']);
            $ok = $ok && isset($_POST['a-propos']);
            $ok = $ok && isset($_POST['site']);
            $ok = $ok && isset($_POST['siren']);
            $ok = $ok && isset($_POST['rue']);
            $ok = $ok && isset($_POST["compl_adr"]);
            $ok = $ok && isset($_POST['cp']);
            $ok = $ok && isset($_POST['ville']);
            $ok = $ok && isset($_POST['pays']);
            break;

        default:
            $ok = false;
            break;
        }
        print_r($ok);
        // Récupération des données du formulaire
        $nouveauMotDePasse = $_POST['mdp'] ?? '';
        $ancienMotDePasse = $_POST['ancien_mdp'] ?? '';
        
        // Traitement
        if (!empty($nouveauMotDePasse)) {
            // Si un nouveau mot de passe a été fourni, on le crypte
            $motDePasseFinal = password_hash($nouveauMotDePasse, PASSWORD_BCRYPT);
        } else {
            // Sinon, on conserve l'ancien mot de passe
            $motDePasseFinal = $ancienMotDePasse;
        }  

        $email = $_POST['email'];
        $password = $_POST['mdp'];
        $name = $_POST['nom'];
        $first_name = $_POST['prenom'];
        $tel = $_POST['tel'];
    
        if ($ok) {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            switch ($typeCompte) {
                case 'proPublique':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a-propos'];
                    $site_web = $_POST['site'];
                    $street = $_POST['rue'];
                    $address_complement = $_POST['compl_adr'];
                    $code_postal = $_POST['cp'];
                    $city = $_POST['ville'];
                    $country = $_POST['pays'];
                    if ($address_complement === '') $address_complement = null;
                    // Requete SQL pour modifier la table adresse
                    $query = "UPDATE sae._adresse 
                                set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                    where id_adresse = (select id_adresse from sae._compte where id_compte = ?) returning id_adresse;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country, $id_compte]);
                    $id_adresse = $stmt->fetch()['id_adresse'];
    
                    // Requete SQL pour modifier la vue compte_professionnel_publique
                    $query = "UPDATE sae.compte_professionnel_publique 
                                set (nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web) 
                                    = (?, ?, ?, ?, ?, ?, ?, ?, ?)
                                where id_compte = ?;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$name, $first_name, $email, $tel, $motDePasseFinal, $id_adresse, $denomination, $a_propos, $site_web, $id_compte]);
                    
                    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/debug-utils.php');
                    printInConsole("test");
                    echo "<pre> toto </pre>";
                    break;
                    
                case 'proPrive':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a-propos'];
                    $site_web = $_POST['site'];
                    $siren = $_POST['siren'];
                    $street = $_POST['rue'];
                    $address_complement = $_POST['compl_adr'];
                    $code_postal = $_POST['cp'];
                    $city = $_POST['ville'];
                    $country = $_POST['pays'];
                    if ($address_complement === '') $address_complement = null;
                    // Requete SQL pour modifier la table adresse
                    $query = "UPDATE sae._adresse 
                                set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                    where id_adresse = (select id_adresse from sae._compte where id_compte = ?) returning id_adresse;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country, $id_compte]);
                    $id_adresse = $stmt->fetch()['id_adresse'];
    
                    // Requete SQL pour modifier la vue compte_professionnel_prive
                    $query = "UPDATE sae.compte_professionnel_prive 
                                set (nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web, siren) 
                                    = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                where id_compte = ?;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$name, $first_name, $email, $tel, $motDePasseFinal, $id_adresse, $denomination, $a_propos, $site_web, $siren, $id_compte]);
                    break;
                default:
                    $ok = false;
                    break;
                }
        }   
} ?>
    <script src="/scripts/popupCompte.js"></script>
</body>
</html>