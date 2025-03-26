<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_offre = $_POST['id_offre'] ?? null;
    $id_membre = $_POST['id_membre'] ?? null;
    $reponse = $_POST['reponse'] ?? null;
    $publie_le = date('Y-m-d H:i:s');

    if (isset($id_offre) && isset($id_membre) && isset($reponse)) {

        try {
            // Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insérer la date de publication
            $reqInsertionDateReponse = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
            $stmtInsertionDateReponse = $dbh->prepare($reqInsertionDateReponse);
            $stmtInsertionDateReponse->execute([$publie_le]);
            $idDateReponse = $stmtInsertionDateReponse->fetch(PDO::FETCH_ASSOC)['id_date'];

            // Insérer la réponse liée à l'avis
            $reqInsertionReponse = "INSERT INTO sae._reponse(id_membre, id_offre, texte, publie_le) VALUES (?, ?, ?, ?)";
            $stmtInsertionReponse = $dbh->prepare($reqInsertionReponse);
            $stmtInsertionReponse->execute([$id_membre, $id_offre, $reponse, $idDateReponse]);

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la réponse : " . $e->getMessage();
        } 
    }
    } 
?>
