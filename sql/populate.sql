ROLLBACK;
SET SCHEMA 'sae';

START TRANSACTION;

INSERT INTO sae._prix (prix_ht) VALUES
(0), (167), (334), (1668), (834);

INSERT INTO sae._abonnement (nom, id_prix) VALUES
('gratuit', 1),
('standard', 2),
('premium', 3);

INSERT INTO sae._date(date)
VALUES 
('2024-11-27 11:00:00'), --1
('2024-11-27 17:00:00'), 
('2024-11-27 20:00:00'), 
('2024-11-27 21:00:00'),
('2024-11-28 11:00:00'), -- 5
('2024-11-28 17:00:00'), 
('2024-11-28 20:00:00'), 
('2024-11-28 21:00:00'),
('2024-11-29 17:00:00'), 
('2024-11-29 21:00:00'), -- 10
('2024-10-29 19:00:00'), 
('2024-10-14 22:00:01'),
('2024-10-29 22:00:00'), 
('2024-10-29 21:00:00'), 
('2024-10-14 22:14:01'), -- 15
('2024-10-29 22:02:03'),
('2024-11-13 22:14:01'), 
('2024-11-30 22:02:03'), 
('2024-11-28 09:00:00'),
('2024-11-28 13:00:00'), -- 20
('2024-11-28 16:30:00'), 
('2024-11-29 10:00:00'), 
('2024-11-29 15:00:00'), 
('2024-11-29 18:00:00');
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
('15 Rue de la Corniche', 'Brest', '29200', 'France'),
('32 Quai Saint-Malo', 'Saint-Malo', '35400', 'France'),
('7 Place de l’Église', 'Quimper', '29000', 'France'),
('10 Avenue de l’Océan', 'Lorient', '56100', 'France'),
('25 Rue du Château', 'Vannes', '56000', 'France');


-- ####################################################################
-- INSERTION DE COMPTES PROFESSIONNELS PUBLIQUES
-- ####################################################################

-- Insertion dans compte_professionnel_publique
INSERT INTO compte_professionnel_publique(nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web)
VALUES 
('Dupont', 'Jean', 'jean.dupont@example.com', '0601020304', 'password123', 1, 'Entreprise Dupont', 'Spécialiste des services IT', 'https://www.dupont.com'),
('Martin', 'Claire', 'claire.martin@example.com', '0605060708', 'password456', 2, 'Consulting Martin', 'Expert en stratégie d''entreprise', 'https://www.martin-consulting.com');

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

INSERT INTO _prestation (nom_prestation, description) 
VALUES
('Dégustation', 'Découvrez et dégustez des spécialités locales, notamment des crêpes, galettes, et cidres bretons.'),
('Découverte historique', 'Apprenez les histoires et légendes fascinantes racontées par un guide local.'),
('Observation de la faune', 'Approchez la faune locale dans son habitat naturel et découvrez les oiseaux marins.'),
('Tissage', 'Initiez-vous aux techniques traditionnelles du tissage breton.'),
('Atelier de poterie', 'Créez votre propre poterie sous la guidance d’un artisan expérimenté.');


-- Insertion dans offre_activite
 INSERT INTO offre_activite(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement, duree, age_min)
VALUES 
('Balade sur la Côte Sauvage', 'Découvrez les paysages magnifiques de la Côte Sauvage à Quiberon, une expérience mêlant nature, air marin et détente.', 'Quiberon', 'Plongez au cœur de la Bretagne lors d’une balade sur la célèbre Côte Sauvage de Quiberon. Ce circuit pédestre vous offre une immersion dans des paysages maritimes spectaculaires, où falaises abruptes, plages secrètes et eaux turquoise s’entremêlent. Accompagné par un guide passionné, vous découvrirez l’histoire et les légendes locales, tout en observant la flore et la faune spécifiques à ce littoral unique. Cette aventure est ponctuée de pauses pour admirer le panorama, prendre des photos et déguster des produits locaux comme les célèbres crêpes bretonnes ou le cidre artisanal. Une activité idéale pour se ressourcer, tout en découvrant le patrimoine naturel et culturel de la Bretagne.', 'https://www.cotesauvage.bzh', 1, 2, 80, 'premium', 300, 10),
('Atelier de Tissage Breton', 'Initiez-vous à l’art du tissage traditionnel breton.', 'Vannes', 'Participez à un atelier unique où vous apprendrez les bases du tissage breton avec un artisan local. Découvrez les techniques ancestrales et repartez avec votre création.', 'https://www.tissagebzh.com', 2, 3, 40, 'standard', 120, 8);

