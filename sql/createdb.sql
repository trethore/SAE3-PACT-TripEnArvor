ROLLBACK;
START TRANSACTION;


DROP SCHEMA IF EXISTS sae CASCADE;
CREATE SCHEMA sae;
SET SCHEMA 'sae';


/* ##################################################################### */
/*                                 TYPES                                 */
/* ##################################################################### */


CREATE TYPE gamme_prix_t AS ENUM ('€', '€€', '€€€');
CREATE TYPE type_repas_t AS ENUM ('Petit-déjeuner', 'Brunch', 'Déjeuner', 'Dîner', 'Boissons');
CREATE TYPE jour_t AS ENUM ('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
CREATE TYPE type_offre_t AS ENUM ('gratuite', 'standard', 'premium');
CREATE TYPE contexte_visite_t AS ENUM ('affaires', 'couple', 'famille', 'amis', 'solo');
CREATE TYPE nom_option_t AS ENUM ('En Relief', 'À la Une');


CREATE TABLE _date (
    id_date     SERIAL,
    date        TIMESTAMP NOT NULL,
    CONSTRAINT _date_pk PRIMARY KEY (id_date)
);


CREATE TABLE _prix (
    id_prix SERIAL,
    prix_ht INTEGER NOT NULL,
    CONSTRAINT _prix_pk PRIMARY KEY (id_prix)
);

CREATE TABLE _abonnement (
    nom     VARCHAR(63),
    id_prix INTEGER NOT NULL,
    CONSTRAINT _abonnement_pk
        PRIMARY KEY (nom),
    CONSTRAINT _abonnement_fk_prix
        FOREIGN KEY (id_prix) REFERENCES _prix(id_prix)
);


/* ##################################################################### */
/*                                COMPTES                                */
/* ##################################################################### */


/* ========================== COMPTE ABSTRAIT ========================== */

CREATE TABLE _compte (
    id_compte       SERIAL,
    nom_compte      VARCHAR(30),
    prenom          VARCHAR(30),
    email           VARCHAR(320) UNIQUE NOT NULL,
    tel             VARCHAR(12),
    mot_de_passe    VARCHAR(255) NOT NULL,
    id_adresse      INTEGER,
    CONSTRAINT _compte_pk PRIMARY KEY (id_compte)
);


/* =================== COMPTE PROFESSIONNEL ABSTRAIT =================== */

CREATE TABLE _compte_professionnel (
    id_compte       INTEGER,
    denomination    VARCHAR(255) NOT NULL,
    a_propos         VARCHAR(255) NOT NULL,
    site_web         VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_fk_compte FOREIGN KEY (id_compte) REFERENCES _compte(id_compte)
);


/* ================ COMPTE PROFESSIONNEL PRIVÉ CONCRET ================= */

CREATE TABLE _compte_professionnel_prive (
    id_compte   INTEGER,
    siren       VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_prive_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_prive_fk_compte_professionnel FOREIGN KEY (id_compte) REFERENCES _compte_professionnel(id_compte)
);

CREATE VIEW compte_professionnel_prive AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_professionnel
    NATURAL JOIN _compte_professionnel_prive
;


/* =============== COMPTE PROFESSIONNEL PUBLIQUE CONCRET =============== */

CREATE TABLE _compte_professionnel_publique (
    id_compte   INTEGER,
    CONSTRAINT _compte_professionnel_publique_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_publique_fk_compte_professionnel FOREIGN KEY (id_compte) REFERENCES _compte_professionnel(id_compte)
);

CREATE VIEW compte_professionnel_publique AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_professionnel
    NATURAL JOIN _compte_professionnel_publique
;


/* ======================= COMPTE MEMBRE CONCRET ======================= */

CREATE TABLE _compte_membre (
    id_compte   INTEGER,
    pseudo      VARCHAR(255) UNIQUE NOT NULL,
    CONSTRAINT _compte_membre_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_membre_fk_compte FOREIGN KEY (id_compte) REFERENCES _compte(id_compte)
);

CREATE VIEW compte_membre AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_membre
;



/* ##################################################################### */
/*                                OFFRES                                 */
/* ##################################################################### */


/* ========================== OFFRE ABSTRAITE ========================== */

CREATE TABLE _offre (
    id_offre                SERIAL,
    titre                   VARCHAR(128) NOT NULL,
    resume                  VARCHAR(255) NOT NULL,
    ville                   VARCHAR(255) NOT NULL,
    description_detaille    VARCHAR(1024),
    site_web                VARCHAR(255),
    id_compte_professionnel INTEGER NOT NULL,
    id_adresse              INTEGER,
    abonnement              VARCHAR(63) NOT NULL,
    CONSTRAINT _offre_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_fk_compte_professionnel FOREIGN KEY (id_compte_professionnel) REFERENCES _compte_professionnel(id_compte),
    CONSTRAINT _offre_fk_abonnement FOREIGN KEY (abonnement) REFERENCES _abonnement(nom)
);


/* ====================== OFFRE ACTIVITÉ CONCRETE ====================== */

CREATE TABLE _offre_activite (
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    age_min     INTEGER NOT NULL,
    CONSTRAINT _offre_activite_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_activite_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);

CREATE VIEW offre_activite AS
    SELECT *
    FROM _offre_activite
    NATURAL JOIN _offre
;


/* ======================= OFFRE VISITE CONCRETE ======================= */

CREATE TABLE _offre_visite (
    id_offre        INTEGER,
    duree           INTEGER NOT NULL,
    date_evenement  INTEGER NOT NULL,
    CONSTRAINT _offre_visite_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_visite_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_visite_fk_date FOREIGN KEY (date_evenement) REFERENCES _date(id_date)
);

CREATE VIEW offre_visite AS
    SELECT *
    FROM _offre_visite
    NATURAL JOIN _offre
;


/* ===================== OFFRE SPECTACLE CONCRETE ====================== */

CREATE TABLE _offre_spectacle (
    id_offre       INTEGER,
    duree          INTEGER NOT NULL,
    capacite       INTEGER NOT NULL,
    date_evenement INTEGER NOT NULL,
    CONSTRAINT _offre_spectacle_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_spectacle_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_spectacle_fk_date FOREIGN KEY (date_evenement) REFERENCES _date(id_date)

);

CREATE VIEW offre_spectacle AS
    SELECT *
    FROM _offre_spectacle
    NATURAL JOIN _offre
;


/* ================= OFFRE PARC D'ATTRACTIONS CONCRETE ================= */

CREATE TABLE _offre_parc_attraction (
    id_offre         INTEGER,
    nb_attractions   INTEGER NOT NULL,
    age_min          INTEGER NOT NULL,
    plan            VARCHAR(255) NOT NULL,
    CONSTRAINT _offre_parc_attraction_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_parc_attraction_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);

CREATE VIEW offre_parc_attraction AS
    SELECT * 
    FROM _offre_parc_attraction
    NATURAL JOIN _offre
;


/* ==================== OFFRE RESTAURATION CONCRETE ==================== */

CREATE TABLE _offre_restauration (
    id_offre    INTEGER,
    gamme_prix  gamme_prix_t NOT NULL,
    carte       VARCHAR(255) NOT NULL,
    CONSTRAINT _offre_restauration_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_restauration_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);

CREATE VIEW offre_restauration AS
    SELECT *
    FROM _offre_restauration
    NATURAL JOIN _offre
;

/* ============================== OPTIONS ============================== */

CREATE TABLE _option (
    nom_option  nom_option_t,
    id_prix INTEGER NOT NULL,
    CONSTRAINT _option_pk PRIMARY KEY (nom_option),
    CONSTRAINT _option_fk_prix FOREIGN KEY (id_prix) REFERENCES _prix(id_prix)
);


/* ##################################################################### */
/*                                 AVIS                                  */
/* ##################################################################### */


CREATE TABLE _avis (
    id_avis         SERIAL, -- // TODO Supprimer id_avis et mettre (id_membre, id_offre) comme clé primaire
    id_membre       INTEGER NOT NULL,
    id_offre        INTEGER NOT NULL,
    note            INTEGER NOT NULL,
    titre           VARCHAR(128) NOT NULL,
    commentaire     VARCHAR(1024) NOT NULL,
    nb_pouce_haut   INTEGER NOT NULL DEFAULT 0,
    nb_pouce_bas    INTEGER NOT NULL DEFAULT 0,
    contexte_visite contexte_visite_t NOT NULL,
    publie_le       INTEGER NOT NULL,
    visite_le       INTEGER NOT NULL,
    CONSTRAINT _avis_pk PRIMARY KEY (id_avis),
    -- CONSTRAINT _avis_pk PRIMARY KEY (id_membre, id_offre),
    CONSTRAINT _avis_fk_membre FOREIGN KEY (id_membre) REFERENCES _compte_membre(id_compte),
    CONSTRAINT _avis_fk_date_visite FOREIGN KEY (publie_le) REFERENCES _date(id_date),
    CONSTRAINT _avis_fk_id_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _avis_fk_date_publie FOREIGN KEY (visite_le) REFERENCES _date(id_date)
);


CREATE TABLE _reponse (
    id_avis     INTEGER,
    texte       VARCHAR(1024) NOT NULL,
    publie_le   INTEGER NOT NULL,
    CONSTRAINT _reponse_pk PRIMARY KEY (id_avis),
    CONSTRAINT _reponse_fk_avis FOREIGN KEY (id_avis) REFERENCES _avis(id_avis),
    CONSTRAINT _reponse_fk_date FOREIGN KEY (publie_le) REFERENCES _date(id_date)
);


/* ========================== NOTE DÉTAILLÉE =========================== */
CREATE TABLE _note_detaillee (
    id_note   SERIAL,  
    nom_note  VARCHAR(30) NOT NULL,
    note      INTEGER NOT NULL,
    id_avis   INTEGER NOT NULL,
    CONSTRAINT _note_pk PRIMARY KEY (id_note),
    CONSTRAINT _note_fk_avis FOREIGN KEY (id_avis) REFERENCES _avis(id_avis)
);



/* ##################################################################### */
/*                              UTILITAIRES                              */
/* ##################################################################### */


CREATE TABLE _adresse (
    id_adresse          SERIAL,
    num_et_nom_de_voie  VARCHAR(255) NOT NULL,
    complement_adresse  VARCHAR(255),
    code_postal         VARCHAR(6) NOT NULL,
    ville               VARCHAR(255) NOT NULL,
    pays                VARCHAR(255) NOT NULL,
    CONSTRAINT _adresse_pk PRIMARY KEY (id_adresse)
);


CREATE TABLE _prestation (
    nom_prestation  VARCHAR(128),
    description     VARCHAR(1024) NOT NULL,
    CONSTRAINT _prestation_pk PRIMARY KEY (nom_prestation)
);


CREATE TABLE _langue (
    nom_langue  VARCHAR(128),
    CONSTRAINT _langue_pk PRIMARY KEY (nom_langue)
);


CREATE TABLE _type_repas (
    type_repas  type_repas_t,
    CONSTRAINT _type_repas_pk PRIMARY KEY (type_repas)    
);


CREATE TABLE _image (
    lien_fichier    VARCHAR(255),
    CONSTRAINT _image_pk PRIMARY KEY (lien_fichier)
);


CREATE TABLE _tarif_publique (
    id_tarif_publique    SERIAL,
    nom_tarif VARCHAR(64),
    prix        INTEGER NOT NULL,
    id_offre    INTEGER NOT NULL,
    CONSTRAINT _tarif_publique_pk PRIMARY KEY (id_tarif_publique),
    CONSTRAINT _tarif_publique_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _horaires_du_jour (
    id_horaires_du_jour   SERIAL,
    nom_jour            jour_t NOT NULL,
    id_offre            INTEGER NOT NULL,
    CONSTRAINT _horaires_du_jour_pk PRIMARY KEY (id_horaires_du_jour),
    CONSTRAINT _horaires_du_jour_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _horaire (
    id_horaire          SERIAL,
    ouverture           CHAR(5),
    fermeture           CHAR(5),
    horaires_du_jour    INTEGER,
    CONSTRAINT _horaire_pk PRIMARY KEY (id_horaire),
    CONSTRAINT _horaire_fk_horaires_du_jour FOREIGN KEY (horaires_du_jour) REFERENCES _horaires_du_jour(id_horaires_du_jour)
);


CREATE TABLE _tag (
    nom_tag  VARCHAR(64),
    CONSTRAINT _tag_pk PRIMARY KEY (nom_tag)
);
/* ##################################################################### */
/*                                FACTURES                               */
/* ##################################################################### */


CREATE TABLE _facture(
    numero_facture    SERIAL,
    montant_ht        INTEGER NOT NULL,
    id_date_emission  INTEGER NOT NULL,
    id_date_echeance  INTEGER NOT NULL,
    id_offre          INTEGER NOT NULL,
    CONSTRAINT _facture_pk
        PRIMARY KEY (numero_facture),
    CONSTRAINT _facture_fk_offre
        FOREIGN KEY (id_offre)
        REFERENCES _offre(id_offre),
    CONSTRAINT _facture_fk_date_emission
        FOREIGN KEY (id_date_emission)
        REFERENCES _date(id_date),
    CONSTRAINT _facture_fk_date_echeance
        FOREIGN KEY (id_date_echeance)
        REFERENCES _date(id_date)    
);



/* ##################################################################### */
/*                              ASSOCIATIONS                             */
/* ##################################################################### */


/* ======================= COMPTE HABITE ADRESSE ======================= */

ALTER TABLE _compte
    ADD CONSTRAINT _compte_fk_adresse 
    FOREIGN KEY (id_adresse) REFERENCES _adresse(id_adresse)
;


/* ===================== OFFRE DATES MISE EN LIGNE ===================== */

CREATE TABLE _offre_dates_mise_en_ligne (
    id_offre    INTEGER,
    id_date     INTEGER,
    CONSTRAINT _offre_dates_mise_en_ligne_pk PRIMARY KEY (id_offre, id_date),
    CONSTRAINT _offre_dates_mise_en_ligne_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_dates_mise_en_ligne_fk_date FOREIGN KEY (id_date) REFERENCES _date(id_date)
);


/* ==================== OFFRE DATES MISE HORS LIGNE ==================== */

CREATE TABLE _offre_dates_mise_hors_ligne (
    id_offre    INTEGER,
    id_date     INTEGER,
    CONSTRAINT _offre_dates_mise_hors_ligne_pk PRIMARY KEY (id_offre, id_date),
    CONSTRAINT _offre_dates_mise_hors_ligne_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_dates_mise_hors_ligne_fk_date FOREIGN KEY (id_date) REFERENCES _date(id_date)
);


/* ======================= OFFRE SOUSCRIT OPTION ======================= */

CREATE TABLE _offre_souscrit_option (
    id_offre    INTEGER,
    nom_option  nom_option_t,
    nb_semaine  INTEGER NOT NULL,
    id_date     INTEGER NOT NULL,
    CONSTRAINT _offre_souscrit_option_pk
        PRIMARY KEY (id_offre, nom_option),
    CONSTRAINT _offre_souscrit_option_fk_offre
        FOREIGN KEY (id_offre)
        REFERENCES _offre(id_offre),
    CONSTRAINT _offre_souscrit_option_fk_option
        FOREIGN KEY (nom_option)
        REFERENCES _option(nom_option)
);


/* ====================== OFFRE SE SITUE ADRESSE ======================= */

ALTER TABLE _offre
    ADD CONSTRAINT _offre_fk_adresse 
    FOREIGN KEY (id_adresse) REFERENCES _adresse(id_adresse)
;


/* ======================= OFFRE CONTIENT IMAGE ======================== */

CREATE TABLE _offre_contient_image (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_contient_image_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_contient_image_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_contient_image_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
);


/* ================= OFFRE ACTIVITÉ PROPOSE PRESTATION ================= */

CREATE TABLE _offre_activite_propose_prestation (
    nom_prestation      VARCHAR(128),
    id_offre_activite   INTEGER,
    CONSTRAINT _offre_activite_propose_prestation_pk PRIMARY KEY (nom_prestation, id_offre_activite),
    CONSTRAINT _offre_activite_propose_prestation_fk_offre_activite FOREIGN KEY (id_offre_activite) REFERENCES _offre_activite(id_offre),
    CONSTRAINT _offre_activite_propose_prestation_fk_prestation FOREIGN KEY (nom_prestation) REFERENCES _prestation(nom_prestation)
);


/* ===================== OFFRE VISITE DANS LANGUE ====================== */

CREATE TABLE _offre_visite_dans_langue (
    id_offre_visite INTEGER,
    nom_langue      VARCHAR(128),
    CONSTRAINT _offre_visite_dans_langue_pk PRIMARY KEY (id_offre_visite, nom_langue),
    CONSTRAINT _offre_visite_dans_langue_fk_offre_visite FOREIGN KEY (id_offre_visite) REFERENCES _offre_visite(id_offre),
    CONSTRAINT _offre_visite_dans_langue_fk_langue FOREIGN KEY (nom_langue) REFERENCES _langue(nom_langue)
);


/* ================= OFFRE RESTAURATION PROPOSE REPAS ================== */

CREATE TABLE _offre_restauration_propose_repas (
    id_offre_restauration   INTEGER,
    type_repas              type_repas_t,
    CONSTRAINT _offre_restauration_propose_repas_pk PRIMARY KEY (id_offre_restauration, type_repas),
    CONSTRAINT _offre_restauration_propose_repas_fk_offre_restauration FOREIGN KEY (id_offre_restauration) REFERENCES _offre_restauration(id_offre),
    CONSTRAINT _offre_restauration_propose_repas_fk_type_repas FOREIGN KEY (type_repas) REFERENCES _type_repas(type_repas)
);


/* ========================= OFFRE POSSÈDE TAG ========================= */

CREATE TABLE _offre_possede_tag (
    id_offre    INTEGER,
    nom_tag     VARCHAR(64),
    CONSTRAINT _offre_possede_tag_pk PRIMARY KEY (id_offre, nom_tag),
    CONSTRAINT _offre_possede_tag_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_possede_tag_fk_tag FOREIGN KEY (nom_tag) REFERENCES _tag(nom_tag)
);


/* ======================== AVIS CONTIENT IMAGE ======================== */

CREATE TABLE _avis_contient_image (
    id_avis         INTEGER,
    lien_fichier    VARCHAR(255),
    CONSTRAINT _avis_contient_image_pk
        PRIMARY KEY (id_avis, lien_fichier),
    CONSTRAINT _avis_contient_image_fk_avis
        FOREIGN KEY (id_avis)
        REFERENCES _avis(id_avis),
    CONSTRAINT _avis_contient_image_fk_image
        FOREIGN KEY (lien_fichier)
        REFERENCES _image(lien_fichier)
);



/* ##################################################################### */
/*                       TRIGGERS TABLES ABSTRAITES                      */
/* ##################################################################### */


/* ============================== COMPTE =============================== */

CREATE VIEW totalite_compte AS
SELECT id_compte FROM _compte
EXCEPT
(
    SELECT id_compte FROM _compte_professionnel
    UNION
    SELECT id_compte FROM _compte_membre
);

CREATE FUNCTION _compte_is_abstract() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM totalite_compte;
    IF FOUND THEN
        RAISE EXCEPTION 'Vous ne pouvez pas instancier un _compte.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

DROP TRIGGER IF EXISTS tg_compte_is_abstract ON _compte;
CREATE CONSTRAINT TRIGGER tg_compte_is_abstract
AFTER INSERT
ON _compte
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE _compte_is_abstract();


/* ======================= COMPTE PROFESSIONNEL ======================== */

CREATE VIEW totalite_compte_professionnel AS
SELECT id_compte FROM _compte_professionnel
EXCEPT
(
    SELECT id_compte FROM _compte_professionnel_prive
    UNION
    SELECT id_compte FROM _compte_professionnel_publique
);

CREATE FUNCTION _compte_professionnel_is_abstract() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM totalite_compte_professionnel;
    IF FOUND THEN
        RAISE EXCEPTION 'Vous ne pouvez pas instancier un _compte_professionnel.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

DROP TRIGGER IF EXISTS tg_compte_professionnel_is_abstract ON _compte_professionnel;
CREATE CONSTRAINT TRIGGER tg_compte_professionnel_is_abstract
AFTER INSERT
ON _compte_professionnel
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE _compte_professionnel_is_abstract();


/* =============================== OFFRE =============================== */

CREATE VIEW totalite_offre AS
SELECT id_offre FROM _offre
EXCEPT
(
    SELECT id_offre FROM _offre_activite
    UNION
    SELECT id_offre FROM _offre_visite
    UNION
    SELECT id_offre FROM _offre_spectacle
    UNION
    SELECT id_offre FROM _offre_parc_attraction
    UNION
    SELECT id_offre FROM _offre_restauration
);

CREATE FUNCTION _offre_is_abstract() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM totalite_offre;
    IF FOUND THEN
        RAISE EXCEPTION 'Vous ne pouvez pas instancier une _offre.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

DROP TRIGGER IF EXISTS tg_offre_is_abstract ON _offre;
CREATE CONSTRAINT TRIGGER tg_offre_is_abstract
AFTER INSERT
ON _offre
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE _offre_is_abstract();



/* ##################################################################### */
/*                                  CRUD                                 */
/* ##################################################################### */


/* ==================== COMPTE PROFESSIONNEL PRIVÉ ===================== */

-- CREATE

CREATE FUNCTION create_compte_professionnel_prive() RETURNS TRIGGER AS $$
DECLARE
    id_compte_temp _compte.id_compte%type;
BEGIN
    INSERT INTO _compte(nom_compte, prenom, email, tel, mot_de_passe, id_adresse)
        VALUES (NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse)
        RETURNING id_compte INTO id_compte_temp;
    INSERT INTO _compte_professionnel(id_compte, denomination, a_propos, site_web) 
        VALUES (id_compte_temp, NEW.denomination, NEW.a_propos, NEW.site_web);
    INSERT INTO _compte_professionnel_prive(id_compte, siren)
        VALUES (id_compte_temp, NEW.siren);
    RETURN ROW(id_compte_temp, NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse, NEW.denomination, NEW.a_propos, NEW.site_web, NEW.siren);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_compte_professionnel_prive
INSTEAD OF INSERT
ON compte_professionnel_prive FOR EACH ROW
EXECUTE PROCEDURE create_compte_professionnel_prive();


-- READ

/* SELECT * FROM compte_professionnel_prive; */


-- UPDATE

CREATE FUNCTION update_compte_professionnel_prive() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_compte <> OLD.id_compte) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''un compte.';
    END IF;

    UPDATE _compte
    SET nom_compte = NEW.nom_compte,
        prenom = NEW.prenom,
        email = NEW.email,
        tel = NEW.tel,
        mot_de_passe = NEW.mot_de_passe,
        id_adresse = NEW.id_adresse
    WHERE id_compte = NEW.id_compte;

    UPDATE _compte_professionnel
    SET denomination = NEW.denomination,
        a_propos = NEW.a_propos,
        site_web = NEW.site_web
    WHERE id_compte = NEW.id_compte;

    UPDATE _compte_professionnel_prive
    SET siren = NEW.siren
    WHERE id_compte = NEW.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_compte_professionnel_prive
INSTEAD OF UPDATE
ON compte_professionnel_prive
FOR EACH ROW
EXECUTE PROCEDURE update_compte_professionnel_prive();


-- DELETE

CREATE FUNCTION delete_compte_professionnel_prive() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _compte_professionnel_prive
    WHERE id_compte = OLD.id_compte;

    DELETE FROM _compte_professionnel
    WHERE id_compte = OLD.id_compte;

    DELETE FROM _compte
    WHERE id_compte = OLD.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_compte_professionnel_prive
INSTEAD OF DELETE
ON compte_professionnel_prive
FOR EACH ROW
EXECUTE PROCEDURE delete_compte_professionnel_prive();


/* =================== COMPTE PROFESSIONNEL PUBLIQUE =================== */

-- CREATE

CREATE FUNCTION create_compte_professionnel_publique() RETURNS TRIGGER AS $$
DECLARE
    id_compte_temp _compte.id_compte%type;
BEGIN
    INSERT INTO _compte(nom_compte, prenom, email, tel, mot_de_passe, id_adresse)
        VALUES (NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse)
        RETURNING id_compte INTO id_compte_temp;
    INSERT INTO _compte_professionnel(id_compte, denomination, a_propos, site_web) 
        VALUES (id_compte_temp, NEW.denomination, NEW.a_propos, NEW.site_web);
    INSERT INTO _compte_professionnel_publique(id_compte)
        VALUES (id_compte_temp);
    RETURN ROW(id_compte_temp, NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse, NEW.denomination, NEW.a_propos, NEW.site_web);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_compte_professionnel_publique
INSTEAD OF INSERT
ON compte_professionnel_publique FOR EACH ROW
EXECUTE PROCEDURE create_compte_professionnel_publique();


-- READ

/* SELECT * FROM compte_professionnel_publique; */


-- UPDATE

CREATE FUNCTION update_compte_professionnel_publique() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_compte <> OLD.id_compte) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''un compte.';
    END IF;

    UPDATE _compte
    SET nom_compte = NEW.nom_compte,
        prenom = NEW.prenom,
        email = NEW.email,
        tel = NEW.tel,
        mot_de_passe = NEW.mot_de_passe,
        id_adresse = NEW.id_adresse
    WHERE id_compte = NEW.id_compte;

    UPDATE _compte_professionnel
    SET denomination = NEW.denomination,
        a_propos = NEW.a_propos,
        site_web = NEW.site_web
    WHERE id_compte = NEW.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_compte_professionnel_publique
INSTEAD OF UPDATE
ON compte_professionnel_publique
FOR EACH ROW
EXECUTE PROCEDURE update_compte_professionnel_publique();


-- DELETE

CREATE FUNCTION delete_compte_professionnel_publique() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _compte_professionnel_publique
    WHERE id_compte = OLD.id_compte;

    DELETE FROM _compte_professionnel
    WHERE id_compte = OLD.id_compte;

    DELETE FROM _compte
    WHERE id_compte = OLD.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_compte_professionnel_publique
INSTEAD OF DELETE
ON compte_professionnel_publique
FOR EACH ROW
EXECUTE PROCEDURE delete_compte_professionnel_publique();


/* =========================== COMPTE MEMBRE =========================== */

-- CREATE

CREATE FUNCTION create_compte_membre() RETURNS TRIGGER AS $$
DECLARE
    id_compte_temp _compte.id_compte%type;
BEGIN
    INSERT INTO _compte(nom_compte, prenom, email, tel, mot_de_passe, id_adresse)
        VALUES (NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse)
        RETURNING id_compte INTO id_compte_temp;
    INSERT INTO _compte_membre(id_compte, pseudo)
        VALUES (id_compte_temp, NEW.pseudo);
    RETURN ROW(id_compte_temp, NEW.nom_compte, NEW.prenom, NEW.email, NEW.tel, NEW.mot_de_passe, NEW.id_adresse, NEW.pseudo);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_compte_membre
INSTEAD OF INSERT
ON compte_membre FOR EACH ROW
EXECUTE PROCEDURE create_compte_membre();


-- READ

/* SELECT * FROM compte_membre; */


-- UPDATE

CREATE FUNCTION update_compte_membre() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_compte <> OLD.id_compte) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''un compte.';
    END IF;

    UPDATE _compte
    SET nom_compte = NEW.nom_compte,
        prenom = NEW.prenom,
        email = NEW.email,
        tel = NEW.tel,
        mot_de_passe = NEW.mot_de_passe,
        id_adresse = NEW.id_adresse
    WHERE id_compte = NEW.id_compte;

    UPDATE _compte_membre
    SET pseudo = NEW.pseudo
    WHERE id_compte = NEW.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_compte_membre
