<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_offre = $_POST['id_offre'] ?? null;
    $date = date('Y-m-d H:i:s');

    if (($dateMEL > $dateMHL) || ($dateMHL == null)) {
        
        try {
            // Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Insertion de la date de mise hors ligne
            $reqInsertionDateMHL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
            $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
            $stmtInsertionDateMHL->execute([$date]);
            $idDateMHL = $stmtInsertionDateMHL->fetch(PDO::FETCH_ASSOC)['id_date'];
        
            $reqInsertionDateMHL = "INSERT INTO sae._offre_dates_mise_hors_ligne(id_offre, id_date) VALUES (?, ?)";
            $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
            $stmtInsertionDateMHL->execute([$id_offre, $idDateMHL]);

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    
    } else if ($dateMHL > $dateMEL) {
    
        try {
            // Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Insertion de la date de mise en ligne
            $reqInsertionDateMEL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
            $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
            $stmtInsertionDateMEL->execute([$date]);
            $idDateMEL = $stmtInsertionDateMEL->fetch(PDO::FETCH_ASSOC)['id_date'];
        
            $reqInsertionDateMEL = "INSERT INTO sae._offre_dates_mise_en_ligne(id_offre, id_date) VALUES (?, ?)";
            $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
            $stmtInsertionDateMEL->execute([$id_offre, $idDateMEL]);

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>