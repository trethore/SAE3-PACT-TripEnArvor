<?php
$driver = "mysql"; // Remplacez par "pgsql" pour PostgreSQL, "sqlite" pour SQLite, etc.
$server = "localhost"; // L'hôte de la base de données (souvent "localhost")
$dbname = "votre_base_de_donnees"; // Le nom de votre base de données
$user = "votre_utilisateur"; // Le nom d'utilisateur de la base de données
$pass = "votre_mot_de_passe"; // Le mot de passe de la base de données

try {
    $stmt = $dbh->prepare('SELECT titre FROM sae._offre');
    $stmt->execute();
    $titres = $stmt->fetchAll(PDO::FETCH_COLUMN); // Récupère uniquement la colonne "titre"
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/styleguide.css" />
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/style_navPhone.css"/>
    <title>Liste de vos offres</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php 
                foreach ($titres as $titre) { // Parcourt les titres récupérés
                    echo "<option value=\"{$titre}\"></option>";
                }
                ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/se-connecter"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
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


        <!-- Barre en bas du footer incluse ici -->

        </div>
        <div class="footer-bottom">
        Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        Conditions générales - ©
        Redden's, Inc.
        </div>
    </footer>

    

</body>

    <div class="telephone-nav">
        <div class="bg"></div>
        <div class="nav-content">
        <div class = "btOn">
            <img src="/images/frontOffice/icones/accueil.png">
            </div>
            <img src="/images/frontOffice/icones/chercher.png">
            <img src="/images/frontOffice/icones/utilisateur.png">
        </div>
    </div>
</html>