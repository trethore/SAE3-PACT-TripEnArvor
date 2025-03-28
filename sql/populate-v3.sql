ROLLBACK;

SET SCHEMA 'sae';

START TRANSACTION;

DO $$
DECLARE
    -- Le `var_` permet de lever les ambiguïtés entre le nom des variables et le nom des colonnes des tables.
    var_id_adresse  INTEGER;
    var_id_compte   INTEGER;
    var_id_offre    INTEGER;
BEGIN

    INSERT INTO sae._abonnement ("nom_abonnement") 
    VALUES
    ('gratuit'),
    ('standard'),
    ('premium');

    INSERT INTO _tag ("nom_tag") 
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


    -- /* ##################################################################### */
    -- /*                                Example                                */
    -- /* ##################################################################### */

    -- INSERT INTO sae._adresse (
    --     "num_et_nom_de_voie",
    --     "complement_adresse",
    --     "code_postal",
    --     "ville",
    --     "pays"
    -- )
    -- VALUES (
    --     '1 rue Exemple',
    --     NULL,
    --     '22300',
    --     'Lannion',
    --     'France'
    -- )
    -- RETURNING "id_adresse" INTO var_id_adresse;

    -- INSERT INTO sae.compte_professionnel_prive (
    --     "nom_compte",
    --     "prenom",
    --     "email",
    --     "tel",
    --     "mot_de_passe",
    --     "denomination",
    --     "a_propos",
    --     "site_web",
    --     "id_adresse",
    --     "siren"
    -- )
    -- VALUES (
    --     'prénom',
    --     'NOM',
    --     'email@example.com',
    --     '+33606060606',
    --     '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
    --     'Exemple',
    --     'exemple',
    --     'https://example.com/',
    --     var_id_adresse,
    --     '0123456789'
    -- )
    -- RETURNING "id_compte" INTO var_id_compte;

    -- INSERT INTO sae.offre_restauration (
    --     "gamme_prix", 
    --     "carte", 
    --     "titre", 
    --     "resume", 
    --     "ville", 
    --     "description_detaille", 
    --     "site_web", 
    --     "id_compte_professionnel", 
    --     "id_adresse", 
    --     "abonnement",
    --     "nb_jetons",
    --     "jeton_perdu_le",
    --     "lat",
    --     "lon"
    -- )
    -- VALUES (
    --     '€€',
    --     'la carte',
    --     'Example',
    --     'Restaurant',
    --     'Lannion',
    --     'Laborum minim cillum veniam amet quis est anim pariatur quis. Irure qui est nulla exercitation elit ex et cillum magna eu voluptate. Laboris ad eiusmod eu veniam. Aute do ad cillum elit. Est in laboris ipsum aute reprehenderit.',
    --     'https://example.com/',
    --     var_id_compte,
    --     var_id_adresse,
    --     'standard',
    --     NULL,
    --     NULL,
    --     NULL,
    --     NULL
    -- )
    -- RETURNING "id_offre" INTO var_id_offre;

    -- INSERT INTO sae._image
    -- (
    --     "lien_fichier"
    -- )
    -- VALUES
    -- ('image');

    -- INSERT INTO sae._offre_contient_image (
    --     "id_offre",
    --     "id_image"
    -- )
    -- VALUES
    -- (var_id_offre, 'image');

    -- INSERT INTO sae._offre_possede_tag (
    --     "id_offre",
    --     "nom_tag"
    -- )
    -- VALUES
    -- (var_id_offre, 'tag1'),
    -- (var_id_offre, 'tag2'),
    -- (var_id_offre, 'tag3'),
    -- (var_id_offre, 'tag4'),
    -- (var_id_offre, 'tag5');



    /* ##################################################################### */
    /*                        La Krampouzerie Lannion                        */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '5 All. Clemenceau',
        NULL,
        '22300',
        'Lannion',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_prive (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse",
        "siren"
    )
    VALUES (
        'Beatrice',
        'RAULT',
        'lakrampouzerie-lannion@orange.fr',
        '+33296467186',
        '$2y$10$0G1RbctaFe1pig3ZSDO8DO3Vl/yr85JM3gRK0iBkHbL9IPX5XcNFO', -- 'Mot de passe de La Krampouzerie Lannion'
        'LA KRAMPOUZERIE',
        'Crêperie',
        'https://la-krampouzerie-creperie-lannion.eatbu.com/',
        var_id_adresse,
        '888796869'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae.offre_restauration (
        "gamme_prix", 
        "carte", 
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    ) 
    VALUES(
        '€€',
        'https://cdn.website.dish.co/media/be/37/8288528/Galettes-et-crepes.pdf',
        'La Krampouzerie Lannion',
        'Crêperie',
        'Lannion',
        'Bienvenue à La Krampouzerie, située en face de la gare et à deux pas du cinéma à Lannion. Nous vous accueillons dans une ambiance contemporaine, animée d''expositions d''artistes locaux. Notre équipe vous accueille dans un cadre convivial afin que vous puissiez déguster nos galettes et nos crêpes préparées de façon traditionnelle, à la minute avec des préparations « maison » à base de produits locaux. Vous  pourrez déguster l’incontournable complète mais vos papilles seront sans doute interpellées par d''autres associations de saveurs. Sans oublier notre sélection de boissons, y compris des cidres bretons, des bières artisanales ainsi que du vin français. Pour finir votre repas en douceur, on n''oublie pas les crêpes et les glaces artisanales. L''équipe de La Krampouzerie vous attend donc pour un repas décontracté en famille ou entre amis.',
        'https://la-krampouzerie-creperie-lannion.eatbu.com/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image (
        "lien_fichier"
    )
    VALUES (
        'LA-KRAMPOUZERIE-IMG-0555-JPG.jpg'
    );

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES (
        var_id_offre,
        'LA-KRAMPOUZERIE-IMG-0555-JPG.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Gastronomie'),
    (var_id_offre, 'Tradition'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');



    /* ##################################################################### */
    /*                             Le Coste Mor                              */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '162 Rue Saint-Guirec',
        NULL,
        '22700',
        'Perros-Guirec',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_prive (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse",
        "siren"
    )
    VALUES (
        'Vanessa',
        'Hardouin',
        'reception@hotelsaint-guirec.com',
        '+33296914089',
        '$2y$10$3MnsgiZZSTIPyKdvQVGRdOtyWBo0kYZIG2OnXeq3Sh9YfsKMBhGze', -- 'Mot de passe de Hotel Saint Guirec'
        'Hotel Saint Guirec',
        'Hotel',
        'https://la-krampouzerie-creperie-lannion.eatbu.com/',
        var_id_adresse,
        '22700'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae.offre_restauration (
        "gamme_prix", 
        "carte", 
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    )
    VALUES (
        '€€',
        'https://www.alacarte.direct/menu-restaurant/menu/restaurant-le-coste-mor/58-restaurant-le-coste-mor',
        'Le Coste Mor',
        'Restaurant de fruits de mer',
        'Lannion',
        'Face au château de Costaérès et la plage de Saint-Guirec, venez déguster un plateau de fruits de mer, savourez un délicieux plat de poisson ou goûter au plaisir simple des traditionnelles moules-frites bretonnes. Notre carte vous réserve d’autres belles surprises, n’attendez plus pour réserver !',
        'https://www.hotelsaint-guirec.com/restaurants/le-coste-mor/',
        var_id_compte,
        var_id_adresse,
        'premium',
        3,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image (
        "lien_fichier"
    )
    VALUES (
        'WEB_restaurant_saint_guirec-34.jpg'
    );

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES (
        var_id_offre,
        'WEB_restaurant_saint_guirec-34.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Gastronomie'),
    (var_id_offre, 'Tradition'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');



    /* ##################################################################### */
    /*                              Le Koadenn                               */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '9 Rue Saint-Guillaume',
        NULL,
        '22000',
        'Saint-Brieuc',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_prive (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse",
        "siren"
    )
    VALUES (
        'Martine',
        'JULIE',
        'lekoadenn@hotmail.fr',
        '+33296619377',
        '$2y$10$3MnsgiZZSTIPyKdvQVGRdOtyWBo0kYZIG2OnXeq3Sh9YfsKMBhGze', -- 'Mot de passe de Hotel Saint Guirec'
        'SARL LES DELICES D''ANDRE',
        'Hotel',
        'https://la-krampouzerie-creperie-lannion.eatbu.com/',
        var_id_adresse,
        'FR48491552071'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae.offre_restauration (
        "gamme_prix", 
        "carte", 
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    )
    VALUES (
        '€€',
        'https://www.alacarte.direct/menu-restaurant/menu/restaurant-le-coste-mor/58-restaurant-le-coste-mor',
        'Le Koadenn',
        'Restaurant',
        'Saint-Brieuc',
        '"Le Koadenn" est un restaurant qui sert 3 types de cuisine différentes concoctés par 3 cuisiniers ayant sa spécialité pour satisfaire les goûts de chacun. Une crêperie. Une cuisine traditionnelle. Des grillades sur une plancha. Un accueil chaleureux vous attend au restaurant Le Koadenn à ST BRIEUC ! Nous vous invitons à déguster notre cuisine française authentique. Une cuisine maison exceptionnelle, des produits régionaux et une cuisine traditionnelle sont servis par notre sympathique personnel. Amenez vos amis et votre famille pour déguster nos grillades savoureuses et notre carte aux 3 univers. Nous sommes en mesure de répondre à une multitude de préférences alimentaires, en proposant un large éventail d''options végétariennes. Dans un environnement calme et relaxant, savourez un déjeuner, un dîner et un goûter savoureux.',
        'https://le-koadenn-st-brieuc.eatbu.com/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image (
        "lien_fichier"
    )
    VALUES (
        'Le-Koadenn-IMG-1615-jpeg.jpg'
    );

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES (
        var_id_offre,
        'Le-Koadenn-IMG-1615-jpeg.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Gastronomie'),
    (var_id_offre, 'Tradition'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');



    /* ##################################################################### */
    /*                              Armoripark                               */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        'Armoripark, Rue de Gwenezhan',
        NULL,
        '22140',
        'Bégard',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_prive (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse",
        "siren"
    )
    VALUES (
        'Prénom',
        'NOM',
        'email@armoripark.com',
        '+33296453636',
        '$2y$10$NLe0ZT5LsAZpULI.u/hBj.n4Zr3rS6cJW5Ctu98OjVylsvkvoOKoi', -- 'Mot de passe Armoripark'
        'Armoripark',
        'Dans un cadre idyllique, à Bégard, en plein cœur des Côtes d’Armor, Armoripark vous propose de nombreuses activités de plein air.',
        'https://armoripark.com/',
        var_id_adresse,
        '0123456789'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae.offre_parc_attraction (
        "nb_attractions", 
        "age_min",
        "plan",
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    )
    VALUES (
        '30',
        2,
        'https://armoripark.com/#home_page-plan',
        'Armoripark',
        'Votre Parc de loisirs en Côtes d''Armor',
        'Bégard',
        'Sautez, grimpez, glissez ! Avec ses trampolines et ses structures gonflables, sa piste de luge d’été et son pentogliss, sa tyrolienne de 300m de long et ses 300m² de filets dans les arbres, Armoripark est le lieu idéal pour les aventuriers de tous âges. Pour des moments de convivialité et de complicité, vous pourrez vous initier au mini-golf et faire un tour en pédalo ! Et pour l’émerveillement des petits et des grands, admirez nos animaux de la ferme et notre volière !',
        'https://armoripark.com/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image (
        "lien_fichier"
    )
    VALUES 
    ('bassins-chauffes.webp'),
    ('toboggan1.webp'),
    ('ventriglisse.webp'),
    ('luge.webp'),
    ('armoripark-glisser-pentogliss-2.jpg'),
    ('glisse-aquatique.webp'),
    ('pedalos.webp'),
    ('1683040587428.jpg'),
    ('armoripark-sevader-bateaux_mississipi.jpg'),
    ('trampofilets.webp'),
    ('homeball2024.webp'),
    ('armoripark-tyrolienne.jpg');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre, 'bassins-chauffes.webp'),
    (var_id_offre, 'toboggan1.webp'),
    (var_id_offre, 'ventriglisse.webp'),
    (var_id_offre, 'luge.webp'),
    (var_id_offre, 'armoripark-glisser-pentogliss-2.jpg'),
    (var_id_offre, 'glisse-aquatique.webp'),
    (var_id_offre, 'pedalos.webp'),
    (var_id_offre, '1683040587428.jpg'),
    (var_id_offre, 'armoripark-sevader-bateaux_mississipi.jpg'),
    (var_id_offre, 'trampofilets.webp'),
    (var_id_offre, 'homeball2024.webp'),
    (var_id_offre, 'armoripark-tyrolienne.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Sport'),
    (var_id_offre, 'Festif'),
    (var_id_offre, 'Découverte'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');


    /* ##################################################################### */
    /*                             Parc Aquarev                              */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '20 rue Notre-dame',
        NULL,
        '22600',
        'Loudéac',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_publique (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse"
    )
    VALUES (
        'Bruno',
        'LE BESCAUT',
        'mairie@ville-loudeac.fr',
        '+33296668500',
        '$2y$10$s18bwyAsQCTNOezoVEMTF.FTfq9cFuH6.WT5CsZekqmG21A/ALbmq', -- 'Mot de passe Mairie de Loudéac'
        'Mairie de Loudéac',
        'Mairie de Loudéac',
        'https://www.ville-loudeac.fr/',
        var_id_adresse
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        'Rue du Mène',
        NULL,
        '22600',
        'Loudéac',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.offre_visite (
        "duree", 
        "date_evenement", 
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    )
    VALUES (
        120,
        NULL,
        'Parc Aquarev',
        'Découvrez le parc de loisirs Aquarev, véritable poumon vert de Loudéac.',
        'Loudéac',
        '30 hectares aménagés autour d’un étang pour le loisir et la détente : une plaine de jeux, un labyrinthe, une bambouseraie, des pontons de pêche, des aires de pique-nique, un parcours sportif, un terrain multi-sport, le tout dans une ambiance détendue, « zen »… Pour tous et en accès libre. Autour de son « tipi » emblématique, le site présente une nouvelle image d’un parc de loisirs, de promenades, de vie au cœur d’un espace vert rustique qui permet à chacun de trouver des lieux différents, pour se rencontrer, prendre du bon temps et du plaisir. Le site accueille régulièrement de nombreux événements municipaux et manifestations associatives. Chaque mercredi d’été, venez par exemple participer aux « Ateliers d’Aquarev » sous le tipi, des animations gratuite pour toute la famille proposées par des commerçant ou intervenants du territoire autour des plantes et de la nature.',
        'https://www.ville-loudeac.fr/listes/parc-aquarev/',
        var_id_compte,
        var_id_adresse,
        'gratuit',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image (
        "lien_fichier"
    )
    VALUES
    ('DSC_1891-Copier.jpg'),
    ('aquarev-louvafilm-2018-6.jpg'),
    ('DSC_1889-Copier.jpg'),
    ('aquarev-louvafilm-2018-5.jpg'),
    ('aquarev-louvafilm-2018-4.jpg'),
    ('aquarev-louvafilm-2018-3.jpg'),
    ('aquarev-louvafilm-2018-2.jpg'),
    ('aquarev-louvafilm-2018-1.jpg'),
    ('aquarev-louvafilm-2018-11.jpg'),
    ('aquarev-louvafilm-2018-10.jpg'),
    ('aquarev-louvafilm-2018-9.jpg'),
    ('aquarev-louvafilm-2018-8.jpg'),
    ('DSC_1913-Copier.jpg'),
    ('DSC_1896-Copier.jpg'),
    ('DSC_1902-Copier.jpg'),
    ('aquarev-louvafilm-2018-7.jpg');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre, 'DSC_1891-Copier.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-6.jpg'),
    (var_id_offre, 'DSC_1889-Copier.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-5.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-4.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-3.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-2.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-1.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-11.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-10.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-9.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-8.jpg'),
    (var_id_offre, 'DSC_1913-Copier.jpg'),
    (var_id_offre, 'DSC_1896-Copier.jpg'),
    (var_id_offre, 'DSC_1902-Copier.jpg'),
    (var_id_offre, 'aquarev-louvafilm-2018-7.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Bien-être'),
    (var_id_offre, 'Romantique'),
    (var_id_offre, 'Relaxation'),
    (var_id_offre, 'Eco-responsable'),
    (var_id_offre, 'Nature'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');



    /* ##################################################################### */
    /*                        Château de Coat-an-Noz                         */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '50, avenue de Texier LabbeBourg',
        NULL,
        '22810',
        'Belle-Isle-en-Terre',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.compte_professionnel_prive (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "denomination",
        "a_propos",
        "site_web",
        "id_adresse",
        "siren"
    )
    VALUES (
        'Dilan',
        'NUADJE',
        'nuadjedidi@gmail.com',
        '+33355176213',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Exemple',
        'exemple',
        'https://example.com/',
        var_id_adresse,
        '0123456789'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        'Allée du Château',
        NULL,
        '22810',
        'Belle-Isle-en-Terre',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae.offre_visite (
        "duree", 
        "date_evenement",
        "titre", 
        "resume", 
        "ville", 
        "description_detaille", 
        "site_web", 
        "id_compte_professionnel", 
        "id_adresse", 
        "abonnement",
        "nb_jetons",
        "jeton_perdu_le",
        "lat",
        "lon"
    )
    VALUES (
        '120',
        NULL,
        'Château de Coat-an-Noz',
        'Vous aimez les vieilles pierres ? Vous allez adorer découvrir le château de Coat-an-Noz, devenu célèbre grâce à Lady Mond, cette fille de meunier qui est devenue riche en épousant le roi du nickel, Sir Robert Mond.',
        'Lannion',
        'Situé dans les Côtes d’Armor en Bretagne, ce château se trouve sur la commune de Belle-Isle-en-Terre, proche de la nationale 12. La commune compte moins de 1500 habitants et se trouve à une demi-heure de Guingamp. Coat an Noz est excentré au sud du bourg. Il est au milieu de la forêt, à environ 5 kilomètres. Cette dernière est traversée par une rivière : le Leguer. Cela a son importance dans l’évolution du château. Vous reconnaîtrez facilement le château. Sa forme est rectangulaire avec une tourelle à chaque angle. La pierre utilisée est évidemment locale avec le granit et le schiste. Quant au toit, il est fait d’ardoise, une construction typiquement bretonne.',
        'https://www.ot-belle-isle-en-terre.com/chateau-coat-an-noz/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('Biet_chateau_coatannoz.jpg'),
    ('valeur-Chateau-de-Coat-an-Noz-768x432.jpg');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre, 'Biet_chateau_coatannoz.jpg'),
    (var_id_offre, 'valeur-Chateau-de-Coat-an-Noz-768x432.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre, 'Histoire'),
    (var_id_offre, 'Relaxation'),
    (var_id_offre, 'Découverte'),
    (var_id_offre, 'Tradition'),
    (var_id_offre, 'Nature'),
    (var_id_offre, 'Famille'),
    (var_id_offre, 'Groupe'),
    (var_id_offre, 'Solo');

END $$;



COMMIT;
-- ROLLBACK;