INSTEAD OF UPDATE
ON compte_membre
FOR EACH ROW
EXECUTE PROCEDURE update_compte_membre();


-- DELETE

CREATE FUNCTION delete_compte_membre() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _compte_membre
    WHERE id_compte = OLD.id_compte;

    DELETE FROM _compte
    WHERE id_compte = OLD.id_compte;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_compte_membre
INSTEAD OF DELETE
ON compte_membre
FOR EACH ROW
EXECUTE PROCEDURE delete_compte_membre();


/* ========================== OFFRE ACTIVITÉ =========================== */

-- CREATE

CREATE FUNCTION create_offre_activite() RETURNS TRIGGER AS $$
DECLARE
    id_offre_temp _offre.id_offre%type;
BEGIN
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_activite(id_offre, duree, age_min)
        VALUES (id_offre_temp, NEW.duree, NEW.age_min);
    RETURN ROW(id_offre_temp, NEW.duree, NEW.age_min, NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_offre_activite
INSTEAD OF INSERT
ON offre_activite
FOR EACH ROW
EXECUTE PROCEDURE create_offre_activite();


-- READ

/* SELECT * FROM offre_activite; */


-- UPDATE

CREATE FUNCTION update_offre_activite() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_offre <> OLD.id_offre) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''une offre.';
    END IF;

    IF (NEW.id_compte_professionnel <> OLD.id_compte_professionnel) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''auteur d''une offre.';
    END IF;

    UPDATE _offre
    SET titre = NEW.titre,
        resume = NEW.resume,
        ville = NEW.ville,
        description_detaille = NEW.description_detaille,
        site_web = NEW.site_web,
        id_adresse = NEW.id_adresse,
        abonnement = NEW.abonnement
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_activite
    SET duree = NEW.duree,
        age_min = NEW.age_min
    WHERE id_offre = NEW.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_offre_activite
