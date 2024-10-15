DROP SCHEMA IF EXISTS pact CASCADE;
CREATE SCHEMA pact;
SET SCHEMA 'pact';


/* ********************************************************************* */
/*                                Comptes                                */
/* ********************************************************************* */


CREATE TABLE _compte (
    id          SERIAL,
    nom         VARCHAR(30),
    prenom      VARCHAR(30),
    email       VARCHAR(320) NOT NULL,
    tel         VARCHAR(12),
    motDePasse  VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_pk PRIMARY KEY (id)
);


CREATE TABLE _compte_professionnel (
    id              INTEGER,
    denomination    VARCHAR(255) NOT NULL,
    aPropos         VARCHAR(255) NOT NULL,
    siteWeb         VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_fk_compte FOREIGN KEY (id) REFERENCES _compte.id
);


CREATE TABLE _compte_professionnel_prive (
    id      INTEGER,
    siren   VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_professionnel_prive_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_prive_fk_compte_professionnel FOREIGN KEY (id) REFERENCES _compte_professionnel.id
);


CREATE TABLE _compte_professionnel_publique (
    id      INTEGER,
    CONSTRAINT _compte_professionnel_prive_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_prive_fk_compte_professionnel FOREIGN KEY (id) REFERENCES _compte_professionnel.id
);


CREATE TABLE _compte_membre (
    id      INTEGER,
    pseudo  VARCHAR(255) NOT NULL,
    CONSTRAINT _compte_membre_pk PRIMARY KEY (id),
    CONSTRAINT _compte_membre_fk_compte FOREIGN KEY (id) REFERENCES _compte.id
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
    id                  SERIAL,
    titre               VARCHAR(128) NOT NULL,
    resume              VARCHAR(255) NOT NULL,
    ville               VARCHAR(255) NOT NULL,
    descriptionDetaille VARCHAR(1024),
    siteWeb             VARCHAR(255),
    CONSTRAINT _offre_pk PRIMARY KEY (id),
);


CREATE TABLE _offre_activite (
    id      INTEGER,
    duree   INTEGER NOT NULL,
    ageMin  INTEGER NOT NULL,
    CONSTRAINT _offre_activite_pk PRIMARY KEY (id),
    CONSTRAINT _offre_activite_fk_offre FOREIGN KEY (id) REFERENCES (_offre.id)
);


CREATE TABLE _offre_visite (
    id      INTEGER,
    duree   INTEGER NOT NULL,
    CONSTRAINT _offre_visite_pk PRIMARY KEY (id),
    CONSTRAINT _offre_visite_fk_offre FOREIGN KEY (id) REFERENCES (_offre.id)
);


/* ********************************************************************* */
/*                              Utilitaires                              */
/* ********************************************************************* */


CREATE TABLE _adresse (
    numEtNomDeVoie      VARCHAR(255),
    complementAdresse   VARCHAR(255),
    codePostal          VARCHAR(6),
    ville               VARCHAR(255),
    pays                VARCHAR(255),
    CONSTRAINT _adresse_pk PRIMARY KEY (numEtNomDeVoie, complementAdresse, codePostal, ville, pays)
);


CREATE TABLE _prestation (
    nom         VARCHAR(128),
    description VARCHAR(1024) NOT NULL,
    CONSTRAINT _prestation_pk PRIMARY KEY (nom)
);


CREATE TABLE _langue (
    nom VARCHAR(128)
    CONSTRAINT _langue_pk PRIMARY KEY (nom)
);