INSERT INTO _offre_activite_propose_prestation (nom_prestation, id_offre_activite) VALUES
('Dégustation', 1),
('Découverte historique', 1),
('Observation de la faune', 1),

-- Atelier de Tissage Breton
('Tissage', 2),
('Dégustation', 2);


-- ####################################################################
-- INSERTION D'OFFRES DE VISITE
-- ####################################################################

-- Insertion dans offre_visite
INSERT INTO offre_visite(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement, duree,date_evenement)
VALUES 
('Visite de la Ville Close', 'Découvrez la Ville Close de Concarneau, un joyau historique entouré de remparts.', 'Concarneau', 'La Ville Close, située au cœur de Concarneau, est une cité médiévale fortifiée entourée par les eaux. Ce site emblématique de Bretagne offre une plongée dans l’histoire avec ses remparts bien préservés et ses ruelles pavées. Vous pourrez visiter le musée de la Pêche, qui retrace l’histoire maritime de la région, et profiter des nombreuses boutiques d’artisans et cafés locaux. Depuis les remparts, admirez une vue imprenable sur le port et les environs. Une promenade incontournable pour les amateurs d’histoire et de paysages pittoresques.', 'https://www.concarneau-visite.bzh', 1, 2, 10, 'standard', 120,14),
('Découverte des Alignements de Carnac', 'Explorez les mystérieux alignements de Carnac, un site mégalithique unique en Europe.', 'Carnac', 'Les alignements de Carnac sont une série impressionnante de plus de 3 000 menhirs érigés il y a des milliers d’années. Ce site archéologique, unique au monde, intrigue par son ampleur et son mystère. Accompagné d’un guide, découvrez les théories sur leur origine et leur usage, ainsi que les légendes qui entourent ces pierres. Le parcours inclut une balade à travers les principaux alignements, tels que Le Ménec et Kermario, ainsi que la visite d’un tumulus. Une expérience fascinante pour les passionnés d’histoire, de culture celtique et de patrimoine breton.', 'https://www.carnac-aligne.bzh', 2, 3, 15, 'standard', 180,15),
('Balade à Saint-Malo', 'Une immersion dans les remparts de Saint-Malo, avec des panoramas exceptionnels sur la mer.', 'Saint-Malo', 'Découvrez Saint-Malo, la célèbre cité corsaire, en vous promenant sur ses remparts. Cette balade vous offre une vue imprenable sur la mer, les plages et le port. Apprenez l’histoire de cette ville fortifiée, notamment son rôle stratégique dans le commerce et la piraterie. Ne manquez pas la cathédrale Saint-Vincent et explorez les ruelles animées du centre-ville, où vous trouverez des boutiques, des crêperies et des souvenirs locaux. Une visite incontournable pour comprendre le passé maritime de la Bretagne et profiter d’un cadre exceptionnel.', 'https://www.saintmalo-tourisme.bzh', 2, 4, 5, 'gratuite', 90,16);

-- ####################################################################
-- INSERTION D'OFFRES DE SPECTACLES
-- ####################################################################visite

