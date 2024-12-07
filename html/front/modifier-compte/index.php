<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . COMPTE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"];
if (!isset($id_compte) || !isIdMember($id_compte)) {
    redirectTo("https://redden.ventsdouest.dev/front/consulter-offres/");
}
$submitted = isset($_POST['email']);
$typeCompte = getTypeCompte($id_compte);

$reqCompte = "SELECT * from sae._compte_membre cm 
                join sae._compte c on c.id_compte = cm.id_compte 
                join sae._adresse a on c.id_adresse = a.id_adresse 
                where cm.id_compte = :id_compte;";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_frontCompte.css">
    <link rel="stylesheet" href="/style/style_frontCompteModif.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/styleguide.css">
    <title>Modifier mon compte</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body>
    <?php
    if (!$submitted) {
    ?>
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
    <div class="text-wrapper-17"><a href="/front/consulter-offres" class="retourAccueil">PACT Pro</a></div>
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
    <a href="/front/accueil" class="retourAccueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
    <a href="/front/mon-compte" id="retourCompte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
        <?php
        // Préparation et exécution de la requête
        $stmt = $conn->prepare($reqCompte);
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
        $stmt->execute();
        $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <h1>Modification du compte</h1>
        <form action="/front/modifier-compte/" method="POST" id="myForm">
            <h2>Informations personnelles</h2>
            <table>
                <tr>
                    <td>Pseudo</td>
                    <td>
                        <input type="text" name="pseudo" id="pseudo" value="<?= htmlentities($detailCompte["pseudo"] ?? '');?>">
                    </td>
                </tr>
                <tr>
                    <td>Nom</td>
                    <td>
                        <input type="text" name="nom" id="nom" value="<?= htmlentities($detailCompte["nom_compte"] ?? '');?>">
                    </td>
                </tr>
                <tr>
                    <td>Prénom</td>
                    <td>
                        <input type="text" name="prenom" id="prenom" value="<?= htmlentities($detailCompte["prenom"] ?? '');?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td><input type="email" name="email" id="email" value="<?= htmlentities($detailCompte["email"] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>N° de téléphone</td>
                    <td>
                        <input type="tel" name="tel" id="tel" value="<?= htmlentities($detailCompte["tel"] ?? '');?>"> 
                    </td>
                </tr>
                <tr>
                    <td>Mot de passe</td>
                    <td>
                        <input type="password" name="mdp" id="mdp" placeholder="Saisissez un nouveau mot de passe">
                        <input type="hidden" name="ancien_mdp" value="<?= htmlentities($detailCompte['mot_de_passe']); ?>">
                    </td>
                </tr>
            </table>
            <h2>Mon adresse</h2>
            <table>
                <tr>
                    <td>Adresse postale</td>
                    <td><input type="text" name="rue" id="rue" value="<?= htmlentities($detailCompte["num_et_nom_de_voie"] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>Complément d'adresse</td>
                    <td>
                        <input type="text" name="compl_adr" id="compl_adr" value="<?= htmlentities($detailCompte["complement_adresse"] ?? '');?>">
                    </td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><input type="text" name="cp" id="cp" value="<?= htmlentities($detailCompte["code_postal"] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><input type="text" name="ville" id="ville" value="<?= htmlentities($detailCompte["ville"] ?? '');?>"></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><input type="text" name="pays" id="pays" value="<?= htmlentities($detailCompte["pays"] ?? '');?>"></td>
                </tr>
            <div>
                <a href="/front/mon-compte" id="retour">Revenir au compte</a>
                <input type="submit" value="Valider les modifications">
            </div>
        </form> 
        <div id="popupOverlay" style="display: none;"></div>
        <div id="validerModifCompte" style="display: none;">
            <h3>Valider les modifications</h3>
            <p>Voulez-vous valider les modifications apporter à votre profil ?</p>
            <div >
                <button id="boutonAnnuler"> Annuler </button>
                <button id="boutonValider"> Valider </button> 
            </div>
        </div>
        <div id="annulerModifCompte" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Voulez-vous annuler les modifications apporter à votre compte ?</p>
            <div>
                <button id="boutonReprendre"> Reprendre </button>
                <button id="boutonQuitter"> Quitter </button>
            </div>
        </div>
        <div id="popupRetourAccueil" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Si vous retournez à l'accueil, vous annulez les modifications faites pour l'instant</p>
            <div>
                <button id="boutonReprendreAccueil"> Reprendre </button>
                <button id="boutonRetourAccueil"> Quitter </button>
            </div>
        </div> 
        <div id="popupRetourCompte" style="display: none;">
            <h3>Annuler les modifications</h3>
            <p>Si vous retournez sur votre compte, vous annulez les modifications faites pour l'instant</p>
            <div>
                <button id="boutonReprendreCompte"> Reprendre </button>
                <button id="boutonRetourCompte"> Quitter </button>
            </div>
        </div> 
    </main>
    <?php
    } else {
        $ok = true;
        switch ($typeCompte) {
            case 'membre':
                $ok = $ok && isset($_POST['email']);
                $ok = $ok && isset($_POST['tel']);
                $ok = $ok && isset($_POST['pseudo']);
                break;

            default:
                $ok = false;
                break;
        }
        // Récupération des données du formulaire
        $nouveauMotDePasse = $_POST['mdp'] ?? '';
        $ancienMotDePasse = $_POST['ancien_mdp'] ?? '';

        // Traitement
        if (!empty($nouveauMotDePasse)) {
            // Si un nouveau mot de passe a été fourni, on le crypte
            $motDePasseFinal = password_hash($nouveauMotDePasse, PASSWORD_BCRYPT);
        } else {
            // Sinon, on conserve l'ancien mot de passe
            $motDePasseFinal = $ancienMotDePasse;
        }

        $email = $_POST['email'];
        $pseudo = $_POST['pseudo'];
        $password = $_POST['mdp'];
        $name = $_POST['nom'];
        $first_name = $_POST['prenom'];
        $tel = $_POST['tel'];

        if ($ok) {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            switch ($typeCompte) {
                case 'membre':
                    $street = $_POST['rue'];
                    $address_complement = $_POST['compl_adr'] ?? '';
                    $code_postal = $_POST['cp'];
                    $city = $_POST['ville'];
                    $country = $_POST['pays'];
                    if ($address_complement === '') $address_complement = null;
                    // Requete SQL pour modifier la table adresse
                    $query = "UPDATE sae._adresse 
                                set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                    where id_adresse = (select id_adresse from sae._compte where id_compte = ?) returning id_adresse;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country, $id_compte]);
                    $id_adresse = $stmt->fetch()['id_adresse'];

                    // Requete SQL pour modifier la vue compte_professionnel_publique
                    $query = "UPDATE sae.compte_membre 
                                set (pseudo, nom_compte, prenom, email, tel, mot_de_passe, id_adresse) 
                                    = (?, ?, ?, ?, ?, ?, ?)
                                where id_compte = ?;";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$pseudo, $name, $first_name, $email, $tel, $motDePasseFinal, $id_adresse, $id_compte]);

                    break;

                default:
                    $ok = false;
                    break;
            }
        }
        redirectTo("/front/mon-compte");
    } ?>
<script src="/scripts/popupCompteFront.js"></script>
</body>
</html>