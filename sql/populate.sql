SET SCHEMA 'sae';

START TRANSACTION;

INSERT INTO _image (lien_fichier) VALUES
('image1.webp'),
('image2.webp'),
('image3.webp'),
('image4.webp'),
('image5.webp'),
('image6.webp'),
('image7.webp'),
('image8.webp'),
('image9.webp'),
('image10.webp'),
('image11.webp'),
('image12.webp'),
('image13.webp'),
('image14.webp'),
('image15.webp'),
('image16.webp'),
('image17.webp'),
('image18.webp'),
('image19.webp'),
('image20.webp'),
('image21.webp'),
('image22.webp'),
('image23.webp');


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
('Excursion Montagne', 'Une excursion dans les Alpes offre des paysages à couper le souffle : sentiers forestiers, prairies fleuries, et sommets enneigés. Après une montée énergique, le panorama dévoile glaciers, vallées verdoyantes, et lacs bleutés.', 'Grenoble', 'Une excursion dans les Alpes est une expérience captivante, combinant aventure, découverte et communion avec la nature. Elle débute tôt le matin, souvent sous une lumière dorée, propice à des premières foulées énergiques sur des sentiers sinueux. La forêt dense qui entoure les premiers kilomètres dégage une odeur fraîche de pins et de terre humide, tandis que le chant des oiseaux et le murmure des ruisseaux rythment la progression. À mesure que l’on s’élève, les arbres s’éclaircissent pour laisser place à des prairies alpines parsemées de fleurs sauvages : edelweiss, gentianes et autres merveilles colorées. La montée exige un effort constant, mais chaque pas est récompensé par des panoramas toujours plus impressionnants. Une fois au sommet, un spectacle grandiose se dévoile : des glaciers scintillent au loin, des vallées verdoyantes s’étendent à perte de vue, et des lacs alpins reflètent le ciel comme des miroirs bleutés. C’est l’occasion parfaite pour savourer un pique-nique composé de spécialités locales tout en respirant l’air pur. La descente, plus douce, permet de croiser des marmottes curieuses ou d’apercevoir des chamois sur les parois rocheuses.', 'www.rando.com', 1, 2, 100, 'premium', 480, 12),
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
INSERT INTO offre_parc_attraction
(
    titre,
    resume, 
    ville, 
    description_detaille, 
    site_web, 
    id_compte_professionnel, 
    id_adresse, 
    prix_offre, 
    type_offre, 
    nb_attractions, 
    age_min, 
    plan
)
VALUES 
(
    'Parc Astérix', 
    'Découvrez les mondes d''Astérix et Obélix', 
    'Paris', 
    'Une journée entière dans le parc d''attraction Astérix', 
    'www.parcasterix.com', 
    1, 
    1, 
    50, 
    'premium', 
    20, 
    5, 
    'image17.webp'
),
(
    'Disneyland Paris',
    'La magie de Disney à portée de main',
    'Paris',
    'Une journée au parc Disneyland Paris',
    'www.disneylandparis.com',
    2,
    1,
    70,
    'premium',
    30,
    3,
    'image18.webp'
);

-- ####################################################################
-- INSERTION D'OFFRES DE RESTAURATION
-- ####################################################################

-- Insertion dans offre_restauration
INSERT INTO offre_restauration(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, gamme_prix, carte)
VALUES 
('Dîner gastronomique', 'Découvrez la cuisine étoilée de Paris', 'Paris', 'Un dîner dans un restaurant étoilé à Paris', 'www.gastroparis.com', 1, 1, 150, 'standard', '€€€', 'image19.webp'),
('Restaurant traditionnel', 'Cuisine locale et authentique', 'Lyon', 'Un repas dans un restaurant typique de Lyon', 'www.cuisinelyon.com', 2, 2, 60, 'standard', '€€', 'image20.webp');

INSERT INTO _tag (nom_tag) 
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
(1,'Montagne'),
(2,'Bien-être'),
(3,'Découverte'),
(4,'Groupe'),
(5,'Festif'),
(6,'Insolite'),
(7,'Aventure extrême'),
(8,'Découverte'),
(9,'Gastronomie'),
(10,'Tradition');

INSERT INTO _offre_contient_image (id_offre, id_image) VALUES
(1, 'image1.webp'),
(1, 'image2.webp'),
(2, 'image3.webp'),
(3, 'image4.webp'),
(4, 'image5.webp'),
(6, 'image6.webp'),
(7, 'image7.webp'),
(8, 'image8.webp'),
(9, 'image9.webp'),
(10, 'image10.webp'),
(1, 'image11.webp'),
(2, 'image12.webp'),
(3, 'image13.webp'),
(4, 'image14.webp'),
(5, 'image15.webp'),
(6, 'image16.webp');



COMMIT;

