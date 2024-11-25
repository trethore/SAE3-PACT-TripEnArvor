<?php
require_once('../utils/session-utils.php');

startSession();

$submitted = isset($_POST['type-compte']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
    <style>
        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 50vw;
            margin: 0 auto;
            padding: 1rem;
            border: 1px solid black;
        }

        form div {
            margin: 0.5rem 0;
            display: flex;
            flex-direction: column;
            width: 75%;
        }

        select,
        input {
            padding: 0.5rem;
        }

        form > div {
            display: none;
        }

        #div-type-compte {
            display: flex;
        }

        label span {
            display: none;
            color: red;
        }

        #div-type-compte label span {
            display: inline;
        }

        form span span {
            color: red;
        }
    </style>
    <script src="/scripts/creer-compte.js"></script>
</head>

<body>
<?php
if (!$submitted) {
?>
    <form action="/creer-compte/" method="post">
        <h1>Creer un compte</h1>
        <span><span>*</span> Champs obligatoires</span>
        <div id="div-type-compte">
            <label for="type-compte">Type de compte<span> *</span></label>
            <select name="type-compte" id="type-compte">
                <option value="">-- Sélectionner un type de compte --</option>
                <option value="membre">Compte membre</option>
                <option value="pro-publique">Compte professionnel publique</option>
                <option value="pro-privée">Compte professionnel privée</option>
            </select>
        </div>
        <div id="div-email">
            <label for="email">Votre adresse email<span> *</span></label>
            <input type="email" id="email" name="email">
        </div>
        <div id="div-password">
            <label for="password">Mot de passe<span> *</span></label>
            <input type="password" id="password" name="password">
        </div>
        <div id="div-confirm-password">
            <label for="confirm-password">Confirmer le mot de passe<span> *</span></label>
            <input type="password" id="confirm-password" name="confirm-password">
        </div>
        <div id="div-pseudo">
            <label for="pseudo">Pseudo<span> *</span></label>
            <input type="text" name="pseudo" id="pseudo">
        </div>
        <div id="div-name">
            <label for="name">Nom<span> *</span></label>
            <input type="text" id="name" name="name">
        </div>
        <div id="div-first-name">
            <label for="first-name">Prénom<span> *</span></label>
            <input type="text" name="first-name" id="first-name">
        </div>
        <div id="div-tel">
            <label for="tel">Téléphone<span> *</span></label>
            <input type="tel" name="tel" id="tel">
        </div>
        <div id="div-denomination">
            <label for="denomination">Dénomination<span> *</span></label>
            <input type="text" name="denomination" id="denomination">
        </div>
        <div id="div-a-propos">
            <label for="a-propos">À propos<span> *</span></label>
            <textarea name="a-propos" id="a-propos"></textarea>
        </div>
        <div id="div-site-web">
            <label for="site-web">Site web<span> *</span></label>
            <input type="text" name="site-web" id="site-web">
        </div>
        <div id="div-siren">
            <label for="siren">Numéro de SIREN<span> *</span></label>
            <input type="text" name="siren" id="siren">
        </div>
        <div id="div-adresse">
            <div id="div-street">
                <label for="street">Numéro et nom de voie<span> *</span></label>
                <input type="text" name="street" id="street">
            </div>
            <div id="div-address-complement">
                <label for="address-complement">Complément d'adresse<span> *</span></label>
                <input type="text" name="address-complement" id="address-complement">
            </div>
            <div id="div-code-postal">
                <label for="code-postal">Code postal<span> *</span></label>
                <input type="number" name="code-postal" id="code-postal">
            </div>
            <div id="div-city">
                <label for="city">Ville<span> *</span></label>
                <input type="text" name="city" id="city">
            </div>
            <div id="div-country">
                <label for="country">Pays<span> *</span></label>
                <input type="text" name="country" id="country">
            </div>
        </div>
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
        case 'pro-privée':
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
    if ($_POST['password'] !== $_POST['confirm-password'])
    {
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
        switch ($_POST['type-compte']) {
            case 'membre':
                $pseudo = $_POST['pseudo'];
                if ($name === '') $name = null;
                if ($first_name === '') $first_name = null;
                if ($tel === '') $tel = null;
                $query = "INSERT INTO sae.compte_membre (nom_compte, prenom, email, tel, mot_de_passe, pseudo) VALUES (?, ?, ?, ?, ?, ?);";
                $stmt = $dbh->prepare($query);
                $stmt->execute([$name, $first_name, $email, $tel, $password_hash, $pseudo]);
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
                $query = "INSERT INTO sae.compte_professionnel_publique (nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
                $stmt = $dbh->prepare($query);
                $stmt->execute([$name, $first_name, $email, $tel, $password_hash, $id_adresse, $denomination, $a_propos, $site_web]);
                break;
            case 'pro-privée':
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
                $query = "INSERT INTO sae.compte_professionnel_prive (nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web, siren) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
                $stmt = $dbh->prepare($query);
                $stmt->execute([$name, $first_name, $email, $tel, $password_hash, $id_adresse, $denomination, $a_propos, $site_web, $siren]);
                break;
            default:
                
                break;
        }

    
?>
    <h1>OK</h1>
    <a href=".">ok</a>
<?php
    } else {
?>
        <h1>Pas OK</h1>
        <a href=".">ok</a>
<?php
    }
}
?>
</body>

</html>
<?php
$dbh = null;
