<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);


startSession();

$submitted = isset($_POST['type-compte']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width"/>
    <title>Créer un compte</title>
    <link rel="stylesheet" href="/style/style.css">
    <script src="/scripts/creer-compte.js"></script>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="creer-compte">
<?php
if (!$submitted) {
?>
    <header>
        <a href="/front/accueil/">
            <img src="/images/universel/logo/Logo_couleurs.png" alt="Logo de la PACT">
        </a>
    </header>
    <form action="/creer-compte/" method="post">
        <h1>Créer un compte</h1>
        <span>Vous avez déjà un compte ? <a href="/se-connecter/">Connexion</a></span>
        <span><span>*</span> Champs obligatoires</span>
        <hr>
        <div id="div-type-compte">
            <label for="type-compte">Type de compte<span> *</span></label>
            <select name="type-compte" id="type-compte">
                <option value="">-- Sélectionnez un type de compte --</option>
                <option value="membre">Compte membre</option>
                <option value="pro-publique">Compte professionnel publique</option>
                <option value="pro-privé">Compte professionnel privé</option>
            </select>
        </div>
        <hr>
        <div id="div-email">
            <label for="email">Votre adresse email<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span><span id="email-already-exist"> Un compte avec cette adresse email existe déjà</span></label>
            <input type="email" id="email" name="email" placeholder="votre.adresse@email.fr" maxlength="319">
        </div>
        <div id="div-password">
            <label for="password">Mot de passe<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <input type="password" id="password" name="password" placeholder="Votre mot de passe" maxlength="254">
        </div>
        <div id="div-confirm-password">
            <label for="confirm-password">Confirmer le mot de passe<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span><span id="different-passwords-message"> Les mots de passe sont différents</span></label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Le même mot de passe" maxlength="254">
        </div>
        <hr>
        <div id="div-pseudo">
            <label for="pseudo">Pseudo<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span><span id="pseudo-already-exist"> Ce pseudo existe déjà</span></label>
            <input type="text" name="pseudo" id="pseudo" placeholder="Votre pseudonyme" maxlength="254">
        </div>
        <div id="div-name-and-first-name">
            <div id="div-name">
                <label for="name">Nom<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                <input type="text" id="name" name="name" placeholder="Ex : DUPONT" maxlength="29">
            </div>
            <div id="div-first-name">
                <label for="first-name">Prénom<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                <input type="text" name="first-name" id="first-name" placeholder="Ex : Jean" maxlength="29">
            </div>
        </div>
        <div id="div-tel">
            <label for="tel">Téléphone<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <input type="tel" name="tel" id="tel" placeholder="Ex : +33606060606" maxlength="12">
        </div>
        <div id="div-denomination">
            <label for="denomination">Dénomination sociale<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <input type="text" name="denomination" id="denomination" placeholder="Le nom de votre société/entreprise/association">
        </div>
        <div id="div-a-propos">
            <label for="a-propos">À propos<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <textarea name="a-propos" id="a-propos" placeholder="Description de vos activités"></textarea>
        </div>
        <div id="div-site-web">
            <label for="site-web">Site web<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <input type="url" name="site-web" id="site-web" placeholder="https://votre.site-web.fr">
        </div>
        <div id="div-siren">
            <label for="siren">Numéro de SIREN<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
            <input type="text" name="siren" id="siren" placeholder="Ex : 123456780">
        </div>
        <hr>
        <div id="div-adresse">
            <div id="div-street">
                <label for="street">Numéro et nom de voie<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                <input type="text" name="street" id="street" placeholder="Ex : 1 rue du poisson d'avril">
            </div>
            <div id="div-address-complement">
                <label for="address-complement">Complément d'adresse<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                <input type="text" name="address-complement" id="address-complement" placeholder="Village, lieu-dit, bâtiment, etc...">
            </div>
            <div class="row">
                <div id="div-code-postal">
                    <label for="code-postal">Code postal<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                    <input type="text" name="code-postal" id="code-postal" placeholder="Ex : 22300">
                </div>
                <div id="div-city">
                    <label for="city">Ville<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                    <input type="text" name="city" id="city" placeholder="Ex : Lannion">
                </div>
                <div id="div-country">
                    <label for="country">Pays<span> *</span><span class="required-message"> Veuillez renseigner ce champs</span></label>
                    <input type="text" name="country" id="country" placeholder="Ex : France">
                </div>
            </div>
        </div>
        <hr>
        <label for="cgu"><input type="checkbox" name="cgu" id="cgu"> J'ai lu et j'accepte les <a href="/droit/CGU-1.pdf" target="_blank">conditions générales d'utilisation</a>.</label>
        <input type="submit" value="Créer un compte" disabled>
    </form>
<?php
} else {
    $ok = true;
    switch ($_POST['type-compte']) {
        case 'membre':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['password']);
            $ok = $ok && isset($_POST['confirm-password']);
            $ok = $ok && isset($_POST['pseudo']);
            break;
        case 'pro-publique':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['password']);
            $ok = $ok && isset($_POST['confirm-password']);
            $ok = $ok && isset($_POST['name']);
            $ok = $ok && isset($_POST['first-name']);
            $ok = $ok && isset($_POST['tel']);
            $ok = $ok && isset($_POST['denomination']);
            $ok = $ok && isset($_POST['a-propos']);
            $ok = $ok && isset($_POST['site-web']);
            $ok = $ok && isset($_POST['street']);
            $ok = $ok && isset($_POST['code-postal']);
            $ok = $ok && isset($_POST['city']);
            $ok = $ok && isset($_POST['country']);
            break;
        case 'pro-privé':
            $ok = $ok && isset($_POST['email']);
            $ok = $ok && isset($_POST['password']);
            $ok = $ok && isset($_POST['confirm-password']);
            $ok = $ok && isset($_POST['name']);
            $ok = $ok && isset($_POST['first-name']);
            $ok = $ok && isset($_POST['tel']);
            $ok = $ok && isset($_POST['denomination']);
            $ok = $ok && isset($_POST['a-propos']);
            $ok = $ok && isset($_POST['site-web']);
            $ok = $ok && isset($_POST['siren']);
            $ok = $ok && isset($_POST['street']);
            $ok = $ok && isset($_POST['code-postal']);
            $ok = $ok && isset($_POST['city']);
            $ok = $ok && isset($_POST['country']);
            break;
        default:
            $ok = false;
            break;
    }
    if ($_POST['password'] !== $_POST['confirm-password']) {
        $ok = false;
    }

    $type_compte = $_POST['type-compte'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $name = $_POST['name'];
    $first_name = $_POST['first-name'];
    $tel = $_POST['tel'];
    $password_hash;

    if ($password === $confirm_password) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    }

    if ($ok) {
        require_once('../php/connect_params.php');
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();
        try{
            switch ($_POST['type-compte']) {
                case 'membre':
                    $pseudo = $_POST['pseudo'];
                    if ($name === '') $name = null;
                    if ($first_name === '') $first_name = null;
                    if ($tel === '') $tel = null;
                    $query = "INSERT INTO sae.compte_membre (nom_compte, prenom, email, tel, mot_de_passe, auth, pseudo) VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id_compte;";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute([$name, $first_name, $email, $tel, $password_hash,FALSE, $pseudo]);
                    $_SESSION['id'] = $stmt->fetch()['id_compte'];
                    break;
                case 'pro-publique':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a-propos'];
                    $site_web = $_POST['site-web'];
                    $street = $_POST['street'];
                    $address_complement = $_POST['address-complement'];
                    $code_postal = $_POST['code-postal'];
                    $city = $_POST['city'];
                    $country = $_POST['country'];
                    if ($address_complement === '') $address_complement = null;
                    $query = "INSERT INTO sae._adresse (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) VALUES (?, ?, ?, ?, ?) RETURNING id_adresse;";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country]);
                    $id_adresse = $stmt->fetch()['id_adresse'];
                    $query = "INSERT INTO sae.compte_professionnel_publique (nom_compte, prenom, email, tel, mot_de_passe, auth, id_adresse, denomination, a_propos, site_web) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_compte;";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute([$name, $first_name, $email, $tel, $password_hash,FALSE, $id_adresse, $denomination, $a_propos, $site_web]);
                    $_SESSION['id'] = $stmt->fetch()['id_compte'];
                    break;
                case 'pro-privé':
                    $denomination = $_POST['denomination'];
                    $a_propos = $_POST['a-propos'];
                    $site_web = $_POST['site-web'];
                    $siren = $_POST['siren'];
                    $street = $_POST['street'];
                    $address_complement = $_POST['address-complement'];
                    $code_postal = $_POST['code-postal'];
                    $city = $_POST['city'];
                    $country = $_POST['country'];
                    if ($address_complement === '') $address_complement = null;
                    $query = "INSERT INTO sae._adresse (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) VALUES (?, ?, ?, ?, ?) RETURNING id_adresse;";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute([$street, $address_complement, $code_postal, $city, $country]);
                    $id_adresse = $stmt->fetch()['id_adresse'];
                    $query = "INSERT INTO sae.compte_professionnel_prive (nom_compte, prenom, email, tel, mot_de_passe, auth, id_adresse, denomination, a_propos, site_web, siren) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_compte;";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute([$name, $first_name, $email, $tel, $password_hash,FALSE, $id_adresse, $denomination, $a_propos, $site_web, $siren]);
                    $_SESSION['id'] = $stmt->fetch()['id_compte'];
                    break;
                default:
                    $ok = false;
                    break;
            }
        
            if (isIdProPrivee($_SESSION['id']) || isIdProPublique($_SESSION['id'])) {
                redirectTo('/back/liste-back/');
            } else if (isIdMember($_SESSION['id'])) {
                redirectTo('/front/consulter-offres/');
            }
        } catch(PDOException $e) {
             http_response_code(400);
        }
    } else {
        http_response_code(500);
    }
    
}
?>
</body>

</html>
<?php
$dbh = null;
