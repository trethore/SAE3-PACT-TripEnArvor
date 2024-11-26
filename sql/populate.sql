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
('Dégustation', 'Dégustations de fromages de spécialité du pays.'),
('Découverte historique', 'Écouté des histoires surprenante du guide.'),
('Poterie', 'Créez vos propres poteries.');

-- Insertion dans offre_activite
INSERT INTO offre_activite(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, duree, age_min)
VALUES 
('Excursion Montagne', 'Excursion en montagne dans les Alpes, une aventure inoubliable alliant randonnée, paysages époustouflants et immersion dans la nature.', 'Grenoble', 'Venez vivre une aventure inoubliable lors d’une excursion en montagne dans les Alpes. Cette randonnée vous plonge au cœur d’un environnement naturel exceptionnel, où vous découvrirez des panoramas à couper le souffle : cimes enneigées, vallées profondes, lacs cristallins et forêts verdoyantes. Accompagné par des guides expérimentés, vous emprunterez des sentiers accessibles à tous les niveaux, que ce soit pour une balade tranquille ou un défi plus sportif. L’air pur et l''immersion dans la nature vous offriront une véritable sensation de bien-être. Vous aurez l’opportunité d’observer la faune locale, d’admirer des fleurs alpines rares et de comprendre les spécificités de cet écosystème unique. La randonnée sera également ponctuée de pauses conviviales où vous pourrez déguster des spécialités locales dans des refuges typiques. Une expérience authentique qui allie sport, nature, et découvertes culturelles. Un moment idéal pour se ressourcer et se reconnecter avec la montagne.', 'www.rando.com', 1, 2, 100, 'premium', 480, 12),
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
('Visite du Louvre', 'Le Louvre, musée iconique de Paris, propose une collection exceptionnelle d''œuvres d''art allant de l''Antiquité à l''art moderne.', 'Paris', 'Le Louvre, l''un des musées les plus célèbres du monde, est un véritable trésor d''art et d''histoire. Situé au cœur de Paris, ce musée abrite des milliers d''œuvres allant de l''Antiquité à la période moderne, avec des collections exceptionnelles d''art européen, oriental, islamique et égyptien. Parmi les œuvres incontournables, on trouve la célèbre "Mona Lisa" de Léonard de Vinci, "La Victoire de Samothrace" et "La Vénus de Milo". Le musée est réparti en plusieurs ailes, chacune dédiée à des civilisations et époques spécifiques. La pyramide du Louvre, conçue par l''architecte Ieoh Ming Pei, est un symbole emblématique de l''entrée principale du musée et un incontournable pour les visiteurs. En plus de ses collections permanentes, le Louvre organise régulièrement des expositions temporaires et des événements culturels, offrant ainsi une expérience dynamique et enrichissante. Une visite du Louvre est un voyage à travers le temps et les cultures, idéal pour les passionnés d''art, d''histoire et de patrimoine.', 'www.louvre.com', 1, 1, 20, 'standard', 150),
('Tour de Bordeaux', 'Le tour de Bordeaux à vélo permet de découvrir la ville et ses environs à travers un itinéraire pittoresque, entre patrimoine historique, quais animés et vignobles.', 'Bordeaux', 'Le tour de Bordeaux à vélo offre une expérience unique pour explorer la ville et ses environs d’une manière active et agréable. Commencez par le centre historique de Bordeaux, classé au patrimoine mondial de l’UNESCO, où vous pourrez admirer ses magnifiques bâtiments, tels que la Place de la Bourse, la Grosse Cloche et la Cathédrale Saint-André. Les pistes cyclables vous guideront le long des quais de la Garonne, avec des vues splendides sur le fleuve et ses ponts emblématiques. Au-delà de la ville, le tour vous emmène dans les vignobles de la région bordelaise, célèbre pour ses crus prestigieux. Vous pourrez pédaler à travers les paysages viticoles, découvrir les châteaux et même visiter quelques caves pour une dégustation de vin. Le parcours inclut aussi des arrêts dans des parcs et des jardins verdoyants, parfaits pour une pause en plein air.', 'www.bordeaux-tour.com', 2, 3, 15, 'gratuite', 300);

-- ####################################################################
-- INSERTION D'OFFRES DE SPECTACLES
-- ####################################################################visite

