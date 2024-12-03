<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

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

?>