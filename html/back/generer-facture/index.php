<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"]; 
if (isset($id_compte)) {
    redirectToConnexionIfNecessaryPro($id_compte);
} else {
    redirectTo('https://redden.ventsdouest.dev/front/consulter-offres/');
}

$reqCompte = "SELECT * from sae.compte_professionnel_prive where id_compte = :id_compte;";
$reqFacture = "";

// Préparation et exécution de la requête
$stmt = $conn->prepare($reqCompte);
$stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
$stmt->execute();
$detailCompte = $stmt->fetch(PDO::FETCH_ASSOC)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body class="genFacture">
    <div class="infoFacture">
        <img src="/images/universel/logo/Logo_couleurs.png" alt="logo de PACT">
        <article>
            <h3>Numéro de facture</h3>
            <h3>{#id_facture}</h3>
            <p>{date_emission}</p>
        </article>
    </div>
    <section>
        <article class="delivre">
            <h3>Délivré à</h3>
            <p>
                <!-- Nom -->
                <?php echo htmlentities($detailCompte["nom_compte"] ?? '');?> 
                <!-- Prenom -->
                <?php echo htmlentities($detailCompte["prenom"] ?? '');?><br>
                <!-- Dénomination Sociale -->
                <?php echo htmlentities($detailCompte["denomination"] ?? '');?><br>
                <br>
                <!-- Email -->
                <?php echo htmlentities($detailCompte["email"] ?? '');?><br>
            </p>
        </article>
        <article class="emetteur">
            <h3>Emetteur</h3>
            <p>
                Trip en Arvor <br>
                Rue Édouard Branly, <br>
                22300 Lannion <br>
                Tél : 02 96 46 93 00
                Email : tripenarvor@gmail.com
            </p>
        </article>
    </section>
    <article class="facture-details">
        <table>
            <thead>
                <tr>
                    <th>Nom offre</th>
                    <th>Nb Jour</th>
                    <th>% TVA</th>
                    <th>Prix HT Journalier</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Restaurant coté plage</td>
                    <td>2</td>
                    <td>20%</td>
                    <td>5.10€</td>
                    <td>22.30€</td>
                </tr>
                <tr>
                    <td>Parc d'attraction vraiment wahou</td>
                    <td>1</td>
                    <td>20%</td>
                    <td>24.75€</td>
                    <td>5.10€</td>
                </tr>
            </tbody>
        </table>
    </article>
    <article class="facture-details">
        <table>
            <thead>
                <tr>
                    <th>Nom Option</th>
                    <th>Nb semaines</th>
                    <th>% TVA</th>
                    <th>Prix HT Hebdomadaire</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>A la Une</td>
                    <td>1</td>
                    <td>20%</td>
                    <td>12.00€</td>
                    <td>18.00€</td>
                </tr>
            </tbody>
        </table>
    </article>
    <hr>
    <table class="totals">
        <tr>
            <td>Total HT</td>
            <td>31.25€</td>
        </tr>
        <tr>
            <td>Total TVA</td>
            <td>10€</td>
        </tr>
        <tr>
            <td><b>Total TTC</b></td>
            <td><b>34.38€</b></td>
        </tr>
    </table>
    <article class="payment-terms">
        <h3>Conditions et modalités de paiement</h3>
        <p>Le paiement est dû dans les 15 jours</p>
        <p>
            Banque PACT<br>
            Nom du compte: Trip en arvor<br>
            Numéro de compte : 123-456-7890
        </p>
    </article>
</body>
</html>
