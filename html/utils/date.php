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
    $date = date('Y-m-d H:i:s');
    $dateMHL = getDateOffreHorsLigne($id_offre);
    $dateMEL = getDateOffreEnLigne($id_offre);

    if (isset($id_offre)) {

        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (($dateMEL > $dateMHL) || ($dateMHL == null)) {
                //Insertion de la date de mise hors ligne
                $reqInsertionDateMHL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
                $stmtInsertionDateMHL->execute([$date]);
                $idDateMHL = $stmtInsertionDateMHL->fetch(PDO::FETCH_ASSOC)['id_date'];
                
                $reqInsertionDateMHL = "INSERT INTO sae._offre_dates_mise_hors_ligne(id_offre, id_date) VALUES (?, ?)";
                $stmtInsertionDateMHL = $dbh->prepare($reqInsertionDateMHL);
                $stmtInsertionDateMHL->execute([$id_offre, $idDateMHL]);
        
            } else if ($dateMHL > $dateMEL) {
                //Insertion de la date de mise en ligne
                $reqInsertionDateMEL = "INSERT INTO sae._date(date) VALUES (?) RETURNING id_date";
                $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
                $stmtInsertionDateMEL->execute([$date]);
                $idDateMEL = $stmtInsertionDateMEL->fetch(PDO::FETCH_ASSOC)['id_date'];
            
                $reqInsertionDateMEL = "INSERT INTO sae._offre_dates_mise_en_ligne(id_offre, id_date) VALUES (?, ?)";
                $stmtInsertionDateMEL = $dbh->prepare($reqInsertionDateMEL);
                $stmtInsertionDateMEL->execute([$id_offre, $idDateMEL]);

            } 
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
} 
?>