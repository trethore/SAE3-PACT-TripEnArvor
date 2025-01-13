<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);

    function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    function endSession() {
        session_unset();
        session_destroy();
        redirectTo('/front/consulter-offres/');
    }

    function checkAuth() {
        return isset($_SESSION['id']);
    }

    function addConsultedOffer($idOffre) {
        if (!isset($_SESSION['recent_offers'])) {
            $_SESSION['recent_offers'] = [];
        }
    
        if (!in_array($idOffre, $_SESSION['recent_offers'])) {
            $_SESSION['recent_offers'][] = $idOffre;
            if (count($_SESSION['recent_offers']) > 10) {
                array_shift($_SESSION['recent_offers']);
            }
        }
    }
    
    function getConsultedOffers() {
        if (!isset($_SESSION['recent_offers']) || empty($_SESSION['recent_offers'])) {
            return [];
        }
    
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
            $placeholders = implode(',', array_fill(0, count($_SESSION['recent_offers']), '?'));
            $query = "SELECT id_offre, titre, note FROM sae._offre WHERE id_offre IN ($placeholders)";
            $stmt = $dbh->prepare($query);
            $stmt->execute($_SESSION['recent_offers']);
            $offers = $stmt->fetchAll();
    
            $dbh = null;
            return $offers;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des offres : " . $e->getMessage();
            return [];
        }
    }
?>