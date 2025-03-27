<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

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

$reqCompte = "SELECT * from sae._compte
                where id_compte = :id_compte;";

$stmt = $conn->prepare($reqCompte);
$stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
$stmt->execute();
$detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);

$APIKey = hash('sha256', $id_compte . $detailCompte["email"] . $detailCompte["mot_de_passe"]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['auth_code'];

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

<body class="authenticator-verify"> 
    <header>
        <a href="/front/accueil/">
            <img src="/images/universel/logo/Logo_couleurs.png" alt="Logo de la PACT">
        </a>
    </header>
    <main>
        <h1>Vérification à deux facteurs</h1> 
        <h2>Entrez le code généré par votre application d'authentification.</h2>

        <form action="/verify-2fa/" method="POST"> 
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