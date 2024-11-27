<?php 
    // Quelques fonctions pour savoir le compte d'un utilisateur
    require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
    function isIdMember($id) {
        global $driver, $server, $dbname, $user, $pass;
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_membre WHERE id_compte = :id;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return isset($result['count']) && $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function isIdProPrivee($id) {
        global $driver, $server, $dbname, $user, $pass;
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_professionnel_prive WHERE id_compte = :id;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return isset($result['count']) && $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }
    
    function isIdProPublique($id) {
        global $driver, $server, $dbname, $user, $pass;
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->prepare("SET SCHEMA 'sae';")->execute();
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_professionnel_publique WHERE id_compte = :id;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return isset($result['count']) && $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }


    function redirectToListOffreIfNecessary($id) {
        if ($id === null || (!isIdProPublique($id) && !isIdProPrivee($id))) {
            redirectTo('/front/consulter-offres/');
            return true;
        }     
        return false;   
    }
?>