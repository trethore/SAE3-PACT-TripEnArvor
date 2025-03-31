<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/email-utils.php');

$reset_link_base = "https://redden.ventsdouest.dev/resetMdpForm.php";

// Initialisation des variables
$message = '';

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invalide.";
    } else {
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();

            // Vérifier si l'email existe dans la base de données
            $stmt = $dbh->prepare("SELECT id_compte FROM sae._compte WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $id_compte = $user['id_compte'];

                // Générer un token unique et le stocker en base de données
                $token = bin2hex(random_bytes(32)); // Générer un token plus long et aléatoire
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $dbh->prepare("INSERT INTO password_reset_tokens (id_compte, token, expiry_date) VALUES (:id_compte, :token, :expiry_date)");
                $stmt->bindParam(':id_compte', $id_compte);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expiry_date', $expiry_date);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    // Log de l'erreur SQL
                    error_log("Erreur SQL lors de l'insertion du token : " . $e->getMessage());
                    $message = "Erreur interne. Veuillez réessayer plus tard.";
                    $dbh = null;
                    // Afficher le message d'erreur à l'utilisateur
                    echo "<p>" . htmlspecialchars($message) . "</p>";
                    exit; // Stop l'exécution du script
                }

                // Construction du lien de réinitialisation
                $reset_link = $reset_link_base . "?token=" . urlencode($token);

                // Préparation de l'email
                $subject = "Réinitialisation de votre mot de passe";
                $body = "<p>Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='" . htmlspecialchars($reset_link) . "'>" . htmlspecialchars($reset_link) . "</a></p>";
                $alt_body = "Copiez ce lien dans votre navigateur : " . $reset_link;

                // Envoi de l'email (avec la fonction améliorée)
                if (sendEmail($email, $subject, $body, $alt_body)) {
                    $message = "Un email de réinitialisation a été envoyé à votre adresse.";
                } else {
                    $message = "Erreur lors de l'envoi de l'email. Veuillez réessayer plus tard.";
                    // Log de l'erreur d'envoi d'email
                    error_log("Erreur lors de l'envoi de l'email à : " . $email);
                }
            } else {
                $message = "Aucun utilisateur trouvé avec cette adresse email.";
            }

            $dbh = null;

        } catch (PDOException $e) {
            // Log de l'erreur de connexion à la base de données
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            $message = "Erreur interne. Veuillez réessayer plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié ?</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>
<body class="connecter">
    <header>
        <a href="/front/accueil/">
            <img src="/images/universel/logo/Logo_couleurs.png" alt="Logo de la PACT">
        </a>
    </header>
    <main>
        <h1>Mot de passe oublié ?</h1>
        <p>Entrez votre adresse email pour réinitialiser votre mot de passe.</p>

        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="#" method="POST">
            <label for="email">Adresse email :</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Réinitialiser le mot de passe">
        </form>
    </main>
</body>
</html>