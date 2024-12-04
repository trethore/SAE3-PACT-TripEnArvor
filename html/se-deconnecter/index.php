<?php
session_start(); // Démarre la session
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session
header("Location: /front/consulter-offres/"); // Redirige vers la page d'accueil (ou autre)
exit();
?>