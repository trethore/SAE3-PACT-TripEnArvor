<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . DEBUG_UTILS);

startSession();
if (!isset($_SESSION["id"])) {
    header("Location: /se-connecter/");
}
$id_compte = $_SESSION["id"];
redirectToConnexionIfNecessaryPro($id_compte);

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

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre NATURAL JOIN sae._compte WHERE id_compte = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll();
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
    print_r("y a un probleme");
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
    $totp->setLabel('PACT-' . $detailCompte["denomination"]);
    $totp->setIssuer('PACT');
    $qrCodeUri = $totp->getProvisioningUri();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_auth'])) {
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
            $totp->setLabel('PACT-' . $detailCompte["denomination"]);
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
    } elseif (isset($_POST['show_qr'])) {
        // Show QR code again
        $showQrModal = true;
    }
}

$statusText = $currentAuthStatus ? "Activé" : "Desactivé";
$buttonText = $currentAuthStatus ? "Afficher le QR Code" : "Activer Authentifikator";
$buttonName = $currentAuthStatus ? "show_qr" : "toggle_auth";

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
    <script src="/scripts/header.js"></script>
</head>
<body class="back compte-back">
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
    </header>
    <main>
        <nav>
            <a class="ici" href="/back/mon-compte">Mes infos</a>
            <?php
                $reqOffre = "SELECT * from sae._offre where id_compte_professionnel = :id_compte;";
                $stmtOffre = $conn->prepare($reqOffre);
                $stmtOffre->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
                $stmtOffre->execute();

                $remainingAvis = 0;

                while ($row = $stmtOffre->fetch(PDO::FETCH_ASSOC)) {
                    $avisNonLus = getLu($row['id_offre']);

                    foreach ($avisNonLus as $avis) {
                        if (!empty($avis) && empty($avis['lu'])) {
                            $remainingAvis++;
                        }
                    }
                }
            ?>
            <a href="/back/mes-avis">Mes avis</a>
            <?php if ($remainingAvis > 0) { ?>
                <span class="notification-badge"><?php echo $remainingAvis; ?></span>
            <?php } ?>
            <?php if ($typeCompte == 'proPrive') { ?>
            <a href="/back/mes-factures">Mes factures</a>
            <?php } ?>
            <a href="/se-deconnecter/index.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
        </nav>
        <section>
            <h1>Détails du compte</h1>
            <article style="display: none;">
                <img src="/images/universel/icones/avatar-homme-1.png" alt="Avatar du profil">
                <a>Importer une photo de profil</a>
            </article>
            <h2>Vue d'ensemble</h2>
            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><?php echo htmlentities($detailCompte["denomination"] ?? '');?></td>
                </tr>
                <?php if ($typeCompte == 'proPrive') {?>
                <tr>
                    <td>N° SIREN</td>
                    <td><?php echo htmlentities($detailCompte["siren"] ?? '');?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>A propos</td>
                    <td>
                        <div><?php echo htmlentities($detailCompte["a_propos"] ?? '');?></div>
                    </td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><?php echo htmlentities($detailCompte["site_web"] ?? '');?></td>
                </tr>
            </table>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td><?php echo htmlentities($detailCompte["nom_compte"] ?? '');?></td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td><?php echo htmlentities($detailCompte["prenom"] ?? '');?></td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><?php echo htmlentities($detailCompte["email"]);?></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td><?php echo htmlentities($detailCompte["tel"] ?? '');?></td>
                </tr>

            </table>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><?php echo htmlentities($detailCompte["num_et_nom_de_voie"] ?? '');?></td>
                </tr>
                <?php  if (isset($detailCompte["complement_adresse"])) { ?>
                    <tr>
                        <td>Complément d'adresse</td>
                        <td><?php echo htmlentities($detailCompte["complement_adresse"]); ?></td>
                    </tr> 
                <?php } ?>
                <tr>
                    <td>Code postal</td>
                    <td><?php echo htmlentities($detailCompte["code_postal"] ?? '');?></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><?php echo htmlentities($detailCompte["ville"] ?? '');?></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><?php echo htmlentities($detailCompte["pays"] ?? '');?></td>
                </tr>
            </table>
<?php
if ($typeCompte === 'proPrive') {
?>
            <h2>Informations bancaires</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td><?php echo htmlentities($informationsBancaires['nom_creancier'] ?? '');?></td>
                </tr>
                <tr>
                    <td>Identifiant</td>
                    <td><?php echo htmlentities($informationsBancaires['id_crancier'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>IBAN</td>
                    <td><?php echo htmlentities($informationsBancaires['iban_creancier'] ?? '');?></td>
                </tr>
                <tr>
                    <td>BIC</td>
                    <td><?php echo htmlentities($informationsBancaires['bic_creancier'] ?? '');?></td>
                </tr>
            </table>
<?php
}
?>
            <div>
                <a href="/back/modifier-compte">Modifier les informations</a>
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
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <button type="submit" name="<?php echo $buttonName; ?>" id="auth_toggle_button">
                        <?php echo htmlspecialchars($buttonText); ?>
                    </button>
                </form>
                <?php if (!empty($message)): ?>
                    <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
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
        </div>
        <div class="footer-bottom">
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>
    <script>
        // QR Code Modal functions
        function showQrModal() {
            document.getElementById('qrModalOverlay').style.display = 'block';
            document.getElementById('qrModal').style.display = 'block';
        }

        function closeQrModal() {
            document.getElementById('qrModalOverlay').style.display = 'none';
            document.getElementById('qrModal').style.display = 'none';
        }

        <?php if ($showQrModal): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showQrModal();
        });
        <?php endif; ?>
    </script>
</body>
</html>