<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);


$emailError = ''; // Message d'erreur par défaut

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
if (!isset($id_compte) || !isIdMember($id_compte)) {
    redirectTo("https://redden.ventsdouest.dev/front/consulter-offres/");
}
$submitted = isset($_POST['email']);
$typeCompte = getTypeCompte($id_compte);

$reqCompte = "SELECT * from sae.compte_membre
        where id_compte = :id_compte;";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="/style/style.css">
    <title>Modifier mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
    <script src="/scripts/header.js"></script>
</head>

<body class="front compte-front-modif">
    <?php
    if (!$submitted) {
    ?>
        <?php
        require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();
            $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
            $stmt->execute();
            $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
            $dbh = null;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des titres : " . $e->getMessage();
        }
        ?>

        <header>
            <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
            <div class="text-wrapper-17"><a href="/front/consulter-offres" class="retourAccueil">PACT Pro</a></div>
            <div class="search-box">
                <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
                <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
                <datalist id="cont">
                    <?php foreach ($offres as $offre) { ?>
                        <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                            <?php echo htmlspecialchars($offre['titre']); ?>
                        </option>
                    <?php } ?>
                </datalist>

            </div>
            <a href="/front/accueil" class="retourAccueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
            <a href="/front/mon-compte" id="retourCompte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
        </header>
        <main>
            <?php
            // Préparation et exécution de la requête
            $stmt = $conn->prepare($reqCompte);
            $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
            $stmt->execute();
            $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC); ?>

            <h1>Modification du compte</h1>
            <form action="/front/modifier-compte/" method="POST" id="myForm">
                <h2>Informations personnelles</h2>
                <table>
                    <tr>
                        <td>Pseudo</td>
                        <td>
                            <input type="text" name="pseudo" id="pseudo" value="<?= htmlentities($detailCompte["pseudo"] ?? ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Nom</td>
                        <td>
                            <input type="text" name="nom" id="nom" value="<?= htmlentities($detailCompte["nom_compte"] ?? ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Prénom</td>
                        <td>
                            <input type="text" name="prenom" id="prenom" value="<?= htmlentities($detailCompte["prenom"] ?? ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Adresse mail</td>
                        <td><input type="email" name="email" id="email" value="<?= htmlentities($detailCompte["email"] ?? ''); ?>" class="<?= $emailError ? 'error' : ''; ?>">
                            <?php if ($emailError): ?>
                                <p class="error"><?= $emailError; ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>N° de téléphone</td>
                        <td>
                            <input type="tel" name="tel" id="tel" value="<?= htmlentities($detailCompte["tel"] ?? ''); ?>">
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
                <div>
                    <a href="/front/mon-compte" id="retour">Revenir au compte</a>
                    <input type="submit" value="Valider les modifications">
                </div>
            </form>
            <div id="popupOverlay" style="display: none;"></div>
            <div id="validerModifCompte" style="display: none;">
                <h3>Valider les modifications</h3>
                <p>Voulez-vous valider les modifications apporter à votre profil ?</p>
                <div>
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
    <?php
    } else {
        $ok = true;
        switch ($typeCompte) {
            case 'membre':
                $ok = $ok && isset($_POST['email']);
                $ok = $ok && isset($_POST['tel']);
                $ok = $ok && isset($_POST['pseudo']);
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
        $pseudo = $_POST['pseudo'];
        $password = $_POST['mdp'];
        $name = $_POST['nom'];
        $first_name = $_POST['prenom'];
        $tel = $_POST['tel'];

        if ($ok) {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            switch ($typeCompte) {
                case 'membre':
                    // Vérification de l'existence d'un email
                    $queryCheckEmail = "SELECT id_compte FROM sae._compte WHERE email = :email AND id_compte != :id_compte";
                    $stmtCheckEmail = $conn->prepare($queryCheckEmail);
                    $stmtCheckEmail->execute(['email' => $email, 'id_compte' => $id_compte]);
                    $existingAccount = $stmtCheckEmail->fetch();


                    if ($existingAccount) {
                        echo "<script>alert('Cet email est déjà utilisé par un autre compte.');</script>";
                    } else {
                        // Mise à jour des informations si l'email est unique
                        // Requete SQL pour modifier la vue compte_membre
                        $query = "UPDATE sae.compte_membre 
                                    set (pseudo, nom_compte, prenom, email, tel, mot_de_passe) 
                                        = (?, ?, ?, ?, ?, ?)
                                    where id_compte = ?;";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$pseudo, $name, $first_name, $email, $tel, $motDePasseFinal, $id_compte]);
                        redirectTo("/front/mon-compte"); // Redirection en cas de succès
                        exit;
                    }
                    break;

                default:
                    $ok = false;
                    break;
            }
        }
        redirectTo("/front/mon-compte");
    } ?>
    <script src="/scripts/popupCompteFront.js"></script>

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
                <div class="btOn">
                    <img width="400" height="400" src="/images/frontOffice/icones/utilisateur.png">
                </div>
            </a>
        </div>
    </div>
</body>

</html>