INSTEAD OF UPDATE
ON offre_activite
FOR EACH ROW
EXECUTE PROCEDURE update_offre_activite();


-- DELETE

CREATE FUNCTION delete_offre_activite() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _offre_activite
    WHERE id_offre = OLD.id_offre;

    DELETE FROM _offre
    WHERE id_offre = OLD.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_offre_activite
INSTEAD OF DELETE
ON offre_activite
FOR EACH ROW
EXECUTE PROCEDURE delete_offre_activite();


/* =========================== OFFRE VISITE ============================ */

-- CREATE

CREATE FUNCTION create_offre_visite() RETURNS TRIGGER AS $$
DECLARE
    id_offre_temp _offre.id_offre%type;
BEGIN
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_visite(id_offre, duree,date_evenement)
        VALUES (id_offre_temp, NEW.duree, NEW.date_evenement);
    RETURN ROW(id_offre_temp, NEW.duree, NEW.date_evenement, NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_offre_visite
INSTEAD OF INSERT
ON offre_visite
FOR EACH ROW
EXECUTE PROCEDURE create_offre_visite();


-- READ

/* SELECT * FROM offre_visite; */


-- UPDATE

CREATE FUNCTION update_offre_visite() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_offre <> OLD.id_offre) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''une offre.';
    END IF;

    IF (NEW.id_compte_professionnel <> OLD.id_compte_professionnel) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''auteur d''une offre.';
    END IF;

    UPDATE _offre
    SET titre = NEW.titre,
        resume = NEW.resume,
        ville = NEW.ville,
        description_detaille = NEW.description_detaille,
        site_web = NEW.site_web,
        id_adresse = NEW.id_adresse,
        abonnement = NEW.abonnement
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_visite
    SET duree = NEW.duree,
        date_evenement = NEW.date_evenement
    WHERE id_offre = NEW.id_offre;
    
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_offre_visite
INSTEAD OF UPDATE
ON offre_visite
FOR EACH ROW
EXECUTE PROCEDURE update_offre_visite();


