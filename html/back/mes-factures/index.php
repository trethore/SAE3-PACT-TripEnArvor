<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/compte-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/auth-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

/*try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}*/

startSession();
$id_compte = $_SESSION["id"];
redirectToListOffreIfNecessary($id_compte);

$factures = [
    [
        "date" => "12/09/2024",
        "nom_offre" => "Côté plage"
    ],
    [
        "date" => "04/09/2024",
        "nom_offre" => "La cité Médiévale"
    ],
    [
        "date" => "15/08/2024",
        "nom_offre" => "Côté plage"
    ],
    [
        "date" => "04/08/2024",
        "nom_offre" => "La cité Médiévale"
    ],
    [
        "date" => "15/07/2024",
        "nom_offre" => "Côté plage"
    ],
    [
        "date" => "04/07/2024",
        "nom_offre" => "La cité Médiévale"
    ],
]

?>
<!DOCTYPE html> 
<html lang="fr">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_mesFactures.css">
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
        <nav>
            <a href="/back/mon-compte">Mes infos</a>
            <a class="ici" href="/back/mes-factures">Mes factures</a>
            <a href="/back/se-connecter">Se déconnecter</a>
        </nav>
        <section>
            <?php 
                // Préparation et exécution de la requête
                /*$stmt = $conn->prepare($reqCompte);
                $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
                $stmt->execute();
                $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC)*/
            ?>
            <h1>Mes factures</h1>
            <ul>
                <?php
                    foreach ($factures as $facture) {
                        ?>
                        <p class="facture hidden">Facture du <?php echo $facture["date"]?> - Abonnement de "<?php echo $facture["nom_offre"]?>"</p>
                        <img class="image-facture" src="/images/universel/facture.png">
                        <?php
                    }
                ?>
            </ul>
        </section>
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

    <script>
        const titre = document.querySelector(".facture");
        const imgFacture = document.querySelector(".image-facture");

        titre.addEventListener("click", () => {
            imgFacture.classList.toggle("hidden");
        });
    </script>
</body>
</html>