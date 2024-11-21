<?php
require_once('../../php/connect_params.php');
require_once('../../utils/offres-utils.php');
require_once('../../utils/auth-utils.php');
require_once('../../utils/site-utils.php');
require_once('../../utils/session-utils.php');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
if (isset($id_compte)) {
    redirectToListOffreIfNecessary($id_compte);
} else {
    redirectTo('https://redden.ventsdouest.dev/front/consulter-offres/');
} ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backListe.css">
    <link rel="stylesheet" href="/style/style_HFB.css">
    <link rel="stylesheet" href="/style/style_navPhone.css"/>
    <title>Mon compte</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/se-connecter"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <nav>
        <article>
            <h2>Mes infos</h2>
        </article>
    </nav>
    <main>
        <h1>Détails du compte</h1>
        <img src="/images/universel/icones/avatar-homme-1" alt="Avatar du profil">
        <button type="button">Importer une photo de profil</button>
        <h2>Informations personnelles</h2>
        <table>
            <tr>
                <td>Nom</td>
                <td>David Guillet</td>
            </tr>
            <tr>
                <td>Raison Sociale</td>
                <td>APELLA</td>
            </tr>
            <tr>
                <td>Adresse mail</td>
                <td>david.guillet@gmail.com</td>
            </tr>
            <tr>
                <td>Numéro de téléphone</td>
                <td>+33 7 65 47 15 56</td>
            </tr>
            <tr>
                <td>Numéro SIREN</td>
                <td>123 465 789</td>
            </tr>
            <tr>
                <td>Numéro IBAN</td>
                <td>FR54864651896416548644654654</td>
            </tr>
        </table>
    </main>
</body>
</html>