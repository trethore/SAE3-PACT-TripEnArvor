<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

/*try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}*/

startSession();
$id_compte = $_SESSION["id"];
redirectToConnexionIfNecessaryPro($id_compte);

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
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>
<body>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
startSession();
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT * from sae._offre where id_compte_professionnel = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}
?>

<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input  autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const inputSearch = document.querySelector(".input-search");
                const datalist = document.querySelector("#cont");
                // Événement sur le champ de recherche
                inputSearch.addEventListener("input", () => {
                    // Rechercher l'option correspondante dans le datalist
                    const selectedOption = Array.from(datalist.options).find(
                        option => option.value === inputSearch.value
                    );
                    if (selectedOption) {
                        const idOffre = selectedOption.getAttribute("data-id");
                        //console.log("Option sélectionnée :", selectedOption.value, "ID:", idOffre);
                        // Rediriger si un ID valide est trouvé
                        if (idOffre) {
                            // TD passer du back au front quand fini
                            window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
                        }
                    }
                });
                // Debugging pour vérifier les options disponibles
                const options = Array.from(datalist.options).map(option => ({
                    value: option.value,
                    id: option.getAttribute("data-id")
                }));
                //console.log("Options disponibles dans le datalist :", options);
            });
        </script>
    </header>
    <main>
        <nav>
            <a href="/back/mon-compte">Mes infos</a>
            <a class="ici" href="/back/mes-factures">Mes factures</a>
            <a href="/se-connecter">Se déconnecter</a>
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
                    <li>
                        <a href="/images/universel/facture.pdf" target="_blank"><p>Facture du <?php echo $facture["date"] ?> - Abonnement de "<?php echo $facture["nom_offre"] ?>"</p></a>
                    </li>
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
</body>
</html>