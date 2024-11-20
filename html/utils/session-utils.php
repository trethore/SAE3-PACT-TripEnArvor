<?php
    include('/var/www/html/php/connect_params.php');

    function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    function endSession() {
        session_destroy();
    }

    function checkAuth() {
        return isset($_SESSION['id']);
    }

?>