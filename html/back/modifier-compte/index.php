<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/utils/file_paths-utils.php");
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);

startSession();
$id_compte = $_SESSION["id"];

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare('SET SCHEMA \'sae\';')->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

redirectToConnexionIfNecessaryPro($id_compte);

$submitted = isset($_POST['email']);
$typeCompte = getTypeCompte($id_compte);

switch ($typeCompte) {
    case 'proPublique':
        $reqCompte = "SELECT * from sae.compte_professionnel_publique cp 
                        join sae._adresse a on cp.id_adresse = a.id_adresse 
                        where cp.id_compte = :id_compte;";
        break;

    case 'proPrive':
        $reqCompte = "SELECT * from sae.compte_professionnel_prive cp 
                        join sae._adresse a on cp.id_adresse = a.id_adresse
                        where cp.id_compte = :id_compte;";
        break;

    default:
        break;
}

if (!$submitted) {
?>
   <?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
startSession();
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT * from sae._offre where id_compte_professionnel = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
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
    <title>Modifier mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>
<body class="back compte-back-modif">

<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back" class="retourAccueil">PACT Pro</a></div>
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
        <a href="/back/liste-back" class="retourAccueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte" id="retourCompte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
    <main>
        <h1>Modification du compte</h1>
        <form action="/back/modifier-compte/" method="POST" id="myForm">
            <h2>Mon entreprise</h2>
            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><input type="text" name="denomination" id="denomination" value="<?= htmlentities($detailCompte["denomination"] ?? '');?>"></td>
                </tr>
                <?php if ($typeCompte == 'proPrive') { ?>
                <tr>
                    <td>N° SIREN</td>
                    <td><input type="text" name="siren" id="siren" value="<?= htmlentities($detailCompte["siren"]);?>"></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>A propos</td>
                    <td><input type="text" name="a_propos" id="a_propos" value="<?= htmlentities($detailCompte["a_propos"] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><input type="url" name="site" id="site" value="<?= htmlentities($detailCompte["site_web"] ?? '');?>"></td>
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
                    <td><input type="email" name="email" id="email" value="<?= htmlentities($detailCompte["email"]);?>"></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td>
                        <input type="tel" name="tel" id="tel" value="<?php 
                                        if (isset($detailCompte["tel"])) {
                                            echo htmlentities($detailCompte["tel"]);} ?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td>
                        <input type="password" name="mdp" id="mdp" placeholder="Saisissez un nouveau mot de passe">
                        <input type="hidden" name="ancien_mdp" value="<?= htmlentities($detailCompte['mot_de_passe']); ?>">
                    </td>
                </tr>
            </table>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><input type="text" name="rue" id="rue" value="<?= htmlentities($detailCompte["num_et_nom_de_voie"]);?>"></td>
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
                    <td><input type="text" name="cp" id="cp" value="<?= htmlentities($detailCompte["code_postal"]);?>"></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><input type="text" name="ville" id="ville" value="<?= htmlentities($detailCompte["ville"]);?>"></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><input type="text" name="pays" id="pays" value="<?= htmlentities($detailCompte["pays"]);?>"></td>
                </tr>
            </table>
            <?php
if ($typeCompte === 'proPrive') {
    if ($informationsBancaires == null) {
?>
            <input type="hidden" name="creer-infos-bancaires" value="true">
<?php
    }
?>
            <h2>Informations bancaires</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td><input type="text" name="nom_creancier" id="nom_creancier" value="<?php echo htmlentities($informationsBancaires['nom_creancier'] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>Identifiant</td>
                    <td><input type="text" name="id_crancier" id="id_crancier" value="<?php echo(htmlentities($informationsBancaires['id_crancier'] ?? '')); ?>"></td>
                </tr>
                <tr>
                    <td>IBAN</td>
                    <td><input type="text" name="iban_creancier" id="iban_creancier" value="<?php echo htmlentities($informationsBancaires['iban_creancier'] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>BIC</td>
                    <td><input type="text" name="bic_creancier" id="bic_creancier" value="<?php echo htmlentities($informationsBancaires['bic_creancier'] ?? '');?>"></td>
                </tr>
            </table>
<?php
}
?>
            <div>
                <a href="/back/mon-compte" id="retour">Revenir au compte</a>
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
            <p>Si vous retournez sur votre compte, vous annulez les modifications faites pour l'instant</p>
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
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>
    <?php
} else {
    $ok = true;
    switch ($typeCompte) {
        case 'proPublique':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['nom']);
            $ok = $ok && isset($_POST['prenom']);
            $ok = $ok && isset($_POST['tel']);
            $ok = $ok && isset($_POST['denomination']);
            $ok = $ok && isset($_POST['a_propos']);
            $ok = $ok && isset($_POST['site']);
            $ok = $ok && isset($_POST['rue']);
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
            $ok = $ok && isset($_POST['a_propos']);
            $ok = $ok && isset($_POST['site']);
            $ok = $ok && isset($_POST['siren']);
            $ok = $ok && isset($_POST['rue']);
            $ok = $ok && isset($_POST['cp']);
            $ok = $ok && isset($_POST['ville']);
            $ok = $ok && isset($_POST['pays']);
            break;

        default:
            $ok = false;
            break;
        }
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

        $conn->prepare('SET SCHEMA \'sae\';')->execute();
        $stmt = $conn->prepare($reqCompte);
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
        $stmt->execute();
        $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($ok) {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            switch ($typeCompte) {
                case 'proPublique':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a_propos'];
                    $site_web = $_POST['site'];
                    $street = $_POST['rue'];
                    $address_complement = $_POST['compl_adr'] ?? '';
                    $code_postal = $_POST['cp'];
                    $city = $_POST['ville'];
                    $country = $_POST['pays'];
                    if ($address_complement === '') $address_complement = null;
                    // Requete SQL pour modifier la table adresse
                    $query = "UPDATE sae._adresse 
                                set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                    where id_adresse = (select id_adresse from sae._compte_professionnel where id_compte = ?) returning id_adresse;";
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
                    
                    break;
                    
                case 'proPrive':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a_propos'];
                    $site_web = $_POST['site'];
                    $siren = $_POST['siren'];
                    $street = $_POST['rue'];
                    $address_complement = $_POST['compl_adr'] ?? '';
                    $code_postal = $_POST['cp'];
                    $city = $_POST['ville'];
                    $country = $_POST['pays'];
                    if ($address_complement === '') $address_complement = null;

                    $nomCreancier = $_POST['nom_creancier'];
                    $idCreancier = $_POST['id_crancier'];
                    $ibanCreancier = $_POST['iban_creancier'];
                    $bicCreancier = $_POST['bic_creancier'];

                    if (isset($nomCreancier) && isset($idCreancier) && isset($ibanCreancier) && isset($bicCreancier)) {
                        if (isset($_POST['creer-infos-bancaires'])) {
                            $query = 'INSERT INTO sae._mandat_prelevement_sepa
(
  rum,
  nom_creancier,
  iban_creancier,
  bic_creancier,
  id_crancier,
  nom_debiteur,
  iban_debiteur,
  bic_debiteur,
  nature_prelevement,
  periodicite,
  signature_mandat,
  date_signature,
  date_premiere_echeance,
  id_compte_pro_prive
)
VALUES
(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
                            $stmt = $conn->prepare($query);
                            $stmt->execute([uniqid(), $nomCreancier, $ibanCreancier, $bicCreancier, $idCreancier, 'TripEnArvor', '0123456789', '12345', 'récurrent', 'mensuel', uniqid(), '01-04-2025', '01-04-2025', $detailCompte['id_compte']]);
                        } else {
                            $query = 'UPDATE sae._mandat_prelevement_sepa SET nom_creancier = ?, iban_creancier = ?, bic_creancier = ?, id_crancier = ? WHERE id_compte_pro_prive = ?;';
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$nomCreancier, $ibanCreancier, $bicCreancier, $idCreancier, $detailCompte['id_compte']]);
                        }
                    }


                    // Requete SQL pour modifier la table adresse
                    $query = "UPDATE sae._adresse 
                                set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                    where id_adresse = (select id_adresse from sae._compte_professionnel where id_compte = ?) returning id_adresse;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country, $id_compte]);
                    $id_adresse = $stmt->fetch()['id_adresse'];
    
                    $conn->prepare('SET SCHEMA \'sae\';')->execute();
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
        redirectTo("/back/mon-compte");
    } ?>
<script src="/scripts/popupCompteBack.js"></script>
</body>
</html>