-- Insertion dans offre_spectacle
INSERT INTO offre_spectacle(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement, duree, capacite, date_evenement)
VALUES 
('Fest-Noz Traditionnel', 'Plongez dans une soirée bretonne festive, avec danses et musique traditionnelle en live.', 'Quimper', 'Le Fest-Noz, inscrit au patrimoine immatériel de l’UNESCO, est une véritable célébration de la culture bretonne. Lors de cette soirée animée, laissez-vous emporter par les rythmes envoûtants de la musique traditionnelle jouée en live par des groupes locaux, avec des instruments tels que la bombarde, le biniou et l’accordéon. Les danses bretonnes, faciles à apprendre, invitent tout le monde, débutants ou experts, à se joindre à la ronde. Les pauses sont l’occasion de déguster des spécialités bretonnes comme le cidre et les crêpes. Un événement unique pour vivre la convivialité et la richesse culturelle de la Bretagne.', 'https://www.festnoz-quimper.bzh', 1, 1, 20, 'standard', 180, 200,11),
('Spectacle Son et Lumière', 'Découvrez l’histoire bretonne à travers un spectacle captivant mêlant projections lumineuses et musique.', 'Saint-Malo', 'Ce spectacle son et lumière, organisé dans les remparts de Saint-Malo, offre une immersion spectaculaire dans l’histoire bretonne. Les projections animées illuminent les vieilles pierres, racontant des récits captivants sur les corsaires, les batailles maritimes et les légendes locales. Accompagné d’une bande-son envoûtante et parfois de performances en direct, ce spectacle est une expérience unique pour petits et grands. Une belle façon de redécouvrir Saint-Malo sous un angle artistique et immersif.', 'https://www.saintmalo-spectacle.bzh', 2, 2, 25, 'premium', 90, 300,12),
('Concert de Musique Celtique', 'Un concert vibrant de musique celtique, mêlant instruments traditionnels et modernité.', 'Lorient', 'Plongez dans l’univers de la musique celtique avec ce concert exceptionnel organisé à Lorient, la capitale interceltique. Des artistes renommés vous offrent une performance où se mêlent instruments traditionnels comme la harpe celtique, la cornemuse et le violon, avec des arrangements modernes. Ce concert, empreint d’émotion et d’énergie, célèbre la richesse musicale des pays celtiques, avec des airs entraînants et des ballades nostalgiques. Un événement incontournable pour les amateurs de musique et de culture bretonne.', 'https://www.celticlorient.bzh', 2, 3, 30, 'standard', 120, 150,13);

-- ####################################################################
-- INSERTION D'OFFRES DE PARCS D'ATTRACTION
-- ####################################################################

-- Insertion dans offre_parc_attraction
INSERT INTO offre_parc_attraction(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement, nb_attractions, age_min, plan)
VALUES 
('Parc de l’Armorique', 'Explorez le Parc Naturel Régional d’Armorique : nature, découvertes et aventures pour tous !', 'Brest', 'Le Parc Naturel Régional d’Armorique est un lieu unique où la nature et les activités ludiques se rencontrent. Avec ses sentiers de randonnée, ses circuits thématiques et ses zones dédiées à la faune et la flore bretonnes, le parc est idéal pour les amoureux de la nature. Petits et grands peuvent participer à des ateliers sur l’environnement, explorer des villages traditionnels et découvrir les légendes locales à travers des parcours interactifs. Le parc propose également des aires de jeux, des zones de pique-nique et des attractions éducatives. Une destination parfaite pour une journée en famille, mêlant détente et apprentissage dans un cadre exceptionnel.', 'https://www.parc-armorique.bzh', 1, 1, 25, 'standard', 10, 3, 'plan-armorique.webp'),
('La Récré des 3 Curés', 'Venez vivre des sensations fortes et des moments de détente dans ce parc familial breton !', 'Milizac', 'Situé à Milizac, près de Brest, La Récré des 3 Curés est un parc d’attractions familial proposant des activités variées pour tous les âges. Avec ses montagnes russes, ses manèges aquatiques et son célèbre bateau pirate, le parc offre des sensations fortes aux amateurs d’adrénaline. Les plus jeunes profiteront d’espaces adaptés, avec des structures gonflables, des petits trains et des zones de jeux. Entre deux attractions, les visiteurs peuvent se détendre dans les aires de pique-nique ou savourer des spécialités bretonnes dans les restaurants du parc. La Récré des 3 Curés garantit une journée mémorable dans un cadre verdoyant et convivial.', 'https://www.larecredes3cures.com', 2, 2, 40, 'premium', 15, 5, 'plan-recre.webp'),
('Océanopolis', 'Partez à la découverte des mondes marins dans ce parc scientifique et ludique.', 'Brest', 'Océanopolis, situé à Brest, est bien plus qu’un aquarium. C’est un parc dédié à l’exploration des océans et à la sensibilisation à leur préservation. Avec ses trois pavillons thématiques (polaire, tropical et tempéré), les visiteurs peuvent admirer une grande variété d’espèces marines, des manchots aux requins, en passant par les méduses. Des animations interactives, des projections et des ateliers pédagogiques enrichissent l’expérience. Océanopolis est un lieu incontournable pour découvrir les merveilles de la vie marine, dans un cadre moderne et immersif, adapté à tous les âges.', 'https://www.oceanopolis.com', 2, 3, 25, 'standard', 8, 3, 'plan-oceanopolis.webp');

-- ####################################################################
-- INSERTION D'OFFRES DE RESTAURATION
-- ####################################################################

