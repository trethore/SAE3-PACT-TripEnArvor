DROP SCHEMA IF EXISTS pact CASCADE;
CREATE SCHEMA pact;
SET SCHEMA 'pact';


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


CREATE TABLE _membre (
    id      INTEGER,
    pseudo  VARCHAR(255),
    CONSTRAINT _compte_membre_pk PRIMARY KEY (id),
    CONSTRAINT _compte_membre_fk_compte FOREIGN KEY (id) REFERENCES _compte.id
);