-- Insertion dans offre_spectacle
INSERT INTO offre_spectacle(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, prix_offre, type_offre, duree, capacite)
VALUES 
('Concert de Jazz', 'Un concert de jazz qui vous plonge dans un univers musical unique, où l’improvisation et la créativité des musiciens créent une expérience vivante et captivante.', 'Paris', 'Un concert de jazz est une expérience musicale où l’improvisation et la créativité occupent une place centrale. Dans un cadre intimiste ou une grande salle, l’atmosphère est toujours unique, propice à la découverte et à l’évasion. Les musiciens, souvent virtuoses, jouent des standards du jazz tout en apportant leur touche personnelle, créant ainsi une alchimie avec le public. Les instruments emblématiques du genre, tels que le saxophone, la trompette, le piano et la contrebasse, se mélangent pour produire des rythmes dynamiques et des harmonies captivantes. Ce type de concert permet à chaque musicien d’exprimer sa personnalité et ses émotions à travers l’improvisation, offrant une expérience vivante et imprévisible. Chaque performance est un événement unique, un véritable voyage musical où les moments d’interaction entre les musiciens et le public renforcent l’intensité de l’expérience.', 'www.jazzparis.com', 1, 1, 40, 'standard', 60, 100),
('Théâtre de Rue', 'Le théâtre de rue offre des spectacles vivants en extérieur, mêlant comédie, poésie et performances interactives.', 'Lyon', 'Le théâtre de rue transforme l’espace public en une scène vivante et accessible, permettant aux spectateurs de tous horizons de découvrir des performances variées. Que ce soit dans les parcs, rues ou places publiques, ces spectacles mêlent souvent comédie, poésie, cirque et musique, avec une grande proximité entre les artistes et le public. Les performances sont interactives, les spectateurs peuvent être invités à participer, créant une atmosphère festive et conviviale. Ce type de théâtre se distingue par son caractère spontané et flexible, avec des mises en scène souvent improvisées en fonction du lieu et des réactions du public. Loin des conventions des salles traditionnelles, le théâtre de rue offre une expérience immersive et éphémère, où chaque représentation devient un moment unique et vivant. C’est une célébration de l’art accessible à tous, qui surprend, divertit et connecte les gens au-delà des frontières habituelles du spectacle.', 'www.theatrelyon.com', 2, 2, 30, 'standard', 90, 50);

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
    'Le Parc Astérix offre aventures gauloises, attractions variées, spectacles drôles et un univers immersif pour tous !', 
    'Paris', 
    'Le Parc Astérix est une destination unique mêlant humour, aventures et sensations fortes dans l’univers des célèbres Gaulois. Situé près de Paris, il propose une vingtaine d''attractions variées, adaptées à tous les âges : montagnes russes à couper le souffle, manèges aquatiques rafraîchissants et expériences immersives pour les plus jeunes. Des spectacles captivants, comme des cascades ou des aventures gauloises, rythment la journée et plongent les visiteurs dans une ambiance festive. Les décors inspirés des bandes dessinées d’Astérix et Obélix offrent un cadre vivant et coloré, parfait pour l’évasion. Avec une offre de restauration thématique et des hôtels confortables, le Parc Astérix garantit une expérience inoubliable, pleine de rires et d’émotions, pour toute la famille.', 
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
    'À Disneyland Paris, plongez dans un univers magique avec des attractions emblématiques, des spectacles féeriques et des rencontres inoubliables !',
    'Paris',
    'Disneyland Paris est un lieu magique composé de deux parcs : Disneyland Park et Walt Disney Studios Park. Le Disneyland Park vous transporte dans cinq royaumes féeriques, avec des attractions emblématiques comme "Pirates of the Caribbean" et "Space Mountain". Les parades, feux d''artifice et spectacles ajoutent à la magie du parc. Le Walt Disney Studios Park offre une immersion dans l''univers du cinéma, avec des attractions à sensations fortes telles que "The Twilight Zone Tower of Terror" et des spectacles interactifs comme "Mickey and the Magician". Les visiteurs peuvent rencontrer leurs héros Disney, vivre des expériences uniques et profiter de nombreuses options de restauration et de shopping. Avec ses hôtels thématiques et une multitude d’animations, Disneyland Paris garantit une expérience inoubliable pour toute la famille !',
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
('Dîner gastronomique', 'Découvrez notre restaurant gastronomique offrant une cuisine raffinée, préparée avec des ingrédients d''exception et haut de gamme.', 'Paris', 'Un restaurant gastronomique incarne l’excellence culinaire, combinant créativité, qualité et raffinement. Chaque plat est conçu comme une œuvre d’art, mettant en valeur des produits locaux et de saison sublimés par des techniques innovantes. Le chef, souvent une figure reconnue, propose une expérience gustative unique, mêlant saveurs audacieuses et harmonieuses. Le cadre, élégant et soigné, crée une atmosphère propice à la détente, tandis que le service, attentif et discret, personnalise chaque moment. Sommeliers experts et équipes formées assurent un accueil haut de gamme. Plus qu’un repas, un passage dans un restaurant gastronomique est une expérience sensorielle complète, où se mêlent art de la table, exploration des saveurs et plaisir des sens, offrant une escapade culinaire inoubliable', 'www.gastroparis.com', 1, 1, 150, 'standard', '€€€', 'image19.webp'),
('Restaurant traditionnel', 'Découvrez notre restaurant traditionnel offrant une cuisine authentique, préparée avec des ingrédients frais et locaux.', 'Lyon', 'Plongez dans l’univers culinaire de notre restaurant traditionnel, où authenticité et saveurs se rencontrent. Chaque plat est soigneusement préparé avec des ingrédients frais et de saison, sélectionnés auprès de producteurs locaux pour garantir une qualité exceptionnelle. Profitez d’une ambiance chaleureuse et conviviale, idéale pour partager un repas en famille, entre amis ou pour célébrer une occasion spéciale. Notre carte variée met à l’honneur des recettes classiques revisitées, des spécialités régionales et des desserts maison savoureux. Laissez-vous séduire par une expérience gastronomique unique, où chaque détail compte.', 'www.cuisinelyon.com', 2, 2, 60, 'standard', '€€', 'image20.webp');

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


INSERT INTO _horaires_du_jour (nom_jour, id_offre) VALUES
('Lundi', 1),    
('Mardi', 2),   
('Mercredi', 3), 
('Jeudi', 1),    
('Vendredi', 2); 

INSERT INTO _horaire (ouverture, fermeture, horaires_du_jour) VALUES
('08:00', '12:00', 1),
('13:00', '17:00', 1), 
('09:00', '13:00', 2), 
('14:00', '18:00', 3), 
('08:00', '12:00', 4), 
('13:00', '17:00', 5); 

INSERT INTO _tarif_publique (nom_tarif, prix, id_offre) VALUES
('Tarif Standard Matin', 15, 1), 
('Tarif Réduit Matin', 10, 1),   
('Tarif Standard Après-midi', 20, 2), 
('Tarif Réduit Après-midi', 15, 2),   
('Tarif Standard Soir', 25, 3), 
('Tarif Réduit Soir', 18, 3);   
COMMIT;