-- Insertion dans offre_restauration
INSERT INTO offre_restauration(titre, resume, ville, description_detaille, site_web, id_compte_professionnel, id_adresse, abonnement, gamme_prix, carte)
VALUES 
('Festin Breton', 'Découvrez un festin breton mettant à l’honneur les spécialités locales dans une ambiance chaleureuse et conviviale.', 'Quimper', 'Plongez au cœur des traditions culinaires bretonnes dans notre restaurant. Chaque plat célèbre les richesses de la Bretagne : galettes de sarrasin croustillantes, crêpes sucrées délicates, fruits de mer fraîchement pêchés et cidres artisanaux. Préparée avec soin par notre chef, notre cuisine met en avant des produits locaux et de saison, issus directement des producteurs de la région. Dans un cadre rustique et authentique, savourez une expérience unique où la convivialité est à l’honneur. Que vous veniez pour un repas en famille ou une soirée entre amis, notre établissement promet un moment inoubliable.', 'https://www.festinbreton.bzh', 1, 1, 30, 'standard', '€€', 'festin-breton.webp'),
('Restaurant Gastronomique Breton', 'Savourez une cuisine bretonne raffinée dans un cadre élégant, avec des produits de la mer et des terres bretonnes sublimés.', 'Saint-Malo', 'Notre restaurant gastronomique breton vous invite à une expérience culinaire unique, alliant tradition et modernité. À travers une carte imaginée par notre chef étoilé, découvrez des créations mettant en valeur des produits locaux d’exception : homard de Bretagne, coquilles Saint-Jacques, algues marines et légumes de saison. Chaque assiette est un hommage aux saveurs authentiques de la région, sublimées par des techniques culinaires modernes. Dans une salle élégante avec vue sur la mer, profitez d’un service attentif et personnalisé. Une adresse incontournable pour les amateurs de gastronomie et les curieux en quête de nouvelles sensations gustatives.', 'https://www.gastrobreton.com', 2, 2, 120, 'premium', '€€€', 'gastro-breton.webp'),
('Crêperie Authentique', 'Découvrez les saveurs des galettes et crêpes bretonnes dans une crêperie traditionnelle.', 'Brest', 'Notre crêperie vous invite à savourer l’authenticité des galettes et crêpes bretonnes, préparées selon les recettes traditionnelles transmises de génération en génération. Accompagnées de cidre local, nos galettes, réalisées avec de la farine de sarrasin breton, se déclinent en une variété de garnitures, des plus classiques aux plus originales. Dans un cadre chaleureux et typiquement breton, vous pourrez également déguster nos crêpes sucrées, garnies de caramel au beurre salé maison ou de confitures artisanales. Que ce soit pour un repas rapide ou un moment convivial, notre crêperie est l’adresse idéale pour découvrir ou redécouvrir les incontournables de la Bretagne.', 'https://www.creperieauthentique.bzh', 2, 3, 15, 'standard', '€', 'creperie-authentique.webp');

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
('Nature'),
('Famille'),
('Insolite'),
('Groupe'),
('Musique'),
('Solo');

-- ####################################################################
-- ASSOCIATION DES TAGS AUX OFFRES
-- ####################################################################
-- Offres de restauration
INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
-- Festin Breton
(1, 'Gastronomie'),
(1, 'Tradition'),
(1, 'Relaxation'),
(1, 'Groupe'),

-- Restaurant Gastronomique Breton
(2, 'Gastronomie'),
(2, 'Romantique'),
(2, 'Innovation'),
(2, 'Eco-responsable'),

-- Crêperie Authentique
(3, 'Gastronomie'),
(3, 'Tradition'),
(3, 'Relaxation'),
(3, 'Découverte');

-- Offres d'activités
INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
-- Balade sur la Côte Sauvage
(4, 'Découverte'),
(4, 'Relaxation'),
(4, 'Eco-responsable'),

-- Atelier de Tissage Breton
(5, 'Artisanat'),
(5, 'Tradition'),
(5, 'Découverte'),
(5, 'Relaxation');

-- Offres de visite
INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
-- Visite de la Ville Close
(6, 'Histoire'),
(6, 'Découverte'),
(6, 'Tradition'),
(6, 'Relaxation'),

-- Découverte des Alignements de Carnac
(7, 'Histoire'),
(7, 'Découverte'),
(7, 'Insolite'),
(7, 'Eco-responsable'),

-- Balade à Saint-Malo
(8, 'Histoire'),
(8, 'Plage'),
(8, 'Relaxation'),
(8, 'Groupe');

