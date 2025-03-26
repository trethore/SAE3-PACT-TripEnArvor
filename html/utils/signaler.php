<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

date_default_timezone_set('Europe/Paris');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_offre = $_POST['id_offre'] ?? null;
    $id_signale = $_POST['id_signale'] ?? null;
    $id_signalant = $_POST['id_signalant'] ?? null;
    $motif = $_POST['motif'] ?? null;
    $date_signalement = date('Y-m-d H:i:s');

    print_r($id_offre);
    print_r($id_signale);
    print_r($id_signalant);
    print_r($motif);
    print_r($date_signalement);


    if (isset($id_offre) && isset($id_signale) && isset($id_signalant) && isset($motif) && isset($date_signalement)) {

        try {
            //Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Insertion des différentes valeurs dans la table _signaler
            $reqInsertionSignaler = "INSERT INTO sae._signaler(id_offre, id_signale, id_signalant, motif, date_signalement) VALUES (?, ?, ?, ?, ?)";
            $stmtInsertionSignaler = $dbh->prepare($reqInsertionSignaler);
            $stmtInsertionSignaler->execute([$id_offre, $id_signale, $id_signalant, $motif, $date_signalement]);

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>