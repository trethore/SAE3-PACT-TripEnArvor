<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backCompte.css">
    <link rel="stylesheet" href="/style/style_backCompteModif.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/styleguide.css">
    <title>Modifier mon compte</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/front/consulter-offres"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
    <?php 
                // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqCompte);
                $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
                $stmt->execute();
                $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC)
            ?>
            <h1>Détails du compte</h1>
            <article style="display: none;">
                <img src="/images/universel/icones/avatar-homme-1.png" alt="Avatar du profil">
                <a>Importer une photo de profil</a>
            </article>
            <h2>Vue d'ensemble</h2>
            <table>
                <tr>
                    <td>Dénomination Sociale</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["denomination"]);?>"></td>
                </tr>
                <tr>
                    <td>A propos</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["a_propos"]);?>"></td>
                </tr>
                <tr>
                    <td>Site web</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["site_web"]);?>"></td>
                </tr>
            </table>
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Nom</td>
                    <td>
                        <input type="text" placeholder="<?php 
                                if (isset($detailCompte["nom_compte"])) {
                                    echo htmlentities($detailCompte["nom_compte"]);} ?>">
                    </td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td>
                        <input type="text" placeholder="<?php 
                                    if (isset($detailCompte["prenom"])) {
                                        echo htmlentities($detailCompte["prenom"]);} ?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["email"]);?>"></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td>
                        <input type="text" placeholder="<?php 
                                        if (isset($detailCompte["tel"])) {
                                            echo htmlentities($detailCompte["tel"]);} ?>"> 
                    </td>
                </tr>
                <?php if ($typeCompte == 'proPrive') {?>
                <tr>
                    <td>N° SIREN</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["siren"]);?>"></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>N° IBAN</td>
                    <td><input type="text" placeholder="<?php echo htmlentities("à implémenter");?>"></td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["mot_de_passe"]);?>"></td>
                </tr>
            </table>
            <?php if (isset($detailCompte["id_adresse"])) { ?>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><input type="text" placeholder="<?php echo htmlentities($detailCompte["num_et_nom_de_voie"]);?>"></td>
                </tr>
                <tr>
                    <td>Complément d'adresse</td>
                    <?php   ?>
                    <td>
                        <input type="text" placeholder="<?php 
                            if (isset($detailCompte["complement_adresse"])) {
                                echo htmlentities($detailCompte["complement_adresse"]);} ?>">
                    </td>
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
            </table> <?php } ?>
            <div>
                <a href="/back/modifier-compte">Valider les modifications</a>
            </div>
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
</html>