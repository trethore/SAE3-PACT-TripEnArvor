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
    $id_membre = $_POST['id_membre'] ?? null;
    $dateBlacklist = date('Y-m-d H:i:s');

    if (isset($id_offre) && isset($id_membre)) {

        try {
            //Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Insertion de la date de blacklistage
            $reqInsertionDateBlacklist = "INSERT INTO sae._blacklister(id_offre, id_membre, blackliste_le) VALUES (?, ?, ?)";
            $stmtInsertionDateBlacklist = $dbh->prepare($reqInsertionDateBlacklist);
            $stmtInsertionDateBlacklist->execute([$id_offre, $id_membre, $dateBlacklist]);

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}