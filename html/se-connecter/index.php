<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);

startSession();

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT email, mot_de_passe, id_compte from _compte;');
    $stmt->execute();
    $result = $stmt->fetchAll();
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

// Variables de contrôle
$loginFailed = false;
$otpFailed = false;
$message = '';

// Traitement du formulaire OTP
if (isset($_POST["otp"])) {
    if (!isset($_SESSION['pending_otp'])) {
        $otpFailed = true;
        $message = "La session OTP a expiré, veuillez recommencer.";
    } else {
        $id_compte = $_SESSION['pending_otp'];
        $enteredOTP = $_POST["otp"];

        // Récupération du dernier OTP généré pour ce compte
        $sql = "SELECT id_otp, code_otp, expire_le, utiliser, tentatives, cree_le FROM _compte_otp 
                WHERE id_compte = :id_compte 
                ORDER BY cree_le DESC LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
        $stmt->execute();
        $otpData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($otpData) {
            if ($otpData['utiliser']) {
                $otpFailed = true;
                $message = "Ce code OTP a déjà été utilisé.";
            } else if (new DateTime() > new DateTime($otpData['expire_le'])) {
                $otpFailed = true;
                $message = "Le code OTP a expiré.";
            } else if ($otpData['tentatives'] >= 3) {
                $otpFailed = true;
                $message = "Trop de tentatives échouées.";
            } else if (password_verify($enteredOTP, $otpData['code_otp'])) {
                // Marquer l'OTP comme utilisé (ou le supprimer)
                $sqlUpdate = "UPDATE _compte_otp SET utiliser = 1 WHERE id_otp = :id_otp";
                $stmtUpdate = $dbh->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':id_otp', $otpData['id_otp'], PDO::PARAM_INT);
                $stmtUpdate->execute();

                // Connexion effective
                $_SESSION['id'] = $id_compte;
                unset($_SESSION['pending_otp']);
                if (isIdMember($id_compte)) {
                    header("Location: /front/consulter-offres/");
                    exit();
                } else if (isIdProPrivee($id_compte) || isIdProPublique($id_compte)) {
                    header("Location: /back/liste-back/");
                    exit();
                }
            } else {
                // Incrémenter le compteur de tentatives
                $sqlUpdate = "UPDATE _compte_otp SET tentatives = tentatives + 1 WHERE id_otp = :id_otp";
                $stmtUpdate = $dbh->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':id_otp', $otpData['id_otp'], PDO::PARAM_INT);
                $stmtUpdate->execute();
                $otpFailed = true;
                $message = "Code OTP invalide.";
            }
        } else {
            $otpFailed = true;
            $message = "Aucun code OTP n'a été généré, veuillez réessayer.";
        }
    }
}
// Traitement du formulaire de connexion
else if (isset($_POST["email"]) && isset($_POST["mdp"])) {
    $trouve = false;
    $emailUtilisateur = $_POST["email"];
    $mdpUtilisateur = $_POST["mdp"];
    $id = -1;
    foreach ($result as $entry) {
        if ($emailUtilisateur == $entry['email'] && password_verify($mdpUtilisateur, $entry['mot_de_passe'])) {
            $id = $entry['id_compte'];
            $trouve = true;
            break;
        }
    }
    if ($trouve) {
        $hashedOTP = generateOTP();
        // Sauvegarder l'OTP hashé en base (validité d'1 minute)
        saveOTP($dbh, $id, $hashedOTP);

        // Pour la démonstration, on affiche le code OTP à l'écran
        $message = "Un code OTP vous a été envoyé (pour la démo, le code est : " . $plainOTP . ")";
        // En production, vous devez envoyer le code via email ou SMS de façon sécurisée.

        // Stocker l'ID du compte en attente de validation OTP
        $_SESSION['pending_otp'] = $id;
    } else {
        $loginFailed = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>
<body class="connecter">
    <!-- Logo -->
    <header>
        <a href="/front/accueil/">
            <img src="/images/universel/logo/Logo_couleurs.png" alt="Logo de la PACT">
        </a>
    </header>
    <!-- Main -->
    <main>
        <!-- Titres -->
        <h1>Se connecter</h1>
        <h2>Vous n'avez pas de compte ? <a href="/creer-compte/">Créez votre compte</a></h2>

        <!-- Formulaire -->
        <?php
        if (isset($_POST["email"]) && isset($_POST["mdp"])) {
            $trouve = false;
            $emailUtilisateur = $_POST["email"];
            $mdpUtilisateur = $_POST["mdp"];
            $id = -1;
            foreach ($result as $entry) {
                if ($emailUtilisateur == $entry['email'] && password_verify($mdpUtilisateur, $entry['mot_de_passe']) ) {
                    $id = $entry['id_compte'];
                    $trouve = true;
                    $_SESSION['id'] = $id;
                    break;
                }
            }

            if ($trouve) {
                if (isIdMember($id)) {
                    redirectTo('/front/consulter-offres/');
                } else if (isIdProPrivee($id) || isIdProPublique($id)) {
                    redirectTo('/back/liste-back/');
                }
            } else {
                unset($_POST["email"]);
                unset($_POST["mdp"]);
                $loginFailed = true;

            }
        }
        ?>
        <form action="/se-connecter/" method="POST" enctype="multipart/form-data">
            <label for="email">Quelle est votre adresse mail ?</label>
            <input type="email" id="email" name="email" required/>

            <section>
                <label for="mdp">Quel est votre mot de passe ?</label>
                <article>
                    <input type="checkbox" id="toggle" onclick="hidePassword()">
                    <label for="toggle">Afficher</label>
                </article>
            </section>
            <input type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required/>

            <!-- Boutons -->
            <input type="submit" value="Connexion">
        </form>
      
    </main>
    <!-- Script pour afficher ou non le mot de passe -->
    <script>
        function hidePassword() {
            var x = document.getElementById("mdp");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function redirect(lien) {
            window.location.href = lien;
        }
    </script>
    <?php if (isset($loginFailed) && $loginFailed): ?>
    <script>
        alert("Invalid email or password.");
        document.getElementById('email').value = "";
        document.getElementById('mdp').value = "";
    </script>
    <?php endif; ?>
</body>
</html>