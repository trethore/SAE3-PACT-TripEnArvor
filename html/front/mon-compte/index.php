<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
startSession();
if (!isset($_SESSION["id"])) {
    header("Location: /se-connecter/");
}
$id_compte = $_SESSION["id"];
redirectToConnexionIfNecessaryMembre($id_compte);

require_once('../../utils/compte-utils.php');
require_once('../../utils/site-utils.php');

if (!function_exists('OTPHP\trigger_deprecation')) {
    function trigger_deprecation(string $package, string $version, string $message, ...$args): void {
        @trigger_error("$package $version: $message", E_USER_DEPRECATED);
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/symfony/deprecation-contracts/function.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/psr/clock/ClockInterface.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/constant_time_encoding/src/Binary.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/constant_time_encoding/src/EncoderInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/constant_time_encoding/src/Base32.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/ParameterTrait.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/OTPInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/HOTPInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/TOTPInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/FactoryInterface.php');

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/OTP.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/HOTP.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/TOTP.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/Factory.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/InternalClock.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/otphp/src/Url.php');

class_alias('OTPHP\TOTP', 'TOTP');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$reqCompte = "SELECT * from sae.compte_membre where id_compte = :id_compte;";

// auth toggle
$stmt_get = $conn->prepare("SELECT auth FROM _compte WHERE id_compte = :id");
$stmt_get->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
$stmt_get->execute();
$currentStatusRow = $stmt_get->fetch(PDO::FETCH_ASSOC);
$currentAuthStatus = false;
if (!$currentStatusRow) {
    header("Location: /se-connecter/");
}
$currentAuthStatus = (bool)$currentStatusRow['auth'];

$message = '';
$qrCodeUri = '';
$showQrModal = false;

// Get account details first since we need them for the API key
$stmt = $conn->prepare($reqCompte);
$stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
$stmt->execute();
$detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);

// Generate API key (used as secret)
$APIKey = hash('sha256', $id_compte . $detailCompte["email"] . $detailCompte["mot_de_passe"]);

$truncatedKey = substr($APIKey, 0, 32);
$AuthKey = \ParagonIE\ConstantTime\Base32::encodeUpper($truncatedKey);

// Generate TOTP object (needed for both activation and showing QR code)
if ($currentAuthStatus) {
    $totp = TOTP::create(
        $AuthKey, // Secret
        30,       // Period (30 seconds)
        'sha1',   // Digest algorithm
        6,        // Digits
        0         // Epoch (0 means current time)
    );
    $totp->setLabel('PACT-' . $detailCompte["pseudo"]);
    $totp->setIssuer('PACT');
    $qrCodeUri = $totp->getProvisioningUri();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_auth'])) {
    // Only allow activation, not deactivation
    if (!$currentAuthStatus) {
        $newAuthStatus = true;
        
        $totp = TOTP::create(
            $AuthKey, // Secret
            30,       // Period (30 seconds)
            'sha1',   // Digest algorithm
            6,        // Digits
            0         // Epoch (0 means current time)
        );
        $totp->setLabel('PACT-' . $detailCompte["pseudo"]);
        $totp->setIssuer('PACT');
        $qrCodeUri = $totp->getProvisioningUri();
        $showQrModal = true;
        
        $stmt_update = $conn->prepare("UPDATE _compte SET auth = :new_auth WHERE id_compte = :id");
        $stmt_update->bindParam(':new_auth', $newAuthStatus, PDO::PARAM_BOOL);
        $stmt_update->bindParam(':id', $id_compte, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            $currentAuthStatus = $newAuthStatus;
        }
    }
}

$statusText = $currentAuthStatus ? "Activé" : "Desactivé";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="/style/style.css">
    <title>Mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
    <script src="/scripts/header.js"></script>
</head>

<body class="front compte-front">
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();
        $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
        $stmt->execute();
        $offres = $stmt->fetchAll();
        $dbh = null;
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des titres : " . $e->getMessage();
    }
    ?>

    <!-- QR Code Modal -->
    <div class="modal-overlay" id="qrModalOverlay" style="<?= $showQrModal ? 'display: block;' : 'display: none;' ?>"></div>
    <div class="qr-modal" id="qrModal" style="<?= $showQrModal ? 'display: block;' : 'display: none;' ?>">
        <h3>Configurer l'authentification à deux facteurs</h3>
        <p>Scannez ce QR code avec Google Authenticator:</p>
        <?php if ($showQrModal): ?>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qrCodeUri) ?>" alt="QR Code">
        <?php else: ?>
            <div id="qrCodeContainer" style="width: 200px; height: 200px; margin: 0 auto; background: white; padding: 10px;"></div>
        <?php endif; ?>
        <p>Ou entrez manuellement ce code:<br>
        <strong><?= rtrim($AuthKey, '=') ?></strong></p>
        <button onclick="closeQrModal()">Fermer</button>
    </div>

    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
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
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <nav>
            <a class="ici" href="/front/mon-compte">Mes infos</a>
            <a href="/front/mes-avis/">Mes avis</a>
            <a href="/se-deconnecter/index.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
        </nav>
        <section>
            <h1>Détails du compte</h1>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Pseudo</td>
                    <td><?php echo htmlentities($detailCompte["pseudo"]); ?></td>
                </tr>
                <tr>
                    <td>Nom</td>
                    <td><?php echo htmlentities($detailCompte["nom_compte"] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td><?php echo htmlentities($detailCompte["prenom"] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><?php echo htmlentities($detailCompte["email"]); ?></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td><?php echo htmlentities($detailCompte["tel"] ?? ''); ?></td>
                </tr>
            </table>
            <div>
                <a href="/front/modifier-compte">Modifier les informations</a>
            </div>
            <div>
                <script>
                    function copyAPIKey() {
                        var apiKey = "<?php echo addslashes($APIKey); ?>";
                        navigator.clipboard.writeText(apiKey);
                        alert("Clé d'API Tchatator copiée dans le presse-papier!");
                    }
                </script>
                <h2>Clé d'accès au Tchatator : </h2>
                <button onclick="copyAPIKey()" id="apibutton">Cliquez ici !</button>
            </div>
            <div class="container-authentificator">
                <h2>Authentificateur</h2>
                <p>L'Authentificateur est: <span class="status-<?php echo $currentAuthStatus ? 'enabled' : 'disabled'?>"> <?php echo htmlspecialchars($statusText); ?></span></p>
                <?php if (!$currentAuthStatus): ?>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <button type="submit" name="toggle_auth" id="auth_toggle_button">
                            Activer Authentifikator
                        </button>
                    </form>
                <?php endif; ?>
                <?php if (!empty($message)): ?>
                    <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
            </div>
            <button onclick="delCompteMembre(event, <?= $id_compte ?>)" id="delButton">Supprimer le compte</button>
        </section>
        <div id="popupOverlay" style="display: none;"></div>
        <div id="validerDeleteCompte" style="display: none;">
            <h3>Supprimer le compte</h3>
            <p>Voulez-vous vraiment supprimer votre compte ?</p>
            <div>
                <button id="boutonAnnuler">Annuler</button>
                <button id="boutonValider">Supprimer</button>
            </div>
        </div>
    </main>
    <script>
        const popupOverlay = document.getElementById("popupOverlay");
        const popupValider = document.getElementById("validerDeleteCompte");
        const boutonAnnuler = document.getElementById("boutonAnnuler");
        const boutonValider = document.getElementById("boutonValider");
        const boutonDel = document.getElementById("delButton");

        let compteId = null;

        // Ouvre la popup et stocke l'ID du compte
        function delCompteMembre(event, id) {
            event.preventDefault();
            compteId = id;
            popupOverlay.style.display = "block";
            popupValider.style.display = "flex";
        }

        // Ferme la popup sans supprimer
        boutonAnnuler.addEventListener("click", function() {
            popupOverlay.style.display = "none";
            popupValider.style.display = "none";
        });

        // Supprime le compte en AJAX
        boutonValider.addEventListener("click", function() {
            if (compteId != null) {
                fetch("supprimer_compte.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `id_compte=${compteId}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.includes("Compte supprimé avec succès")) {
                            alert(data);
                            window.location.href = "https://redden.ventsdouest.dev/front/accueil/";
                        }
                    })
                    .catch(error => console.error("Erreur :", error));
            }

            popupOverlay.style.display = "none";
            popupValider.style.display = "none";
        });

        function closeQrModal() {
            document.getElementById('qrModalOverlay').style.display = 'none';
            document.getElementById('qrModal').style.display = 'none';
        }

        <?php if ($showQrModal): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('qrModalOverlay').style.display = 'block';
            document.getElementById('qrModal').style.display = 'block';
        });
        <?php endif; ?>
    </script>
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