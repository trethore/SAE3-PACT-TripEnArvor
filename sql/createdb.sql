START TRANSACTION;

DROP SCHEMA IF EXISTS pact CASCADE;
CREATE SCHEMA pact;
SET SCHEMA 'pact';

COMMIT;

-- ROLLBACK;


START TRANSACTION;



/* ##################################################################### */
/*                                 TYPES                                 */
/* ##################################################################### */


CREATE TYPE gamme_prix_t AS ENUM ('€', '€€', '€€€');
CREATE TYPE type_repas_t AS ENUM ('Petit-déjeuner', 'Brunch', 'Déjeuner', 'Dîner', 'Boissons');
CREATE TYPE jour_t AS ENUM ('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
CREATE TYPE type_offre_t AS ENUM('gratuite', 'standard', 'premium');



/* ##################################################################### */
/*                                COMPTES                                */
/* ##################################################################### */


/* ========================== COMPTE ABSTRAIT ========================== */

CREATE TABLE _compte (
    id_compte       SERIAL,
    nom_compte      VARCHAR(30),
    prenom          VARCHAR(30),
    email           VARCHAR(320) NOT NULL,
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
    pseudo      VARCHAR(255) NOT NULL,
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
    prix_offre              INTEGER NOT NULL,
    type_offre              type_offre_t NOT NULL,
    CONSTRAINT _offre_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_fk_compte_professionnel FOREIGN KEY (id_compte_professionnel) REFERENCES _compte_professionnel(id_compte)
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
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    CONSTRAINT _offre_visite_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_visite_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);

CREATE VIEW offre_visite AS
    SELECT *
    FROM _offre_visite
    NATURAL JOIN _offre
;


/* ===================== OFFRE SPECTACLE CONCRETE ====================== */

CREATE TABLE _offre_spectacle (
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    capacite    INTEGER NOT NULL,
    CONSTRAINT _offre_spectacle_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_spectacle_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
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
    CONSTRAINT _offre_restauration_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_restauration_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);

CREATE VIEW offre_restauration AS
    SELECT *
    FROM _offre_restauration
    NATURAL JOIN _offre
;



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
/*                              ASSOCIATIONS                             */
/* ##################################################################### */


/* ======================= COMPTE HABITE ADRESSE ======================= */

ALTER TABLE _compte
    ADD CONSTRAINT _compte_fk_adresse 
    FOREIGN KEY (id_adresse) REFERENCES _adresse(id_adresse)
;


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


/* ================ OFFRE PARC ATTRACTION POSSÈDE PLAN ================= */

CREATE TABLE _offre_parc_attraction_possede_plan (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_parc_attraction_possede_plan_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_parc_attraction_possede_plan_fk_offre_parc_attraction FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_parc_attraction_possede_plan_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
);


/* ================= OFFRE RESTAURATION POSSÈDE CARTE ================== */

CREATE TABLE _offre_restauration_possede_carte (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_restauration_possede_carte_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_restauration_possede_carte_fk_offre_restauration FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_restauration_possede_carte_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
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
    CONSTRAINT _offre_possede_tag_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_possede_tag_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
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
    RETURN NEW;
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
    RETURN NEW;
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
    RETURN NEW;
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
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.prix_offre, NEW.type_offre)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_activite(id_offre, duree, age_min)
        VALUES (id_offre_temp, NEW.duree, NEW.age_min);
    RETURN NEW;
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
        prix_offre = NEW.prix_offre,
        type_offre = NEW.type_offre
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
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.prix_offre, NEW.type_offre)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_visite(id_offre, duree)
        VALUES (id_offre_temp, NEW.duree);
    RETURN NEW;
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
        prix_offre = NEW.prix_offre,
        type_offre = NEW.type_offre
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_visite
    SET duree = NEW.duree
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
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.prix_offre, NEW.type_offre)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_spectacle(id_offre, duree, capacite)
        VALUES (id_offre_temp, NEW.duree, NEW.capacite);
    RETURN NEW;
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
        type_offre = type_offre
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_spectacle
    SET duree = NEW.duree,
        capacite = NEW.capacite
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
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.prix_offre, NEW.type_offre)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_parc_attraction(id_offre, nb_attractions, age_min)
        VALUES (id_offre_temp, NEW.nb_attractions, NEW.age_min);
    RETURN NEW;
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
        prix_offre = NEW.prix_offre,
        type_offre = NEW.type_offre
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_parc_attraction
    SET nb_attractions = NEW.nb_attractions,
        age_min = NEW.age_min
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
    INSERT INTO _offre(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre)
        VALUES (NEW.titre, NEW.resume, NEW.ville, NEW.description_detaille, NEW.site_web, NEW.id_compte_professionnel, NEW.id_adresse, NEW.prix_offre, NEW.type_offre)
        RETURNING id_offre INTO id_offre_temp;
    INSERT INTO _offre_restauration(id_offre, gamme_prix)
        VALUES (id_offre_temp, NEW.gamme_prix);
    RETURN NEW;
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
        prix_offre = NEW.prix_offre,
        type_offre = NEW.type_offre
    WHERE id_offre = NEW.id_offre;

    UPDATE _offre_restauration
    SET gamme_prix = NEW.gamme_prix
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


CREATE FUNCTION offre_pas_plus_de_7_jours() RETURNS TRIGGER AS $$
DECLARE
    nb_jours INTEGER;
BEGIN
    nb_jours = (SELECT COUNT(*) FROM _horaires_du_jour WHERE id_offre = NEW.id_offre);
    IF (nb_jours > 7) THEN
        RAISE EXCEPTION 'Il ne peut pas y avoir plus de 7 jours.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER offre_pas_plus_de_7_jours_tg
AFTER INSERT
ON _horaires_du_jour
FOR EACH ROW
EXECUTE PROCEDURE offre_pas_plus_de_7_jours();


COMMIT;