-- Offres de spectacles
INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
-- Fest-Noz Traditionnel
(9, 'Festif'),
(9, 'Tradition'),
(9, 'Musique'),
(9, 'Groupe'),

-- Spectacle Son et Lumière
(10, 'Histoire'),
(10, 'Innovation'),
(10, 'Nocturne'),
(10, 'Insolite'),

-- Concert de Musique Celtique
(11, 'Musique'),
(11, 'Tradition'),
(11, 'Festif'),
(11, 'Relaxation');

-- Offres de parcs d'attraction
INSERT INTO _offre_possede_tag(id_offre, nom_tag) 
VALUES 
-- Parc de l’Armorique
(12, 'Nature'),
(12, 'Découverte'),
(12, 'Eco-responsable'),
(12, 'Relaxation'),

-- La Récré des 3 Curés
(13, 'Famille'),
(13, 'Aventure extrême'),
(13, 'Festif'),
(13, 'Relaxation'),

-- Océanopolis
(14, 'Nature'),
(14, 'Découverte'),
(14, 'Innovation'),
(14, 'Eco-responsable');

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
(11, 'image17.webp'),
(12, 'image18.webp'),
(13, 'image19.webp'),
(14, 'image20.webp'),
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


INSERT INTO sae.compte_professionnel_prive(nom_compte, prenom, email, tel, mot_de_passe, id_adresse, denomination, a_propos, site_web, siren)
VALUES ('Durand', 'Alice', 'alice.durand@example.com', '0612345678', 'MotDePasse123!', 5, 
'Société Informatique Durand', 'Spécialisée dans le développement de logiciels personnalisés.', 
'https://www.durand-informatique.com', '123456789');


INSERT INTO sae._avis(id_membre, id_offre, note, titre, commentaire, nb_pouce_haut, nb_pouce_bas, contexte_visite, publie_le, visite_le)
VALUES 
-- Atelier de Tissage Breton
(3, 2, 5, 'Accueil chaleureux et service au top', 'Le personnel était très accueillant et le service irréprochable.', 1, 0, 'famille', 2, 1),

-- Visite de la ville close
(4, 3, 3, 'Bonne visite, mais quelques points négatifs', 'Le personnel était sympathique, et les découvertes intéressantes', 3, 0, 'famille', 4, 3),

-- Balade à Saint-Malo
(4, 5, 4, 'Belle promenade malgré tout', 'La balade était agréable et bien organisée.', 2, 1, 'amis', 7, 6),

-- Fest-Noz
(4, 6, 2, 'Ambiance mitigée', 'Malgré une bonne ambiance, le service et le personnel m’ont mis mal à l’aise.', 3, 2, 'amis', 8, 7),
(3, 6, 5, 'Plats savoureux et moment agréable', 'Les crêpes étaient délicieuses, j’ai passé un bon moment.', 1, 0, 'famille', 2, 1),

-- Restauration 
(4, 12, 2, 'Expérience décevante', 'Le service n était pas à la hauteur de mes attentes. Plusieurs points à améliorer.', 4, 2, 'affaires', 4, 3),
(3, 13, 5, 'Plats savoureux et moment agréable', 'Les pâtes étaient délicieuses, j’ai passé un très bon moment.', 1, 0, 'solo', 5, 4);

INSERT INTO sae._note_detaillee (nom_note, note, id_avis) VALUES
('Cuisine', 3, 6),
('Service', 1, 6),
('Ambiance', 4, 6),
('Rapport qualité prix', 3, 6),
('Cuisine', 5, 7),
('Service', 4, 7),
('Ambiance', 5, 7),
('Rapport qualité prix', 4, 7);

INSERT INTO sae._reponse(id_avis, texte, publie_le)
VALUES 
(3, 'Nous avons modifié notre carte.', 9),
(4, 'Nous sommes désolé du désagrément causé.', 10);

-- 'En Relief', 'À la Une'
INSERT INTO sae._option(nom_option, id_prix)
VALUES
("En Relief", 5), 
("A la Une", 4);
INSERT INTO sae._offre_souscrit_option(id_offre, nom_option, nb_semaine, id_date)
VALUES
(2, 'En Relief', 0, 5),
(4, 'En Relief', 0, 6),
(6, 'En Relief', 0, 7),
(5, 'À la Une', 0, 8),
(7, 'À la Une', 0, 9),
(8, 'À la Une', 0, 10);
COMMIT;

-- ROLLBACK;