-- DELETE

CREATE FUNCTION delete_offre_visite() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _offre_visite
    WHERE id_offre = OLD.id_offre;

    DELETE FROM _offre
    WHERE id_offre = OLD.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_offre_visite
INSTEAD OF DELETE
ON offre_visite
FOR EACH ROW
EXECUTE PROCEDURE delete_offre_visite();


/* ========================== OFFRE SPECTACLE ========================== */

-- CREATE

CREATE FUNCTION create_offre_spectacle() RETURNS TRIGGER AS $$
DECLARE
    id_offre_temp _offre.id_offre%type;
BEGIN
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_spectacle(id_offre, duree, capacite,date_evenement)
        VALUES (id_offre_temp, NEW.duree, NEW.capacite,NEW.date_evenement);
    RETURN ROW(id_offre_temp, NEW.duree, NEW.capacite, NEW.date_evenement, NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_offre_spectacle
INSTEAD OF INSERT
ON offre_spectacle
FOR EACH ROW
EXECUTE PROCEDURE create_offre_spectacle();


-- READ

/* SELECT * FROM offre_spectacle; */


-- UPDATE

CREATE FUNCTION update_offre_spectacle() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_offre <> OLD.id_offre) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''une offre.';
    END IF;

    IF (NEW.id_compte_professionnel <> OLD.id_compte_professionnel) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''auteur d''une offre.';
    END IF;

    UPDATE _offre
    SET titre = NEW.titre,
        resume = NEW.resume,
        ville = NEW.ville,
        description_detaille = NEW.description_detaille,
        site_web = NEW.site_web,
        id_adresse = NEW.id_adresse,
        prix_offre = prix_offre,
        type_offre = type_offre,
        abonnement = NEW.abonnement
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_spectacle
    SET duree = NEW.duree,
        capacite = NEW.capacite,
        date_evenement = NEW.date_evenement
    WHERE id_offre = NEW.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_offre_spectacle
