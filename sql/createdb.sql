START TRANSACTION;

DROP SCHEMA IF EXISTS pact CASCADE;
CREATE SCHEMA pact;
SET SCHEMA 'pact';

COMMIT;


START TRANSACTION;


/* ********************************************************************* */
/*                                 Types                                 */
/* ********************************************************************* */


CREATE TYPE gamme_prix_t AS ENUM ('€', '€€', '€€€');
CREATE TYPE type_repas_t AS ENUM ('Petit-déjeuner', 'Brunch', 'Déjeuner', 'Dîner', 'Boissons');
CREATE TYPE jour_t AS ENUM ('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');


/* ********************************************************************* */
/*                                Comptes                                */
/* ********************************************************************* */


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


CREATE TABLE _compte_professionnel (
    id_compte       INTEGER,
    denomination    VARCHAR(255) NOT NULL,
    a_propos         VARCHAR(255) NOT NULL,
    site_web         VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_fk_compte FOREIGN KEY (id_compte) REFERENCES _compte(id_compte)
);


CREATE TABLE _compte_professionnel_prive (
    id_compte   INTEGER,
    siren       VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_prive_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_prive_fk_compte_professionnel FOREIGN KEY (id_compte) REFERENCES _compte_professionnel(id_compte)
);


CREATE TABLE _compte_professionnel_publique (
    id_compte   INTEGER,
    CONSTRAINT _compte_professionnel_publique_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_professionnel_publique_fk_compte_professionnel FOREIGN KEY (id_compte) REFERENCES _compte_professionnel(id_compte)
);


CREATE TABLE _compte_membre (
    id_compte   INTEGER,
    pseudo      VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_membre_pk PRIMARY KEY (id_compte),
    CONSTRAINT _compte_membre_fk_compte FOREIGN KEY (id_compte) REFERENCES _compte(id_compte)
);


CREATE VIEW compte_professionnel_prive AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_professionnel
    NATURAL JOIN _compte_professionnel_prive;


CREATE VIEW compte_professionnel_publique AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_professionnel
    NATURAL JOIN _compte_professionnel_publique;


CREATE VIEW compte_membre AS
    SELECT * 
    FROM _compte
    NATURAL JOIN _compte_membre;


/* ********************************************************************* */
/*                                Offres                                 */
/* ********************************************************************* */


CREATE TABLE _offre (
    id_offre                SERIAL,
    titre                   VARCHAR(128) NOT NULL,
    resume                  VARCHAR(255) NOT NULL,
    ville                   VARCHAR(255) NOT NULL,
    description_detaille    VARCHAR(1024),
    site_web                VARCHAR(255),
    id_compte_professionnel INTEGER,
    id_adresse              INTEGER,
    CONSTRAINT _offre_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_fk_compte_professionnel FOREIGN KEY (id_compte_professionnel) REFERENCES _compte_professionnel(id_compte)
);


CREATE TABLE _offre_activite (
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    age_min     INTEGER NOT NULL,
    CONSTRAINT _offre_activite_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_activite_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _offre_visite (
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    CONSTRAINT _offre_visite_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_visite_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _offre_spectacle (
    id_offre    INTEGER,
    duree       INTEGER NOT NULL,
    capacite    INTEGER NOT NULL,
    CONSTRAINT _offre_spectacle_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_spectacle_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _offre_parc_attraction (
    id_offre         INTEGER,
    nb_attractions   INTEGER NOT NULL,
    age_min          INTEGER NOT NULL,
    CONSTRAINT _offre_parc_attraction_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_parc_attraction_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


CREATE TABLE _offre_restauration (
    id_offre    INTEGER,
    gamme_prix  gamme_prix_t NOT NULL,
    CONSTRAINT _offre_restauration_pk PRIMARY KEY (id_offre),
    CONSTRAINT _offre_restauration_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
);


/* ********************************************************************* */
/*                              Utilitaires                              */
/* ********************************************************************* */


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


CREATE TABLE _tarif (
    id_tarif    SERIAL,
    prix        INTEGER NOT NULL,
    id_offre    INTEGER NOT NULL,
    CONSTRAINT _tarif_pk PRIMARY KEY (id_tarif),
    CONSTRAINT _tarif_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre)
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


/* ********************************************************************* */
/*                              Associations                             */
/* ********************************************************************* */


ALTER TABLE _compte
    ADD CONSTRAINT _compte_fk_adresse 
    FOREIGN KEY (id_adresse) REFERENCES _adresse(id_adresse)
;


ALTER TABLE _offre
    ADD CONSTRAINT _offre_fk_adresse 
    FOREIGN KEY (id_adresse) REFERENCES _adresse(id_adresse)
;


CREATE TABLE _offre_contient_image (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_contient_image_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_contient_image_fk_offre FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_contient_image_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
);


CREATE TABLE _offre_parc_attraction_contient_image (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_parc_attraction_contient_image_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_parc_attraction_contient_image_fk_offre_parc_attraction FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_parc_attraction_contient_image_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
);


CREATE TABLE _offre_restauration_contient_image (
    id_offre    INTEGER,
    id_image    VARCHAR(255),
    CONSTRAINT _offre_restauration_contient_image_pk PRIMARY KEY (id_offre, id_image),
    CONSTRAINT _offre_restauration_contient_image_fk_offre_restauration FOREIGN KEY (id_offre) REFERENCES _offre(id_offre),
    CONSTRAINT _offre_restauration_contient_image_fk_image FOREIGN KEY (id_image) REFERENCES _image(lien_fichier)
);


CREATE TABLE _offre_restauration_propose_repas (
    id_offre_restauration   INTEGER,
    type_repas              type_repas_t,
    CONSTRAINT _offre_restauration_propose_repas_pk PRIMARY KEY (id_offre_restauration, type_repas),
    CONSTRAINT _offre_restauration_propose_repas_fk_offre_restauration FOREIGN KEY (id_offre_restauration) REFERENCES _offre_restauration(id_offre),
    CONSTRAINT _offre_restauration_propose_repas_fk_type_repas FOREIGN KEY (type_repas) REFERENCES _type_repas(type_repas)
);


COMMIT;

