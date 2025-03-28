<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
startSession();
if (!isset($_SESSION["id_auth"])) {
    header("Location: /se-connecter/");
    exit();
}

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
$id_compte = $_SESSION["id_auth"];

// OTPHP Library Includes
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


$conn = null;
$twoFaFailed = false; 

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $conn->exec("SET SCHEMA 'sae'");

    $reqCompte = "SELECT email, mot_de_passe FROM sae._compte WHERE id_compte = :id_compte";
    $stmt = $conn->prepare($reqCompte);
    $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
    $stmt->execute();
    $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detailCompte) {
        session_destroy();
        header("Location: /se-connecter/");
        exit();
    }

    $APIKey = hash('sha256', $id_compte . $detailCompte["email"] . $detailCompte["mot_de_passe"]);
    $truncatedKey = substr($APIKey, 0, 32); 
    $AuthKey = \ParagonIE\ConstantTime\Base32::encodeUpper($truncatedKey);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['auth_code'] ?? '';

        if (!empty($code) && preg_match('/^[0-9]{6}$/', $code)) {
            try {
                 $otp = OTPHP\TOTP::createFromSecret($AuthKey);
                 if ($otp->verify($code)) {
                     unset($_SESSION['id_auth']); 
                     $_SESSION['id'] = $id_compte; 
                     if (isIdMember($id_compte)) {
                        header("Location: /front/consulter-offres/");
                    } else if (isIdProPrivee($id_compte) || isIdProPublique($id_compte)) {
                        header("Location: /back/liste-back/");
                    }
                    exit();
                 } else {
                     error_log("code: 1");
                     $twoFaFailed = true;
                 }
            } catch (Exception $e) {
                 error_log("OTPHP Error: " . $e->getMessage()); 
                 $twoFaFailed = true; 
            }

        } else {
            error_log("code: 3");
            $twoFaFailed = true;
        }
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage()); 
    die("Une erreur interne est survenue. Veuillez réessayer plus tard."); 
} finally {
    $conn = null;
}

?>

<!DOCTYPE html>
<html lang="fr"> 

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code</title> 
    <link rel="stylesheet" href="/style/style.css">
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="connecter authenticator-verify"> 
    <header>
        <a href="/front/accueil/">
            <img src="/images/universel/logo/Logo_couleurs.png" alt="Logo de la PACT">
        </a>
    </header>
    <main>
        <h1>Vérification à deux facteurs</h1> 
        <h2>Entrez le code généré par votre application d'authentification.</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="auth-code">Code d'authentification</label>
            <input
                type="text"  
                id="auth-code"
                name="auth_code"
                required
                maxlength="6"  
                pattern="[0-9]{6}" 
                inputmode="numeric"
                autocomplete="one-time-code" 
                placeholder="123456"
            />
            <input type="submit" value="Vérifier le code">
        </form>

    </main>
    <?php ?>
    <?php if (isset($twoFaFailed) && $twoFaFailed): ?>
        <script>
            alert("Le code d'authentification est invalide ou a expiré.");
        </script>
    <?php endif; ?>
</body>

</html>