INSTEAD OF UPDATE
ON offre_spectacle
FOR EACH ROW
EXECUTE PROCEDURE update_offre_spectacle();


-- DELETE

CREATE FUNCTION delete_offre_spectacle() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _offre_spectacle
    WHERE id_offre = OLD.id_offre;

    DELETE FROM _offre
    WHERE id_offre = OLD.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_offre_spectacle
INSTEAD OF DELETE
ON offre_spectacle
FOR EACH ROW
EXECUTE PROCEDURE delete_offre_spectacle();


/* ===================== OFFRE PARC D'ATTRACTIONS ====================== */

-- CREATE

CREATE FUNCTION create_offre_parc_attraction() RETURNS TRIGGER AS $$
DECLARE
    id_offre_temp _offre.id_offre%type;
BEGIN
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_parc_attraction(id_offre, nb_attractions, age_min, plan)
        VALUES (id_offre_temp, NEW.nb_attractions, NEW.age_min, NEW.plan);
    RETURN ROW(id_offre_temp, NEW.nb_attractions, NEW.age_min, NEW.plan, NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_offre_parc_attraction
INSTEAD OF INSERT
ON offre_parc_attraction
FOR EACH ROW
EXECUTE PROCEDURE create_offre_parc_attraction();


-- READ

/* SELECT * FROM offre_parc_attraction; */


-- UPDATE

CREATE FUNCTION update_offre_parc_attraction() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_offre <> OLD.id_offre) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''une offre.';
    END IF;

    IF (NEW.id_compte_professionnel <> OLD.id_compte_professionnel) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''auteur d''une offre.';
    END IF;

    UPDATE _offre
    SET titre = NEW.titre,
        resume = NEW.resume,
        ville = NEW.ville,
        description_detaille = NEW.description_detaille,
        site_web = NEW.site_web,
        id_adresse = NEW.id_adresse,
        abonnement = NEW.abonnement
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_parc_attraction
    SET nb_attractions = NEW.nb_attractions,
        age_min = NEW.age_min,
        plan = NEW.plan
    WHERE id_offre = NEW.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_offre_parc_attraction
