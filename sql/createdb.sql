DROP SCHEMA IF EXISTS pact CASCADE;
CREATE SCHEMA pact;
SET SCHEMA 'pact';


START TRANSACTION;


/* ********************************************************************* */
/*                                 Types                                 */
/* ********************************************************************* */


CREATE TYPE gammePrix_t AS ENUM ('€', '€€', '€€€');
CREATE TYPE typeRepas_t AS ENUM ('Petit-déjeuner', 'Brunch', 'Déjeuner', 'Dîner', 'Boissons');
CREATE TYPE jour_t AS ENUM ('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');


/* ********************************************************************* */
/*                                Comptes                                */
/* ********************************************************************* */


CREATE TABLE _compte (
    id              SERIAL,
    nom             VARCHAR(30),
    prenom          VARCHAR(30),
    email           VARCHAR(320) NOT NULL,
    tel             VARCHAR(12),
    mot_de_passe    VARCHAR(255) NOT NULL,
    adresse         INTEGER,
    CONSTRAINT _compte_pk PRIMARY KEY (id)
);


CREATE TABLE _compte_professionnel (
    id              INTEGER,
    denomination    VARCHAR(255) NOT NULL,
    a_propos         VARCHAR(255) NOT NULL,
    site_web         VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_fk_compte FOREIGN KEY (id) REFERENCES _compte(id)
);


CREATE TABLE _compte_professionnel_prive (
    id      INTEGER,
    siren   VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_prive_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_prive_fk_compte_professionnel FOREIGN KEY (id) REFERENCES _compte_professionnel(id)
);


CREATE TABLE _compte_professionnel_publique (
    id      INTEGER,
    CONSTRAINT _compte_professionnel_publique_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_publique_fk_compte_professionnel FOREIGN KEY (id) REFERENCES _compte_professionnel(id)
);


CREATE TABLE _compte_membre (
    id      INTEGER,
    pseudo  VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_membre_pk PRIMARY KEY (id),
    CONSTRAINT _compte_membre_fk_compte FOREIGN KEY (id) REFERENCES _compte(id)
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
    id                      SERIAL,
    titre                   VARCHAR(128) NOT NULL,
    resume                  VARCHAR(255) NOT NULL,
    ville                   VARCHAR(255) NOT NULL,
    description_detaille    VARCHAR(1024),
    siteWeb                 VARCHAR(255),
    CONSTRAINT _offre_pk PRIMARY KEY (id)
);


CREATE TABLE _offre_activite (
    id      INTEGER,
    duree   INTEGER NOT NULL,
    age_min  INTEGER NOT NULL,
    CONSTRAINT _offre_activite_pk PRIMARY KEY (id),
    CONSTRAINT _offre_activite_fk_offre FOREIGN KEY (id) REFERENCES _offre(id)
);


CREATE TABLE _offre_visite (
    id      INTEGER,
    duree   INTEGER NOT NULL,
    CONSTRAINT _offre_visite_pk PRIMARY KEY (id),
    CONSTRAINT _offre_visite_fk_offre FOREIGN KEY (id) REFERENCES _offre(id)
);


CREATE TABLE _offre_spectacle (
    id          INTEGER,
    duree       INTEGER NOT NULL,
    capacite    INTEGER NOT NULL,
    CONSTRAINT _offre_spectacle_pk PRIMARY KEY (id),
    CONSTRAINT _offre_spectacle_fk_offre FOREIGN KEY (id) REFERENCES _offre(id)
);


CREATE TABLE _offre_parc_attraction (
    id              INTEGER,
    nb_attractions   INTEGER NOT NULL,
    age_min          INTEGER NOT NULL,
    CONSTRAINT _offre_parc_attraction_pk PRIMARY KEY (id),
    CONSTRAINT _offre_parc_attraction_fk_offre FOREIGN KEY (id) REFERENCES _offre(id)
);


CREATE TABLE _offre_restauration (
    id          INTEGER,
    gamme_prix  gammePrix_t NOT NULL,
    CONSTRAINT _offre_restauration_pk PRIMARY KEY (id),
    CONSTRAINT _offre_restauration_fk_offre FOREIGN KEY (id) REFERENCES _offre(id)
);


/* ********************************************************************* */
/*                              Utilitaires                              */
/* ********************************************************************* */


CREATE TABLE _adresse (
    id                  SERIAL,
    num_et_nom_de_voie  VARCHAR(255) NOT NULL,
    complement_adresse  VARCHAR(255),
    code_postal         VARCHAR(6) NOT NULL,
    ville               VARCHAR(255) NOT NULL,
    pays                VARCHAR(255) NOT NULL,
    CONSTRAINT _adresse_pk PRIMARY KEY (id)
);


CREATE TABLE _prestation (
    nom         VARCHAR(128),
    description VARCHAR(1024) NOT NULL,
    CONSTRAINT _prestation_pk PRIMARY KEY (nom)
);


CREATE TABLE _langue (
    nom VARCHAR(128),
    CONSTRAINT _langue_pk PRIMARY KEY (nom)
);


CREATE TABLE _type_repas (
    type_repas  typeRepas_t NOT NULL
);

CREATE TABLE _image (
    lien_fichier    VARCHAR(255),
    CONSTRAINT _image_pk PRIMARY KEY (lienFichier)
);


CREATE TABLE _tarif (
    prix    INTEGER NOT NULL
);


CREATE TABLE _horaires_du_jour (
    id          SERIAL,
    nom_jour    jour_t NOT NULL,
    offre       INTEGER NOT NULL,
    CONSTRAINT _horaires_du_jour_pk PRIMARY KEY (id),
    CONSTRAINT _horaires_du_jour_fk_offre FOREIGN KEY (offre) REFERENCES _offre(id)
);


CREATE TABLE _horaire (
    id                  SERIAL,
    ouverture           CHAR(5),
    fermeture           CHAR(5),
    horaires_du_jour    INTEGER,
    CONSTRAINT _horaire_pk PRIMARY KEY (id),
    CONSTRAINT _horaire_fk_horaires_du_jour FOREIGN KEY (horairesDuJour) REFERENCES _horaires_du_jour(id)
);


CREATE TABLE _tag (
    nomTag  VARCHAR(64),
    CONSTRAINT _tag_pk PRIMARY KEY (nomTag)
);


/* ********************************************************************* */
/*                              Associations                             */
/* ********************************************************************* */


ALTER TABLE _compte
    ADD CONSTRAINT _compte_fk_adresse 
    FOREIGN KEY (adresse) REFERENCES _adresse(id)
;


COMMIT;

