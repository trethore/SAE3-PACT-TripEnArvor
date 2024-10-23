ROLLBACK;

SET SCHEMA 'pact';

START TRANSACTION;

-- ####################################################################
-- INSERTION D'ADRESSES
-- ####################################################################

-- Insertion d'adresses pour les comptes
INSERT INTO _adresse(num_et_nom_de_voie, ville, code_postal, pays)
VALUES 
('12 Rue de Paris', 'Paris', '75000', 'France'),
('45 Boulevard de Lyon', 'Lyon', '69000', 'France'),
('8 Avenue de Bordeaux', 'Bordeaux', '33000', 'France'),
('27 Rue de Lille', 'Lille', '59000', 'France');

-- ####################################################################
-- INSERTION DE COMPTES PROFESSIONNELS PUBLIQUES
-- ####################################################################

-- Insertion dans compte_professionnel_publique
INSERT INTO compte_professionnel_publique(nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web)
VALUES 
('Dupont', 'Jean', 'jean.dupont@example.com', '0601020304', 'password123', 1, 'Entreprise Dupont', 'Spécialiste des services IT', 'www.dupont.com'),
('Martin', 'Claire', 'claire.martin@example.com', '0605060708', 'password456', 2, 'Consulting Martin', 'Expert en stratégie d''entreprise', 'www.martin-consulting.com');

-- ####################################################################
-- INSERTION DE COMPTES MEMBRES
-- ####################################################################

-- Insertion dans compte_membre
INSERT INTO compte_membre(nom_compte, prenom, email, tel, mot_de_passe, id_adresse, pseudo)
VALUES 
('Leclerc', 'Antoine', 'antoine.leclerc@example.com', '0708091011', 'password789', 3, 'AntoineL'),
('Durand', 'Sophie', 'sophie.durand@example.com', '0611223344', 'password321', 4, 'SophieD');

-- ####################################################################
-- INSERTION D'OFFRES D'ACTIVITÉS
-- ####################################################################

INSERT INTO _prestation (nom_prestation, description) VALUES
('Dégustation', 'Dégustations de fromages de spécialité su pays.'),
('Découverte historique', 'Écouté des histoires surprenante du guide.'),
('Poterie', 'Créez vos propres poteries.');

-- Insertion dans offre_activite
INSERT INTO offre_activite(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, duree, age_min)
VALUES 
('Excursion Montagne', 'Une aventure en montagne', 'Grenoble', 'Une randonnée d''une journée dans les Alpes', 'www.rando.com', 1, 2, 100, 'premium', 480, 12),
('Atelier Créatif', 'Créez votre propre poterie', 'Paris', 'Un atelier de poterie guidé par des experts', 'www.poterie.com', 2, 1, 50, 'standard',180 , 8);

INSERT INTO _offre_activite_propose_prestation (nom_prestation, id_offre_activite) VALUES
('Dégustation', 1),
('Découverte historique', 2),
('Poterie', 2);


-- ####################################################################
-- INSERTION D'OFFRES DE VISITE
-- ####################################################################

-- Insertion dans offre_visite
INSERT INTO offre_visite(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, duree)
VALUES 
('Visite du Louvre', 'Explorez le musée du Louvre', 'Paris', 'Une visite guidée des œuvres emblématiques du Louvre', 'www.louvre.com', 1, 1, 20, 'standard', 150),
('Tour de Bordeaux', 'Découvrez Bordeaux en vélo', 'Bordeaux', 'Une visite guidée de la ville de Bordeaux en vélo', 'www.bordeaux-tour.com', 2, 3, 15, 'gratuite', 300);

-- ####################################################################
-- INSERTION D'OFFRES DE SPECTACLES
-- ####################################################################visite

-- Insertion dans offre_spectacle
INSERT INTO offre_spectacle(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, duree, capacite)
VALUES 
('Concert de Jazz', 'Une soirée de jazz à Paris', 'Paris', 'Un concert de jazz avec des musiciens renommés', 'www.jazzparis.com', 1, 1, 40, 'standard', 60, 100),
('Théâtre de Rue', 'Spectacle de rue interactif', 'Lyon', 'Une performance théâtrale dans les rues de Lyon', 'www.theatrelyon.com', 2, 2, 30, 'standard', 90, 50);

-- ####################################################################
-- INSERTION D'OFFRES DE PARCS D'ATTRACTION
-- ####################################################################

-- Insertion dans offre_parc_attraction
INSERT INTO offre_parc_attraction(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, nb_attractions, age_min)
VALUES 
('Parc Astérix', 'Découvrez les mondes d''Astérix et Obélix', 'Paris', 'Une journée entière dans le parc d''attraction Astérix', 'www.parcasterix.com', 1, 1, 50, 'premium', 20, 5),
('Disneyland Paris', 'La magie de Disney à portée de main', 'Paris', 'Une journée au parc Disneyland Paris', 'www.disneylandparis.com', 2, 1, 70, 'premium', 30, 3);

-- ####################################################################
-- INSERTION D'OFFRES DE RESTAURATION
-- ####################################################################

-- Insertion dans offre_restauration
INSERT INTO offre_restauration(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, gamme_prix)
VALUES 
('Dîner gastronomique', 'Découvrez la cuisine étoilée de Paris', 'Paris', 'Un dîner dans un restaurant étoilé à Paris', 'www.gastroparis.com', 1, 1, 150, 'standard', '€€€'),
('Restaurant traditionnel', 'Cuisine locale et authentique', 'Lyon', 'Un repas dans un restaurant typique de Lyon', 'www.cuisinelyon.com', 2, 2, 60, 'standard', '€€');

INSERT INTO tags (nom_tag) 
VALUES 
('Sport'),
('Gastronomie'),
('Bien-être'),
('Aventure extrême'),
('Histoire'),
('Romantique'),
('Relaxation'),
('Plage'),
('Montagne'),
('Festif'),
('Nocturne'),
('Découverte'),
('Artisanat'),
('Tradition'),
('Technologie'),
('Innovation'),
('Eco-responsable'),
('Insolite'),
('Groupe'),
('Solo');

INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
(1,'Sport'),
(2,'Bien-être'),
(3,'Découverte'),
(4,'Groupe'),
(5,'Festif'),
(6,'Insolite'),
(7,'Aventure extrême'),
(8,'Découverte'),
(9,'Gastronomie'),
(10,'Tradition');


COMMIT;