INSTEAD OF UPDATE
ON offre_parc_attraction
FOR EACH ROW
EXECUTE PROCEDURE update_offre_parc_attraction();


-- DELETE

CREATE FUNCTION delete_offre_parc_attraction() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _offre_parc_attraction
    WHERE id_offre = OLD.id_offre;

    DELETE FROM _offre
    WHERE id_offre = OLD.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_offre_parc_attraction
INSTEAD OF DELETE
ON offre_parc_attraction
FOR EACH ROW
EXECUTE PROCEDURE delete_offre_parc_attraction();


/* ======================== OFFRE RESTAURATION ========================= */

-- CREATE

CREATE FUNCTION create_offre_restauration() RETURNS TRIGGER AS $$
DECLARE
    id_offre_temp _offre.id_offre%type;
BEGIN
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_restauration(id_offre, gamme_prix, carte)
        VALUES (id_offre_temp, NEW.gamme_prix, NEW.carte);
    RETURN ROW(id_offre_temp, NEW.gamme_prix, NEW.carte, NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.abonnement);
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_create_offre_restauration
INSTEAD OF INSERT
ON offre_restauration
FOR EACH ROW
EXECUTE PROCEDURE create_offre_restauration();


-- READ

/* SELECT * FROM offre_restauration; */


-- UPDATE

