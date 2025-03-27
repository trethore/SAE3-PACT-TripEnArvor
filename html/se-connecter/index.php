<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

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

if (isset($_POST["email"]) && isset($_POST["mdp"])) {
    $trouve = false;
    $emailUtilisateur = $_POST["email"];
    $mdpUtilisateur = $_POST["mdp"];
    $id = -1;
    foreach ($result as $entry) {
        if ($emailUtilisateur == $entry['email'] && password_verify($mdpUtilisateur, $entry['mot_de_passe'])) {
            $id = $entry['id_compte'];
            $trouve = true;
            $_SESSION['id'] = $id;
            break;
        }
    }
    if ($trouve) {
        if (isIdMember($id)) {
            header("Location: /front/consulter-offres/");
        } else if (isIdProPrivee($id) || isIdProPublique($id)) {
            header("Location: /back/liste-back/");
        }
    } else {
        unset($_POST["email"]);
        unset($_POST["mdp"]);
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
      
        <form action="/se-connecter/" method="POST" enctype="multipart/form-data">
            <label for="email">Quelle est votre adresse mail ?</label>
            <input type="email" id="email" name="email" required />

            <section>
                <label for="mdp">Quel est votre mot de passe ?</label>
                <article>
                    <input type="checkbox" id="toggle" onclick="hidePassword()">
                    <label for="toggle">Afficher</label>
                </article>
                <a href="/resetMdp">Mot de passe oublié ?</a>
            </section>
            <input type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required />

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
            } else {x
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