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

