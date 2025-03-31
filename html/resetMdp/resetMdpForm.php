<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);

$message = '';

// Traitement du formulaire de réinitialisation (si soumis)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();

            // Récupérer l'ID de l'utilisateur à partir du token
            $stmt = $dbh->prepare("SELECT id_compte FROM _password_reset_tokens WHERE token = :token AND expiry_date > NOW()");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $reset_token = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reset_token) {
                $id_compte = $reset_token['id_compte'];

                // Hasher le nouveau mot de passe
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Mettre à jour le mot de passe dans la table `_compte`
                $stmt = $dbh->prepare("UPDATE _compte SET mot_de_passe = :password WHERE id_compte = :id_compte");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':id_compte', $id_compte);
                $stmt->execute();

                // Supprimer le token
                $stmt = $dbh->prepare("DELETE FROM _password_reset_tokens WHERE token = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                $message = "Votre mot de passe a été modifié avec succès.";
                // Rediriger vers la page de connexion
                header("Location: /se-connecter/"); // Ajustez le chemin si nécessaire
                exit();

            } else {
                $message = "Token invalide ou expiré.";
            }

            $dbh = null;

        } catch (PDOException $e) {
            $message = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }
}
// Affichage du formulaire (si le token est valide)
elseif (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();

        // Vérifier si le token est valide
        $stmt = $dbh->prepare("SELECT id_compte FROM _password_reset_tokens WHERE token = :token AND expiry_date > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $reset_token = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset_token) {
            $id_compte = $reset_token['id_compte'];
            // Formulaire pour le nouveau mot de passe
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Réinitialiser votre mot de passe</title>
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
                    <h1>Réinitialiser votre mot de passe</h1>
                    <?php if ($message): ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>
                    <form action="/resetMdp/resetMdpForm.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <label for="new_password">Nouveau mot de passe :</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <input type="submit" value="Modifier le mot de passe">
                    </form>
                </main>
            </body>
            </html>
            <?php
        } else {
            $message = "Token invalide ou expiré.";
        }
        $dbh = null;
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
// Si aucun token n'est présent dans l'URL
else {
    $message = "Token manquant.";
}

if ($message && !($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password']))): ?>
    <h2><?= htmlspecialchars($message); ?></h2>
<?php endif; ?>