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

?>