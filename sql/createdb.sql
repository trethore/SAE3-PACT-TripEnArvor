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
    email       VARCHAR(320),
    tel         VARCHAR(12),
    motDePasse  VARCHAR(255),
    CONSTRAINT _compte_pk PRIMARY KEY (id)
);


CREATE TABLE _compte_professionnel (
    id              INTEGER,
    denomination    VARCHAR(255),
    aPropos         VARCHAR(255),
    siteWeb         VARCHAR(255),
    CONSTRAINT _compte_professionnel_pk PRIMARY KEY (id),
    CONSTRAINT _compte_professionnel_fk_compte FOREIGN KEY (id) REFERENCES _compte.id
);


CREATE TABLE _compte_professionnel_prive (
    id      INTEGER,
    siren   VARCHAR(255),
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
    pseudo  VARCHAR(255),
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
/*                              Utilitaires                              */
/* ********************************************************************* */


CREATE TABLE _address (
    numEtNomDeVoie      VARCHAR(255),
    complementAdresse   VARCHAR(255),
    codePostal          VARCHAR(6),
    ville               VARCHAR(255),
    pays                VARCHAR(255),
    CONSTRAINT _address_pk PRIMARY KEY (numEtNomDeVoie, complementAdresse, codePostal, ville, pays)
);

