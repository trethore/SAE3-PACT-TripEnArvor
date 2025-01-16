<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
    // Quelques fonctions pour avoir les infos des offres

    function getTypeOffre($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTypeOffre = "SELECT 
            CASE
                WHEN EXISTS (SELECT 1 FROM sae._offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                WHEN EXISTS (SELECT 1 FROM sae._offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc attraction'
                WHEN EXISTS (SELECT 1 FROM sae._offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                WHEN EXISTS (SELECT 1 FROM sae._offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                WHEN EXISTS (SELECT 1 FROM sae._offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                ELSE 'Inconnu'
            END AS offreSpe
            FROM sae._offre o
            WHERE o.id_offre = :id_offre;";

            try {
                $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $conn->prepare("SET SCHEMA 'sae';")->execute();

                // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqTypeOffre);
                $stmt->bindParam(':id_offre', $id_offre, PDO::PARAM_INT); // Lié à l'ID de l'offre
                $stmt->execute();
                $type_offre = $stmt->fetch(PDO::FETCH_COLUMN);

                $conn = null;
                return $type_offre;
            } catch(Exception $e) {
                print "Erreur !: " . $e->getMessage() . "<br>";
                die();
            }
    }

    function getFirstIMG($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqIMG = "SELECT img.lien_fichier 
            FROM sae._image img
            JOIN sae._offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = :id_offre
            LIMIT 1;";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            // Préparer et exécuter la requête
            $stmtIMG = $conn->prepare($reqIMG);
            $stmtIMG->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtIMG->execute();

            // Récupérer la première image
            $image = $stmtIMG->fetch(PDO::FETCH_ASSOC);

            if ($image && !empty($image['lien_fichier'])) {
                // Afficher l'image si elle existe
                $lienIMG = $image['lien_fichier'];
            } else {
                // Afficher une image par défaut
                $lienIMG = 'default-image.jpg';
            }

            $conn = null;
            return $lienIMG;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function getIMGbyId($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqIMG = "SELECT img.lien_fichier 
            FROM sae._image img
            JOIN sae._offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = :id_offre;";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            // Préparer et exécuter la requête
            $stmtIMG = $conn->prepare($reqIMG);
            $stmtIMG->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtIMG->execute();

            // Récupérer toutes les images sous forme de tableau
            $images = $stmtIMG->fetchAll(PDO::FETCH_COLUMN);

            // Si aucune image trouvée, retourner une image par défaut
            if (empty($images)) {
                $images[] = 'default-image.jpg';
            }

            $conn = null;
            return $images;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function isOpen() {
        global $driver, $server, $dbname, $user, $pass;
        // une offre est ouverte si sa date de fermeture est sup à la date d'ouverture
        $reqDateOuv = "SELECT date_heure from sae._dates_mise_hors_ligne_offre where id_offre = :id_offre;";
        $reqDateFer = "SELECT date_heure from sae._dates_mise_en_ligne_offre where id_offre = :id_offre;";

        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            // Préparer et exécuter les requêtes des dates d'ouverture
            $stmtDateOuv = $conn->prepare($reqDateOuv);
            $stmtDateOuv->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDateOuv->execute();

            // Préparer et exécuter les requêtes des dates de fermeture
            $stmtDateFer = $conn->prepare($reqDateFer);
            $stmtDateFer->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDateFer->execute();

            // Récupérer les dates d'ouvertures
            $images = $stmtDateOuv->fetchAll(PDO::FETCH_ASSOC);
            // Récupérer les dates de fermetures
            $images = $stmtDateOuv->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function getNoteMoyenne($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqNote = "SELECT AVG(note)
            FROM sae._avis
            WHERE id_offre = :id_offre";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            $stmtNOTE = $conn->prepare($reqNote);
            $stmtNOTE->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtNOTE->execute();

            $moyenne = $stmtNOTE->fetch(PDO::FETCH_ASSOC);

            $conn = null;
            return $moyenne["avg"];
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function getNombreNotes($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqNote = "SELECT COUNT(*)
            FROM sae._avis
            WHERE id_offre = :id_offre";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            $stmtNOTE = $conn->prepare($reqNote);
            $stmtNOTE->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtNOTE->execute();

            $moyenne = $stmtNOTE->fetch(PDO::FETCH_ASSOC);

            $conn = null;
            return $moyenne["count"];
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

// ===== GESTION DES OFFRES ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre ===== //
    function getOffre($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqOffre = "SELECT * FROM _offre WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtOffre = $conn->prepare($reqOffre);
            $stmtOffre->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtOffre->execute();
            $offre = $stmtOffre->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $offre;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre si l'offre est une activité ===== //
    function getActivite($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqActivite = "SELECT * FROM _offre NATURAL JOIN _offre_activite WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtActivite = $conn->prepare($reqActivite);
            $stmtActivite->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtActivite->execute();
            $activite = $stmtActivite->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $activite;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre si l'offre est une visite ===== //
    function getVisite($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqVisite = "SELECT * FROM _offre NATURAL JOIN _offre_visite WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtVisite = $conn->prepare($reqVisite);
            $stmtVisite->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtVisite->execute();
            $visite = $stmtVisite->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $visite;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre si l'offre est un spectacle ===== //
    function getSpectacle($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqSpectacle = "SELECT * FROM sae._offre NATURAL JOIN sae._offre_spectacle JOIN sae._date ON sae._offre_spectacle.date_evenement = sae._date.id_date WHERE id_offre= :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtSpectacle = $conn->prepare($reqSpectacle);
            $stmtSpectacle->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtSpectacle->execute();
            $spectacle = $stmtSpectacle->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $spectacle;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre si l'offre est un parc d'attractions ===== //
    function getParcAttraction($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqAttraction = "SELECT * FROM _offre NATURAL JOIN _offre_parc_attraction WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtAttraction = $conn->prepare($reqAttraction);
            $stmtAttraction->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtAttraction->execute();
            $attraction = $stmtAttraction->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $attraction;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations d'une offre si l'offre est un restaurant ===== //
    function getRestaurant($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqRestaurant = "SELECT * FROM _offre NATURAL JOIN _offre_restauration WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtRestaurant = $conn->prepare($reqRestaurant);
            $stmtRestaurant->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtRestaurant->execute();
            $restaurant = $stmtRestaurant->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $restaurant;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

// ===== GESTION DES ADRESSES ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations de l'adresse de l'offre ===== //
    function getAdresse($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqAdresse = "SELECT * FROM _offre JOIN _adresse ON _offre.id_adresse = _adresse.id_adresse WHERE _offre.id_offre =  :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtAdresse = $conn->prepare($reqAdresse);
            $stmtAdresse->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtAdresse->execute();
            $adresse = $stmtAdresse->fetch(PDO::FETCH_ASSOC); 
            $conn = null;
            return $adresse;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        } 
    }  

// ===== GESTION DES COMPTES ===== //

    // ===== Requête SQL pour récupérer les informations du compte du propriétaire de l'offre ===== //
    function getCompte($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqCompte = "SELECT * FROM _offre JOIN _compte ON  _offre.id_compte_professionnel = _compte.id_compte JOIN _compte_professionnel ON _compte.id_compte = _compte_professionnel.id_compte WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtCompte = $conn->prepare($reqCompte);
            $stmtCompte->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtCompte->execute();
            $compte = $stmtCompte->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $compte;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }


// ===== GESTION DES TAGS ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les tags d'une offre ===== //
    function getTags($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTags = "SELECT nom_tag FROM _offre_possede_tag NATURAL JOIN _tag WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtTags = $conn->prepare($reqTags);
            $stmtTags->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtTags->execute();
            $tags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $tags;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }
 
// ===== GESTION DES TARIFS ===== //
  
    // ===== Fonction qui exécute une requête SQL pour récupérer les différents tarifs d'une offre ===== //
    function getTarifs($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTarifs = "SELECT * FROM _offre NATURAL JOIN _tarif_publique WHERE _offre.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtTarifs = $conn->prepare($reqTarifs);
            $stmtTarifs->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtTarifs->execute();
            $tarifs = $stmtTarifs->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $tarifs;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

// ===== GESTION DE L'OUVERTURE ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les horaires d'ouverture d'une offre ===== //
    function getHorairesOuverture($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqHoraire = "SELECT * FROM _horaires_du_jour JOIN _horaire ON _horaires_du_jour.id_horaires_du_jour = _horaire.horaires_du_jour WHERE _horaires_du_jour.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);    
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtHoraire = $conn->prepare($reqHoraire);
            $stmtHoraire->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtHoraire->execute();
            $horaire = $stmtHoraire->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $horaire;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

// ===== GESTION DES AVIS ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les avis d'une offre ===== //
    function getAvis($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqAvis = "SELECT * FROM _offre JOIN _avis ON _offre.id_offre = _avis.id_offre WHERE _offre.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtAvis = $conn->prepare($reqAvis);
            $stmtAvis->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtAvis->execute();
            $avis = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $avis;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer la note détaillée d'une offre de restauration ===== //
    function getAvisDetaille($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqAvisDetaille = "SELECT * FROM _offre JOIN _avis ON _offre.id_offre = _avis.id_offre JOIN _note_detaillee ON _avis.id_offre = _note_detaillee.id_offre AND _avis.id_membre = _note_detaillee.id_membre WHERE _avis.id_membre = _note_detaillee.id_membre AND _avis.id_offre = _note_detaillee.id_offre AND _offre.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtAvisDetaille = $conn->prepare($reqAvisDetaille);
            $stmtAvisDetaille->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtAvisDetaille->execute();
            $avisDetaille = $stmtAvisDetaille->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $avisDetaille;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations des membres ayant publié un avis sur l'offre ===== //
    function getInformationsMembre($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqMembre = "SELECT * FROM _avis NATURAL JOIN compte_membre WHERE _avis.id_membre = compte_membre.id_compte AND _avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtMembre = $conn->prepare($reqMembre);
            $stmtMembre->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtMembre->execute();
            $membre = $stmtMembre->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $membre;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer la date de visite d'une personne yant rédigé un avis sur une offre ===== //
    function getDatePassage($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDatePassage = "SELECT * FROM _avis NATURAL JOIN _date WHERE _avis.visite_le = _date.id_date AND _avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDatePassage = $conn->prepare($reqDatePassage);
            $stmtDatePassage->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDatePassage->execute();
            $datePassage = $stmtDatePassage->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $datePassage;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function getImageAvis($id_offre, $id_membre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqImageAvis = "SELECT lien_fichier FROM _avis_contient_image WHERE _avis_contient_image.id_offre = :id_offre AND _avis_contient_image.id_membre = :id_membre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtImageAvis = $conn->prepare($reqImageAvis);
            $stmtImageAvis->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtImageAvis->bindParam(':id_membre', $id_membre, PDO::PARAM_INT);
            $stmtImageAvis->execute();
            $imageAvis = $stmtImageAvis->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $imageAvis;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les informations des membres ayant publié un avis sur une offre ===== //
    function getDatePublication($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDatePublication = "SELECT * FROM _avis NATURAL JOIN _date WHERE _avis.publie_le = _date.id_date AND _avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDatePublication = $conn->prepare($reqDatePublication);
            $stmtDatePublication->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDatePublication->execute();
            $datePublication = $stmtDatePublication->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $datePublication;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }
    
// ===== GESTION DES RÉPONSES ===== //

    // ===== Fonction qui exécute une requête SQL pour récupérer les réponses d'un avis d'une offre ===== //
    function getReponse($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqReponse = "SELECT * FROM _avis JOIN _reponse ON _avis.id_membre = _reponse.id_membre AND _avis.id_offre = _reponse.id_offre WHERE _avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtReponse = $conn->prepare($reqReponse);
            $stmtReponse->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtReponse->execute();
            $reponse = $stmtReponse->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $reponse;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }
    // ===== Fonction qui exécute une requête SQL pour récupérer la date de publication de la réponse à un avis sur une offre ===== //
    function getDatePublicationReponse($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDatePublicationReponse = "SELECT * FROM _avis JOIN _reponse ON _avis.id_membre = _reponse.id_membre AND _avis.id_offre = _reponse.id_offre JOIN _date ON _reponse.publie_le = _date.id_date WHERE _avis.id_membre = _reponse.id_membre AND _avis.id_offre = _reponse.id_offre AND _avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDatePublicationReponse = $conn->prepare($reqDatePublicationReponse);
            $stmtDatePublicationReponse->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDatePublicationReponse->execute();
            $datePublicationReponse = $stmtDatePublicationReponse->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $datePublicationReponse;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui update le nombre de pouces en haut ou en bas =====//
    function updatePouce($id_avis, $type, $action) {
        global $driver, $server, $dbname, $user, $pass;

        // Determine the column to update based on the type
        $column = $type === 'haut' ? 'nb_pouce_haut' : 'nb_pouce_bas';

        // Determine whether to increment or decrement
        $increment = $action === 'add' ? '+ 1' : '- 1';

        // SQL query to modify the count
        $reqUpdate = "UPDATE _avis SET $column = $column $increment WHERE id_avis = :id_avis";

        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            $stmtUpdate = $conn->prepare($reqUpdate);
            $stmtUpdate->bindParam(':id_avis', $id_avis, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $conn = null;
            return true;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer le prix le plus petit sur une offre ===== //
    function getPrixPlusPetit($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqPrix = "SELECT MIN(prix) FROM _tarif_publique WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtPrix = $conn->prepare($reqPrix);
            $stmtPrix->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtPrix->execute();
            $prixPlusPetit = $stmtPrix->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $prixPlusPetit[0]["min"];
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les ids des offres à la une ===== //
    function getIdALaUne() {
        global $driver, $server, $dbname, $user, $pass;
        $reqALaUne = "SELECT id_offre FROM sae._offre_souscrit_option WHERE nom_option = 'À la Une'";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtALaUne = $conn->prepare($reqALaUne);
            $stmtALaUne->execute();
            $ALaUne = $stmtALaUne->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $ALaUne;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les dates des offres visites ===== //
    function getDateVisite($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDate = "SELECT date FROM _date JOIN _offre_visite ON _date.id_date = _offre_visite.date_evenement AND id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDate = $conn->prepare($reqDate);
            $stmtDate->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDate->execute();
            $date = $stmtDate->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $date;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

     // ===== Fonction qui exécute une requête SQL pour récupérer les dates des offres spectacles ===== //
    function getDateSpectacle($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDate = "SELECT date FROM _date JOIN _offre_spectacle ON _date.id_date = _offre_spectacle.date_evenement AND id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDate = $conn->prepare($reqDate);
            $stmtDate->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDate->execute();
            $date = $stmtDate->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $date;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function isOffreEnRelief($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqEnRelief = "SELECT 1 FROM sae._offre_souscrit_option WHERE nom_option = 'En Relief' AND id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtEnRelief = $conn->prepare($reqEnRelief);
            $stmtEnRelief->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtEnRelief->execute();
            $EnRelief = $stmtEnRelief->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $EnRelief !== false;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }


    function isOffreALaUne($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqALaUne = "SELECT 1 FROM sae._offre_souscrit_option WHERE nom_option = 'À la Une' AND id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtALaUne = $conn->prepare($reqALaUne);
            $stmtALaUne->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtALaUne->execute();
            $ALaUne = $stmtALaUne->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $ALaUne !== false;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise hors ligne existe pour une offre ===== //
    function isOffreHorsLigne($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDate = "SELECT 1 FROM sae._offre_dates_mise_hors_ligne WHERE id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDate = $conn->prepare($reqDate);
            $stmtDate->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDate->execute();
            $date = $stmtDate->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $date !== false;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise hors ligne existe pour une offre ===== //
    function getDateOffreHorsLigne($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDate = "SELECT date FROM sae._date NATURAL JOIN sae._offre_dates_mise_hors_ligne  WHERE id_offre = :id_offre ORDER BY date DESC LIMIT 1";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDate = $conn->prepare($reqDate);
            $stmtDate->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDate->execute();
            $date = $stmtDate->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $date;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour vérifier si une date de mise en ligne existe pour une offre ===== //
    function getDateOffreEnLigne($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqDate = "SELECT date FROM sae._date NATURAL JOIN sae._offre_dates_mise_en_ligne  WHERE id_offre = :id_offre ORDER BY date DESC LIMIT 1";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDate = $conn->prepare($reqDate);
            $stmtDate->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDate->execute();
            $date = $stmtDate->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $date;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les ids des offres crées récemment ===== //
    function getIdOffresRecentes() {
        global $driver, $server, $dbname, $user, $pass;
        $reqALaUne = "SELECT sae._offre.id_offre FROM sae._offre JOIN sae._offre_dates_mise_en_ligne ON sae._offre.id_offre = sae._offre_dates_mise_en_ligne.id_offre JOIN sae._date ON sae._offre_dates_mise_en_ligne.id_date = sae._date.id_date ORDER BY sae._date.date ASC LIMIT 3";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $stmtALaUne = $conn->prepare($reqALaUne);
            $stmtALaUne->execute();
            $ALaUne = $stmtALaUne->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $ALaUne;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    // ===== Fonction qui exécute une requête SQL pour récupérer les ids des offres crées récemment ===== //
    function getIdMembresContientAvis($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqContientAvis = "SELECT sae._avis.id_membre from sae._avis WHERE sae._avis.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtContientAvis = $conn->prepare($reqContientAvis);
            $stmtContientAvis->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtContientAvis->execute();
            $contientAvis = $stmtContientAvis->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $contientAvis;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function addConsultedOffer($idOffre) {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        if (!isset($_SESSION['recent_offers'])) {
            $_SESSION['recent_offers'] = [];
        }
    
        // Évitez les doublons, mais ajoutez les nouveaux à la fin
        if (!in_array($idOffre, $_SESSION['recent_offers'])) {
            $_SESSION['recent_offers'][] = $idOffre;
        }
    
        // Limitez à 10 offres consultées
        if (count($_SESSION['recent_offers']) > 10) {
            array_shift($_SESSION['recent_offers']); // Supprime le plus ancien
        }
    }

    function getConsultedOffers() {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        if (!isset($_SESSION['recent_offers']) || empty($_SESSION['recent_offers'])) {
            return [];
        }
    
        try {
            // Connexion à la base de données
            global $driver, $server, $dbname, $user, $pass;
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
            // Préparation de la requête
            $reversedOffers = array_reverse($_SESSION['recent_offers']); // Reverse the order
            $placeholders = implode(',', array_fill(0, count($reversedOffers), '?'));
            $query = "SELECT id_offre FROM sae._offre WHERE id_offre IN ($placeholders)";
            $stmt = $dbh->prepare($query);
            $stmt->execute($reversedOffers);
            $offers = $stmt->fetchAll();
    
            // Sort offers to match the reversed order in $reversedOffers
            $offerMap = [];
            foreach ($offers as $offer) {
                $offerMap[$offer['id_offre']] = $offer;
            }
            $sortedOffers = array_map(function ($id) use ($offerMap) {
                return $offerMap[$id] ?? null; // Handle cases where an ID may not exist in the database
            }, $reversedOffers);
    
            $dbh = null;
            return array_filter($sortedOffers); // Remove any null values
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des offres : " . $e->getMessage();
            return [];
        }
    }
    
    function getDatePublicationOffre($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        // SELECT sae._date.date FROM sae._offre JOIN sae._offre_dates_mise_en_ligne ON sae._offre.id_offre = sae._offre_dates_mise_en_ligne.id_offre JOIN sae._date ON sae._offre_dates_mise_en_ligne.id_date = sae._date.id_date WHERE sae._offre.id_offre = 19;
        $reqDatePublicationOffre = "SELECT sae._date.date FROM sae._offre JOIN sae._offre_dates_mise_en_ligne ON sae._offre.id_offre = sae._offre_dates_mise_en_ligne.id_offre JOIN sae._date ON sae._offre_dates_mise_en_ligne.id_date = sae._date.id_date WHERE sae._offre.id_offre = :id_offre";
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->prepare("SET SCHEMA 'sae';")->execute();
            $stmtDatePublicationOffre = $conn->prepare($reqDatePublicationOffre);
            $stmtDatePublicationOffre->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtDatePublicationOffre->execute();
            $datePublicationOffre = $stmtDatePublicationOffre->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $datePublicationOffre;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

function getNbSemaine($date, $today) {
    // Convertir la date de la base de données en objet DateTime
    $dateFromDbObj = new DateTime($date);

    // Calculer la différence entre les deux dates
    $interval = $dateFromDbObj->diff($today);

    // Obtenir la différence en jours
    $daysDifference = $interval->days;

    // Convertir la différence en semaines (en supposant que 1 semaine = 7 jours)
    $weeksDifference = floor($daysDifference / 7);
    return $weeksDifference;
}
function getOffreTTC($prix, $nb, $TVA) {
    return ($prix*$nb)*(1+$TVA/100);
}
?>