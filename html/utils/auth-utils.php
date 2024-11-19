<?php 
    // Quelques fonctions pour savoir le compte d'un utilisateur
    include('../php/connect_params.php');

    function isIdMemver($id) {
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_membre WHERE id_compte = :id';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    function isIdProPrivee($id) {
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_professionnel_prive WHERE id_compte = :id';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    function isIdProPublique($id) {
        try {
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $sql = 'SELECT COUNT(*) AS count FROM sae.compte_professionnel_publique WHERE id_compte = :id';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            $dbh = null;
            
            return $result['count'] > 0;
        }catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
?>