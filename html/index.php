<?php
/*include('../../php/connect_params.php');
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare('SELECT email from compte');
    $stmt->execute();
    $result = $stmt->fetchAll();
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    $result = ["pierre.lechat8@gmail.com", "bob.bob@gmail.com", "pikdev.gopi@gmail.com"];
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="/style/style-se-connecter.css">
</head>
<body class="connecter">
    <!-- Logo -->
    <img src="/images/universel/logo/Logo_couleurs.png">
    <!-- Main -->
    <main>
        <!-- Titres -->
        <h1>Se connecter</h1>
        <h2>Vous n'avez pas de compte ? <a href="#">Créez votre compte</a></h2>
        <br>
        <br>
        <br>
        <!-- Formulaire -->
        <?php
        if (isset($_POST["email"])) {
            $trouve = false;
            $emailUtilisateur = $_POST["email"];
            $mdp = $_POST["mdp"];

            foreach ($result as $email) {
                if ($emailUtilisateur == $email) {
                    $trouve = true;
                }
            }

            if ($trouve) {
                echo "Utilisateur trouvé";
            } else {
                echo "Utilisateur non trouvé";
            }
        } else {
        ?>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <label for="email">Quelle est votre adresse mail ?</label>
            <input type="email" id="email" name="email" required/>
            <br>
            <section>
                <label for="mdp">Quel est votre mot de passe ?</label>
                <article>
                    <input type="checkbox" id="toggle" onclick="myFunction()">
                    <label for="toggle">Afficher</label>
                </article>
            </section>
            <input type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required/>
            <br>
            <br>
            <br>
            <!-- Boutons -->
            <a href="/back/liste/back"><input type="submit" value="Connexion"></a>
        </form>
        <?php
        }
        ?>
    </main>
    <!-- Script pour afficher ou non le mot de passe -->
    <script>
        function myFunction() {
            var x = document.getElementById("mdp");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>
</html>