CREATE FUNCTION update_offre_restauration() RETURNS TRIGGER AS $$
BEGIN
    IF (NEW.id_offre <> OLD.id_offre) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''identifiant d''une offre.';
    END IF;

    IF (NEW.id_compte_professionnel <> OLD.id_compte_professionnel) THEN
        RAISE EXCEPTION 'Vous ne pouvez pas modifier l''auteur d''une offre.';
    END IF;

    UPDATE _offre
    SET titre = NEW.titre,
        resume = NEW.resume,
        ville = NEW.ville,
        description_detaille = NEW.description_detaille,
        site_web = NEW.site_web,
        id_adresse = NEW.id_adresse,
        abonnement = NEW.abonnement
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_restauration
    SET gamme_prix = NEW.gamme_prix,
        carte = NEW.carte
    WHERE id_offre = NEW.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_update_offre_restauration
INSTEAD OF UPDATE
ON offre_restauration
FOR EACH ROW
EXECUTE PROCEDURE update_offre_restauration();


-- DELETE

CREATE FUNCTION delete_offre_restauration() RETURNS TRIGGER AS $$
BEGIN
    DELETE FROM _offre_restauration
    WHERE id_offre = OLD.id_offre;

    DELETE FROM _offre
    WHERE id_offre = OLD.id_offre;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tg_delete_offre_restauration
