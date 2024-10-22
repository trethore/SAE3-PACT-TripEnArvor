<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="connecter">
    <!-- Logo -->
    <img src="images/universel/logo/Logo_couleurs.png">
    <!-- Main -->
    <main>
        <!-- Titres -->
        <h1>Se connecter</h1>
        <h2>Vous n'avez pas de compte ? <a href="#">Créez votre compte</a></h2>
        <br>
        <br>
        <br>
        <!-- Formulaire -->
        <!--<form action="crea.php" method="POST" enctype="multipart/form-data">-->
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
        <!--</form>-->
        <br>
        <br>
        <br>
        <!-- Boutons -->
        <input type="submit" value="Connexion">
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