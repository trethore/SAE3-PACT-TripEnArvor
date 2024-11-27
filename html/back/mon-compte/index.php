<?php
require_once('../../php/connect_params.php');
require_once('../../utils/compte-utils.php');
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
}

$typeCompte = getTypeCompte($id_compte);

$reqCompte = "SELECT * from sae._compte_professionnel cp 
                join sae._compte c on c.id_compte = cp.id_compte 
                join sae._adresse a on c.id_adresse = a.id_adresse 
                where cp.id_compte = :id_compte;";

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backCompte.css">
    <link rel="stylesheet" href="/style/style_HFB.css">
    <link rel="stylesheet" href="/style/styleguide.css">
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
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <article>
            <a class="ici" href="/back/mon-compte">Mes infos</a>
            <a href="/back/se-connecter">Se déconnecter</a>
        </article>
        <section>
            <?php 
                // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqCompte);
                $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
                $stmt->execute();
                $detailCompte = $stmt->fetch(PDO::FETCH_COLUMN)
            ?>
            <h1>Détails du compte</h1>
            <article>
                <img src="/images/universel/icones/avatar-homme-1.png" alt="Avatar du profil">
                <a>Importer une photo de profil</a>
            </article>
            <h2>Vue d'ensemble</h2>
            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><?php echo htmlentities($detailCompte["denomination"]);?></td>
                </tr>
                <tr>
                    <td>A propos</td>
                    <td>
                        <div><?php echo htmlentities($detailCompte["a_propos"]);?></div>
                    </td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><?php echo htmlentities($detailCompte["site_web"]);?></td>
                </tr>
            </table>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td><?php echo htmlentities($detailCompte["nom_compte"]);?></td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td><?php echo htmlentities($detailCompte["prenom"]);?></td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><?php echo htmlentities($detailCompte["email"]);?></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td><?php echo htmlentities($detailCompte["tel"]);?></td>
                </tr>
                <?php if ($typeCompte == 'proPrive') {?>
                <tr>
                    <td>N° SIREN</td>
                    <td><?php echo htmlentities($detailCompte["siren"]);?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>N° IBAN</td>
                    <td><?php echo htmlentities("à implémenter");?></td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td><?php echo htmlentities($detailCompte["mot_de_passe"]);?></td>
                </tr>
            </table>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><?php echo htmlentities($detailCompte["num_et_nom_de_voie"]);?></td>
                </tr>
                <tr>
                    <td>Complément d'adresse</td>
                    <td><?php echo htmlentities($detailCompte["complement_adresse"]);?></td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><?php echo htmlentities($detailCompte["code_postal"]);?></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><?php echo htmlentities($detailCompte["ville"]);?></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><?php echo htmlentities($detailCompte["pays"]);?></td>
                </tr>
            </table>
            <div>
                <a>Modifier les informations</a>
            </div>
        </section>
    </main>
</body>
</html>