INSTEAD OF DELETE
ON offre_restauration
FOR EACH ROW
EXECUTE PROCEDURE delete_offre_restauration();



/* ##################################################################### */
/*                         TRIGGERS ASSOCIATIONS                         */
/* ##################################################################### */


CREATE FUNCTION offre_jours_uniques() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM _horaires_du_jour WHERE nom_jour = NEW.nom_jour AND id_offre = NEW.id_offre;
    IF FOUND THEN
        RAISE EXCEPTION 'Il ne peut pas y avoir plusieurs fois le même jour.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER offre_jours_uniques_tg
BEFORE INSERT
ON _horaires_du_jour
FOR EACH ROW
EXECUTE PROCEDURE offre_jours_uniques();


CREATE FUNCTION offre_activite_propose_au_moins_une_prestation() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM _offre_activite_propose_prestation WHERE id_offre_activite = NEW.id_offre;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Une offre activité doit proposer au moins une prestation.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE CONSTRAINT TRIGGER offre_activite_propose_au_moins_une_prestation_tg
AFTER INSERT
ON _offre_activite
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE offre_activite_propose_au_moins_une_prestation();


CREATE FUNCTION offre_contient_au_moins_une_image() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM _offre_contient_image WHERE id_offre = NEW.id_offre;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Une offre doit avoir au moins une image.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE CONSTRAINT TRIGGER offre_contient_au_moins_une_image_tg
AFTER INSERT
ON _offre
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE offre_contient_au_moins_une_image();


CREATE FUNCTION offre_souscrit_une_seule_option() RETURNS TRIGGER AS $$
BEGIN
    PERFORM * FROM _offre_souscrit_option WHERE id_offre = NEW.id_offre;
    IF FOUND THEN
        RAISE EXCEPTION 'Une offre ne peut avoir qu''une seule option';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER offre_souscrit_une_seule_option_tg
BEFORE INSERT
ON _offre_souscrit_option
FOR EACH ROW
EXECUTE PROCEDURE offre_souscrit_une_seule_option();


CREATE FUNCTION avis_sur_offre_restauration_possede_4_notes_detaillees() RETURNS TRIGGER AS $$
DECLARE
    nb INTEGER;
BEGIN
    nb := (SELECT COUNT(*)
            FROM _avis
            INNER JOIN _note_detaillee
            ON _avis.id_avis = _note_detaillee.id_avis
            WHERE _avis.id_avis = NEW.id_avis);
    
    PERFORM *
    FROM _offre_restauration
    WHERE id_offre = NEW.id_offre;

    IF FOUND THEN
        IF nb <> 4 THEN
            RAISE EXCEPTION 'Une offre de restauration doit avoir 4 notes détaillées.';
        END IF;
    ELSE
        IF nb > 0 THEN
            RAISE EXCEPTION 'Seul les offres de restaurations peut avoir des notes détaillées.';
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE CONSTRAINT TRIGGER avis_sur_offre_restauration_possede_4_notes_detaillees_tg
AFTER INSERT ON _avis
DEFERRABLE INITIALLY DEFERRED
FOR EACH ROW
EXECUTE PROCEDURE avis_sur_offre_restauration_possede_4_notes_detaillees();


COMMIT;
