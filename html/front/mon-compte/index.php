<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
startSession();
if (!isset($_SESSION["id"])) {
    header("Location: /se-connecter/");
}
$id_compte = $_SESSION["id"];
redirectToConnexionIfNecessaryMembre($id_compte);

require_once('../../utils/compte-utils.php');
require_once('../../utils/site-utils.php');

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$reqCompte = "SELECT * from sae.compte_membre
                where id_compte = :id_compte;";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="/style/style.css">
    <title>Mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="front compte-front">
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();
        $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
        $stmt->execute();
        $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
        $dbh = null;
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des titres : " . $e->getMessage();
    }
    ?>

    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>

        </div>
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
                            window.location.href = `/front/consulter-offre/index.php?id=${idOffre}`;
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
            <a class="ici" href="/front/mon-compte">Mes infos</a>
            <a href="/se-deconnecter/index.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
        </nav>
        <section>
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
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Pseudo</td>
                    <td><?php echo htmlentities($detailCompte["pseudo"]); ?></td>
                </tr>
                <tr>
                    <td>Nom</td>
                    <td><?php echo htmlentities($detailCompte["nom_compte"] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Prenom</td>
                    <td><?php echo htmlentities($detailCompte["prenom"] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><?php echo htmlentities($detailCompte["email"]); ?></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td><?php echo htmlentities($detailCompte["tel"] ?? ''); ?></td>
                </tr>
            </table>
            <div>
                <a href="/front/modifier-compte">Modifier les informations</a>
            </div>
            <div>
                <?php
                    $APIKey = hash('sha256', $id_compte . $detailCompte["email"]. $detailCompte["mot_de_passe"]);
                ?>
                <script>
                    function copyAPIKey() {
                        var apiKey = "<?php echo addslashes($APIKey); ?>";
                        navigator.clipboard.writeText(apiKey);
                        alert("Clé d'API Tchatator copiée dans le presse-papier!");
                    }
                </script>
                <h2>Clé d'accès au Tchatator : </h2>
                <button onclick="copyAPIKey()" id="apibutton">Cliquez ici !</button>
            </div>
            <script>
                // constante pour valider les modifications du compte
                const popupValider = document.getElementById("validerDeleteCompte");
                const boutonAnnuler = document.getElementById("boutonAnnuler");
                const boutonValider = document.getElementById("boutonValider");

                // Affiche la popup
                function deleteCompte(event) {
                    popupOverlay.style.display = "block";
                    popupValider.style.display = "flex";
                };

                // Ferme la popup sans valider
                boutonAnnuler.addEventListener("click", function() {
                    popupOverlay.style.display = "none";
                    popupValider.style.display = "none";
                });

                // Valide les modifications et soumet le formulaire
                boutonValider.addEventListener("click", function() {
                    popupOverlay.style.display = "none";
                    popupValider.style.display = "none";
                });
            </script>
            <button onclick="deleteCompte()", id="delButton">Supprimer le compte</button>
        </section>
        <div id="popupOverlay" style="display: none;"></div>
        <div id="validerDeleteCompte" style="display: none;">
            <h3>Supprimer le compte</h3>
            <p>Voulez-vous vraiment supprimer votre compte ?</p>
            <div>
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Supprimer </button>
            </div>
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
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>
    <div class="telephone-nav">
        <div class="nav-content">
            <a href="/front/accueil">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/accueil.png">
                </div>
            </a>
            <a href="/front/consulter-offres">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/chercher.png">
                </div>
            </a>
            <a href="/front/mon-compte">
                <div class="btOn">
                    <img width="400" height="400" src="/images/frontOffice/icones/utilisateur.png">
                </div>
            </a>
        </div>
    </div>
</body>


</html>