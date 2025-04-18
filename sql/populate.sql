ROLLBACK;

SET SCHEMA 'sae';

START TRANSACTION;

DO $$
DECLARE
    -- Le `var_` permet de lever les ambiguïtés entre le nom des variables et le nom des colonnes des tables.
    var_id_adresse              INTEGER;
    var_id_compte               INTEGER;
    var_id_date                 INTEGER;
    var_id_date2                INTEGER;
    var_id_date_souscription    INTEGER;
    var_nom_prestation          VARCHAR(128);

    var_id_offre_krampouzerie                   INTEGER;
    var_id_offre_coste_mor                      INTEGER;
    var_id_offre_koadenn                        INTEGER;
    var_id_offre_armoripark                     INTEGER;
    var_id_offre_aquarev                        INTEGER;
    var_id_offre_coat_an_noz                    INTEGER;
    var_id_offre_terrarium_vivarium_kerdanet    INTEGER;
    var_id_offre_musee_resistance_argoat        INTEGER;
    var_id_offre_fort_latte                     INTEGER;
    var_id_offre_THE_JEFF_PANACLOC_COMPANY      INTEGER;
    var_id_offre_WALY_DIA                       INTEGER;
    var_id_offre_CALOGERO                       INTEGER;
    var_id_offre_labyrinthe_malido              INTEGER;

BEGIN

    INSERT INTO sae._abonnement ("nom_abonnement") 
    VALUES
    ('gratuit'),
    ('standard'),
    ('premium');

    INSERT INTO _option ("nom_option")
    VALUES 
    ('En Relief'), 
    ('À la Une');

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


    INSERT INTO compte_membre (
        "nom_compte", 
        "prenom", 
        "email", 
        "tel", 
        "mot_de_passe", 
        "pseudo"
    )
    VALUES (
        'anonyme', 
        'anonyme', 
        'anonyme@ano.com', 
        '9999999999', 
        '', 
        'Ancien Utilisateur'
    );


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
    RETURNING "id_offre" INTO var_id_offre_krampouzerie;

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
        var_id_offre_krampouzerie,
        'LA-KRAMPOUZERIE-IMG-0555-JPG.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_krampouzerie, 'Gastronomie'),
    (var_id_offre_krampouzerie, 'Tradition'),
    (var_id_offre_krampouzerie, 'Famille'),
    (var_id_offre_krampouzerie, 'Groupe'),
    (var_id_offre_krampouzerie, 'Solo');

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-01'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_krampouzerie,
        var_id_date
    );

    INSERT INTO _date_souscription_option (
        "date_debut",
        "nb_semaines"
    )
    VALUES (
        '2025-03-31',
        4
    )
    RETURNING "id_date_souscription" INTO var_id_date_souscription;

    INSERT INTO _offre_souscrit_option (
        "id_offre",
        "nom_option",
        "id_date_souscription"
    )
    VALUES (
        var_id_offre_krampouzerie,
        'En Relief',
        var_id_date_souscription
    );



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
    RETURNING "id_offre" INTO var_id_offre_coste_mor;

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
        var_id_offre_coste_mor,
        'WEB_restaurant_saint_guirec-34.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_coste_mor, 'Gastronomie'),
    (var_id_offre_coste_mor, 'Tradition'),
    (var_id_offre_coste_mor, 'Famille'),
    (var_id_offre_coste_mor, 'Groupe'),
    (var_id_offre_coste_mor, 'Solo');

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-02'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_coste_mor,
        var_id_date
    );

    INSERT INTO _date_souscription_option (
        "date_debut",
        "nb_semaines"
    )
    VALUES (
        '2025-03-28',
        4
    )
    RETURNING "id_date_souscription" INTO var_id_date_souscription;

    INSERT INTO _offre_souscrit_option (
        "id_offre",
        "nom_option",
        "id_date_souscription"
    )
    VALUES (
        var_id_offre_coste_mor,
        'À la Une',
        var_id_date_souscription
    );




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
    RETURNING "id_offre" INTO var_id_offre_koadenn;

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
        var_id_offre_koadenn,
        'Le-Koadenn-IMG-1615-jpeg.jpg'
    );

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_koadenn, 'Gastronomie'),
    (var_id_offre_koadenn, 'Tradition'),
    (var_id_offre_koadenn, 'Famille'),
    (var_id_offre_koadenn, 'Groupe'),
    (var_id_offre_koadenn, 'Solo');

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-03'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_koadenn,
        var_id_date
    );

    INSERT INTO _date_souscription_option (
        "date_debut",
        "nb_semaines"
    )
    VALUES (
        '2025-03-30',
        4
    )
    RETURNING "id_date_souscription" INTO var_id_date_souscription;

    INSERT INTO _offre_souscrit_option (
        "id_offre",
        "nom_option",
        "id_date_souscription"
    )
    VALUES (
        var_id_offre_koadenn,
        'En Relief',
        var_id_date_souscription
    );



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
        'Jean',
        'DUPONT',
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
    RETURNING "id_offre" INTO var_id_offre_armoripark;

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
    (var_id_offre_armoripark, 'bassins-chauffes.webp'),
    (var_id_offre_armoripark, 'toboggan1.webp'),
    (var_id_offre_armoripark, 'ventriglisse.webp'),
    (var_id_offre_armoripark, 'luge.webp'),
    (var_id_offre_armoripark, 'armoripark-glisser-pentogliss-2.jpg'),
    (var_id_offre_armoripark, 'glisse-aquatique.webp'),
    (var_id_offre_armoripark, 'pedalos.webp'),
    (var_id_offre_armoripark, '1683040587428.jpg'),
    (var_id_offre_armoripark, 'armoripark-sevader-bateaux_mississipi.jpg'),
    (var_id_offre_armoripark, 'trampofilets.webp'),
    (var_id_offre_armoripark, 'homeball2024.webp'),
    (var_id_offre_armoripark, 'armoripark-tyrolienne.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_armoripark, 'Sport'),
    (var_id_offre_armoripark, 'Festif'),
    (var_id_offre_armoripark, 'Découverte'),
    (var_id_offre_armoripark, 'Famille'),
    (var_id_offre_armoripark, 'Groupe'),
    (var_id_offre_armoripark, 'Solo');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('Mars → Juin : 11h - 18h : < 4 ans', 0, var_id_offre_armoripark),
    ('Mars → Juin : 11h - 18h : 4 - 17 ans', 11, var_id_offre_armoripark),
    ('Mars → Juin : 11h - 18h : 18 - 64 ans', 12, var_id_offre_armoripark),
    ('Mars → Juin : 14h - 18h : < 4 ans', 0, var_id_offre_armoripark),
    ('Mars → Juin : 14h - 18h : 4 - 17 ans', 5, var_id_offre_armoripark),
    ('Mars → Juin : 14h - 18h : 18 - 64 ans', 6, var_id_offre_armoripark),
    ('Juillet → Août : 14h - 18h30 : 18 - 64 ans', 0, var_id_offre_armoripark),
    ('Juillet → Août : 14h - 18h30 : 18 - 64 ans', 13, var_id_offre_armoripark),
    ('Juillet → Août : 14h - 18h30 : 18 - 64 ans', 15, var_id_offre_armoripark);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-04'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_armoripark,
        var_id_date
    );

    INSERT INTO _date_souscription_option (
        "date_debut",
        "nb_semaines"
    )
    VALUES (
        '2025-03-29',
        4
    )
    RETURNING "id_date_souscription" INTO var_id_date_souscription;

    INSERT INTO _offre_souscrit_option (
        "id_offre",
        "nom_option",
        "id_date_souscription"
    )
    VALUES (
        var_id_offre_armoripark,
        'À la Une',
        var_id_date_souscription
    );


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
    RETURNING "id_offre" INTO var_id_offre_aquarev;

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
    (var_id_offre_aquarev, 'DSC_1891-Copier.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-6.jpg'),
    (var_id_offre_aquarev, 'DSC_1889-Copier.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-5.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-4.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-3.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-2.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-1.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-11.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-10.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-9.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-8.jpg'),
    (var_id_offre_aquarev, 'DSC_1913-Copier.jpg'),
    (var_id_offre_aquarev, 'DSC_1896-Copier.jpg'),
    (var_id_offre_aquarev, 'DSC_1902-Copier.jpg'),
    (var_id_offre_aquarev, 'aquarev-louvafilm-2018-7.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_aquarev, 'Bien-être'),
    (var_id_offre_aquarev, 'Romantique'),
    (var_id_offre_aquarev, 'Relaxation'),
    (var_id_offre_aquarev, 'Eco-responsable'),
    (var_id_offre_aquarev, 'Nature'),
    (var_id_offre_aquarev, 'Famille'),
    (var_id_offre_aquarev, 'Groupe'),
    (var_id_offre_aquarev, 'Solo');

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-05'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_aquarev,
        var_id_date
    );



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
    RETURNING "id_offre" INTO var_id_offre_coat_an_noz;

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
    (var_id_offre_coat_an_noz, 'Biet_chateau_coatannoz.jpg'),
    (var_id_offre_coat_an_noz, 'valeur-Chateau-de-Coat-an-Noz-768x432.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_coat_an_noz, 'Histoire'),
    (var_id_offre_coat_an_noz, 'Relaxation'),
    (var_id_offre_coat_an_noz, 'Découverte'),
    (var_id_offre_coat_an_noz, 'Tradition'),
    (var_id_offre_coat_an_noz, 'Nature'),
    (var_id_offre_coat_an_noz, 'Famille'),
    (var_id_offre_coat_an_noz, 'Groupe'),
    (var_id_offre_coat_an_noz, 'Solo');

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-06'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_coat_an_noz,
        var_id_date
    );



    /* ##################################################################### */
    /*                   Terrarium & Vivarium de Kerdanet                    */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '38 Kerdanet',
        NULL,
        '22170',
        'Châtelaudren-Plouagat',
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
        'Le serpentologue.............',
        '',
        'serpentologue@example.com',
        '',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Le serpentologue.............',
        'Attentif à toute la nature. Réellement Passionné par la faune sauvage Française et en particulier par les animaux soi-disant "mal aimés" : les reptiles et les amphibiens, rapaces, chauves souris, araignées et bien d''autres. Les fougères, les mousses...',
        'https://example.com/',
        var_id_adresse,
        '0123456789'
    )
    RETURNING "id_compte" INTO var_id_compte;

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
        'Terrarium & Vivarium de Kerdanet',
        'Le monde passionnant des reptiles et des amphibiens. REFUGE officiel pour reptiles venimeux ou non.',
        'Lannion',
        'Le monde passionnant des reptiles et des amphibiens. REFUGE officiel pour reptiles venimeux ou non. Identifications des serpents Français et étrangers. Interventions sur saisies judiciaires, Abandons et découvertes sur la voie publique de tous reptiles ou amphibiens.',
        'https://terrariumdekerdanet.over-blog.com/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_terrarium_vivarium_kerdanet;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('ob_689004_dsc-1501.jpg'),
    ('image_0576388_20221221_ob_42a684_mamba-vert-2.jpg'),
    ('ob_88d14f_copie-de-1-36.JPG'),
    ('ob_1d3168_bb-peliade-1.JPG'),
    ('ob_52f788_naja-annulifera.jpg'),
    ('ob_f2f582_boa-emeuraude-1.jpg'),
    ('ob_32e44b_x-8.JPG'),
    ('ob_d64060_verte.jpg'),
    ('ob_7e23a8_rieuse.JPG'),
    ('ob_9f537f_rainette-verte.jpg'),
    ('ob_b907df_bufo.JPG');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_689004_dsc-1501.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'image_0576388_20221221_ob_42a684_mamba-vert-2.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_88d14f_copie-de-1-36.JPG'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_1d3168_bb-peliade-1.JPG'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_52f788_naja-annulifera.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_f2f582_boa-emeuraude-1.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_32e44b_x-8.JPG'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_d64060_verte.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_7e23a8_rieuse.JPG'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_9f537f_rainette-verte.jpg'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'ob_b907df_bufo.JPG');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_terrarium_vivarium_kerdanet, 'Découverte'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'Eco-responsable'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'Nature'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'Famille'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'Groupe'),
    (var_id_offre_terrarium_vivarium_kerdanet, 'Solo');


    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('3 ans et -', 0, var_id_offre_terrarium_vivarium_kerdanet),
    ('Enfant de 4 à 12 ans', 8, var_id_offre_terrarium_vivarium_kerdanet),
    ('Adulte et Enfant de + de 12 ans', 10, var_id_offre_terrarium_vivarium_kerdanet),
    ('Groupe (+15 personnes) : Enfant de 4 à 12 ans', 7, var_id_offre_terrarium_vivarium_kerdanet),
    ('Groupe (+15 personnes) : Adulte et Enfant de + de 12 ans ', 9, var_id_offre_terrarium_vivarium_kerdanet),
    ('Journée soigneur', 60, var_id_offre_terrarium_vivarium_kerdanet);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-07'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_terrarium_vivarium_kerdanet,
        var_id_date
    );



    /* ##################################################################### */
    /*                   Musée de la Résistance en Argoat                    */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        'L’Étang Neuf',
        NULL,
        '22480',
        'Saint-Connan',
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
        'Jean',
        'DUPOND',
        'etangneuf.asso@orange.fr',
        '+33296471766',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Exemple',
        'exemple',
        'https://www.musee-etangneuf.fr/',
        var_id_adresse,
        '0123456789'
    )
    RETURNING "id_compte" INTO var_id_compte;

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
        'Musée de la Résistance en Argoat',
        'L’histoire de la Seconde Guerre mondiale et de la Résistance dans l’ouest des Côtes d’Armor vous est racontée à travers cinq espaces d’expositions et une salle de projection.',
        'Saint-Connan',
        'Le musée de la Résistance en Argoat s’inscrit dans un lieu porteur de mémoire. C’est au cœur de la forêt de Coatmallouen que se met en place, en juin 1944, le maquis de Plésidy à Saint-Connan. Fort de plusieurs centaines d’hommes, il affronte les troupes d’occupation lors des combats du 27 juillet 1944 puis participe à la Libération de Guingamp et de sa région. L’histoire de la Seconde Guerre mondiale et de la Résistance dans l’ouest des Côtes d’Armor vous est racontée à travers cinq espaces d’expositions et une salle de projection.',
        'https://www.musee-etangneuf.fr/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_musee_resistance_argoat;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('img-musee-inter1.jpg'),
    ('img-musee-inter2.jpg');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_musee_resistance_argoat, 'img-musee-inter1.jpg'),
    (var_id_offre_musee_resistance_argoat, 'img-musee-inter2.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_musee_resistance_argoat, 'Histoire'),
    (var_id_offre_musee_resistance_argoat, 'Découverte');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('Moins de 7 ans', 0, var_id_offre_musee_resistance_argoat),
    ('7-18 ans', 3, var_id_offre_musee_resistance_argoat),
    ('Réduit', 5, var_id_offre_musee_resistance_argoat),
    ('Plein', 6, var_id_offre_musee_resistance_argoat),
    ('Groupe de 6 à 10 personnes', 50, var_id_offre_musee_resistance_argoat),
    ('Groupe de plus de 10 personnes', 6, var_id_offre_musee_resistance_argoat),
    ('Visite guidée pour les scolaires', 4, var_id_offre_musee_resistance_argoat);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-08'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_musee_resistance_argoat,
        var_id_date
    );

    INSERT INTO _date_souscription_option (
        "date_debut",
        "nb_semaines"
    )
    VALUES (
        '2025-03-31',
        4
    )
    RETURNING "id_date_souscription" INTO var_id_date_souscription;

    INSERT INTO _offre_souscrit_option (
        "id_offre",
        "nom_option",
        "id_date_souscription"
    )
    VALUES (
        var_id_offre_musee_resistance_argoat,
        'En Relief',
        var_id_date_souscription
    );



    /* ##################################################################### */
    /*                           Le Fort la Latte                            */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '28 rue de la Latte',
        NULL,
        '22240',
        'Plévenon',
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
        'Guénolé Joüon',
        'Des Longrais',
        'contact@lefortlalatte.com',
        '+33296415711',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Guénolé Joüon Des Longrais',
        'Guénolé Joüon Des Longrais',
        'https://example.com/',
        var_id_adresse,
        '849249461 00021'
    )
    RETURNING "id_compte" INTO var_id_compte;

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
        'Le Fort la Latte',
        'Monument historique privé',
        'Lannion',
        'La Roche Goyon tire son nom d’une des plus anciennes familles bretonnes. Une légende atteste qu’un premier château aurait été construit par un Goyon sous Alain Barbe-Torte en 937. Le château fort actuel, quant à lui, fut construit par Étienne III Goyon avant l’apparition du canon en Bretagne (1364) puis poursuivi au gré de la bonne fortune des Goyon dans la deuxième moitié du XIVème siècle. Il existait en 1379 puisque Du Guesclin envoya un détachement à la Roche Goyon qui résista vaillamment. La forteresse fut confisquée au profit de Charles V, puis restituée à son propriétaire par le traité de Guérande (1381). Au cours du XVème siècle, l’ascension sociale des Goyon se poursuivit. Ils figurent aux États de Bretagne. Un Goyon, chambellan du duc de Bretagne, épousera l’héritière de la baronnie de Thorigni-sur-Vire. La famille Goyon quitte le berceau breton et passe à l’histoire de France. Le château reçoit alors un gouverneur qui loge dans un logis aménagé à cet effet.',
        'https://www.lefortlalatte.com/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_fort_latte;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('89228f_03c758e6d4a94c9a8a45217fe75e0c3b.png'),
    ('89228f_7e27777e16884ed4a3faaea8bb493498.jpg'),
    ('89228f_87eaf41cae6840e587e6a6b67fc5a664~mv2_d_4608_3456_s_4_2.jpg'),
    ('89228f_288000e544714099a97085bc57c694f9~mv2_d_4096_2160_s_2.jpg'),
    ('89228f_f26adfd36ca94af58f7779fd58a25eb9~mv2_d_5472_3648_s_4_2.png');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_fort_latte, '89228f_03c758e6d4a94c9a8a45217fe75e0c3b.png'),
    (var_id_offre_fort_latte, '89228f_7e27777e16884ed4a3faaea8bb493498.jpg'),
    (var_id_offre_fort_latte, '89228f_87eaf41cae6840e587e6a6b67fc5a664~mv2_d_4608_3456_s_4_2.jpg'),
    (var_id_offre_fort_latte, '89228f_288000e544714099a97085bc57c694f9~mv2_d_4096_2160_s_2.jpg'),
    (var_id_offre_fort_latte, '89228f_f26adfd36ca94af58f7779fd58a25eb9~mv2_d_5472_3648_s_4_2.png');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_fort_latte, 'Histoire'),
    (var_id_offre_fort_latte, 'Plage'),
    (var_id_offre_fort_latte, 'Découverte'),
    (var_id_offre_fort_latte, 'Tradition'),
    (var_id_offre_fort_latte, 'Famille'),
    (var_id_offre_fort_latte, 'Groupe'),
    (var_id_offre_fort_latte, 'Solo');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('Adultes', 8, var_id_offre_fort_latte),
    ('Étudiants (jusqu''à 26 ans), lycéens, demandeurs d''emploi', 7, var_id_offre_fort_latte),
    ('Enfants (moins de 12 ans)', 5, var_id_offre_fort_latte);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-09'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_fort_latte,
        var_id_date
    );


    /* ##################################################################### */
    /*                       THE JEFF PANACLOC COMPANY                       */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        '28 allée d’Aquitaine',
        NULL,
        '92000',
        'Nanterre',
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
        'François',
        'THOMINET',
        'email@ticketmaster.fr',
        '+33606060606',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Exemple',
        'exemple',
        'https://www.ticketmaster.fr',
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
        '4 Rue Louis de Broglie',
        NULL,
        '22300',
        'Lannion',
        'France'
    )
    RETURNING "id_adresse" INTO var_id_adresse;

    INSERT INTO sae._date ("date")
    VALUES ('2025-10-23')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae.offre_spectacle (
        "duree", 
        "capacite",
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
        1000,
        var_id_date,
        'THE JEFF PANACLOC COMPANY',
        'Retrouvez Jeff Panacloc et Jean-Marc dans un tout nouveau spectacle explorant la condition humaine avec humour, irrévérence et interactivité.',
        'Lannion',
        'L’humour fait son entrée en grande pompe à Lannion avec la première édition du festival LE RIROSKOPE ! Du 22 au 26 octobre 2025, la salle SKOPE se transforme en temple du rire pour cinq soirées exceptionnelles. Pour cette grande première, nous avons réuni cinq artistes incontournables (Sellig, Jeff Panacloc, Guihome, Marie S''infiltre et Laura Laune), chacun avec son style unique, prêts à vous offrir des spectacles mémorables. Préparez-vous à franchir les portes du bureau secret de Jeff Panacloc et à plonger dans un univers où l''humour flirte avec l''absurde. Dans « The Jeff Panacloc Company », chaque personnage détraqué vous promet une soirée délirante et inoubliable. Rejoignez cette troupe fantastique et vivez une expérience immersive unique qui vous laissera transformé et léger.',
        'https://www.ticketmaster.fr/fr/manifestation/the-jeff-panacloc-company-billet/idmanif/613153',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_THE_JEFF_PANACLOC_COMPANY;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('n_the-jeff-panacloc-company_g.webp');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_THE_JEFF_PANACLOC_COMPANY, 'n_the-jeff-panacloc-company_g.webp');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_THE_JEFF_PANACLOC_COMPANY, 'Festif');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('À partir de', 45, var_id_offre_THE_JEFF_PANACLOC_COMPANY);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-10'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_THE_JEFF_PANACLOC_COMPANY,
        var_id_date
    );



    /* ##################################################################### */
    /*                               WALY DIA                                */
    /* ##################################################################### */

    INSERT INTO sae._date ("date")
    VALUES ('2025-10-03')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae.offre_spectacle (
        "duree", 
        "capacite",
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
        60,
        1000,
        var_id_date,
        'WALY DIA',
        'Waly Dia revient avec son nouveau one-man-show : ''Une heure à tuer''. Accordez-lui une heure, il se charge du reste avec son humour combatif et sans concession.',
        'Lannion',
        'Après le succès de « Ensemble ou rien » dont la tournée affichant complet s’est achevée par un Zénith de Paris plein à craquer, Waly Dia est de retour sur scène avec un nouveau one-man-show : « Une heure à tuer ». Et il ne manque pas de choses à dire… Waly Dia monte sur scène comme sur un ring : combatif, provocateur, charmeur et plein de malice. Son sens aigu de l’observation du monde qui nous entoure et de l’actualité nourrissent ses punchlines aussi acides que percutantes. Une attitude et un humour sans concession qui ont fait sa marque de fabrique dans ses chroniques sur France Inter au côté de Charline Vanhoenacker depuis 2020. Ses interventions au débit mitraillette n’épargnant rien, mais surtout personne, ont fédéré un public toujours plus large.',
        'https://www.ticketmaster.fr/fr/manifestation/waly-dia-billet/idmanif/609865',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_WALY_DIA;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('n_waly-dia_g.webp');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_WALY_DIA, 'n_waly-dia_g.webp');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_WALY_DIA, 'Festif');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('À partir de', 42, var_id_offre_WALY_DIA);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-10'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_WALY_DIA,
        var_id_date
    );



    /* ##################################################################### */
    /*                               CALOGERO                                */
    /* ##################################################################### */

    INSERT INTO sae._date ("date")
    VALUES ('2025-10-11')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae.offre_spectacle (
        "duree", 
        "capacite",
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
        1000,
        var_id_date,
        'CALOGERO',
        'Retrouvez Calogero en concert pour une soirée de variété et chanson française au Skope.',
        'Lannion',
        'Calogero, artiste majeur de la scène française, présente son répertoire lors d''un concert événement. Venez vibrer au son de ses plus grands titres et de ses nouvelles chansons dans l''ambiance intimiste du Skope.',
        'https://www.ticketmaster.fr/fr/manifestation/calogero-billet/idmanif/615826',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_CALOGERO;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('n_calogero_g.webp');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_CALOGERO, 'n_calogero_g.webp');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_CALOGERO, 'Festif');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('À partir de', 39, var_id_offre_CALOGERO);

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-10'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_CALOGERO,
        var_id_date
    );



    /* ##################################################################### */
    /*                         Labyrinthe de Malido                          */
    /* ##################################################################### */

    INSERT INTO sae._adresse (
        "num_et_nom_de_voie",
        "complement_adresse",
        "code_postal",
        "ville",
        "pays"
    )
    VALUES (
        'Malido',
        NULL,
        '22400',
        'Saint-Alban',
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
        'Sebastien',
        'LEGRAND',
        'contact@malido.fr',
        '+33664144287',
        '$2y$10$SbQvvySpoZnHYdiVcIeoKulh.VCDsnpzSZRQZnkcg.KEHjxyvyLAe', -- 'Mot de passe'
        'Ferme de Malido',
        'Culture et élevage associés',
        'https://www.malido.fr/',
        var_id_adresse,
        '83327330300010'
    )
    RETURNING "id_compte" INTO var_id_compte;

    INSERT INTO sae.offre_activite (
        "duree", 
        "age_min",
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
        3,
        'Labyrinthe de Malido',
        'Au cœur des Côtes d’Armor, profitez de vos vacances ou de vos week-ends sur la côte d’Émeraude pour découvrir un endroit unique et y vivre un moment agréable entre amis ou en famille.',
        'Saint-Alban',
        '" À droite ou à gauche, zut... un cul de sac ! La sortie, on verra plus tard. Pour l''instant une seule idée : pénétrer dans ce champ de maïs et voir de ses propres yeux ce qui s''y passe". Parsemé d''embûches, d''impasses et autres subterfuges, le labyrinthe de la Ferme de Malido à Saint-Alban dans les Côtes d’Armor vous emmène dans de nouvelles péripéties : cette année, cap sur la Coupe du monde de football. Profitez d''un passage en Bretagne pour venir au Labyrinthe de la Ferme de Malido à Saint-Alban et y vivre un moment agréable entre amis ou en famille. En plus du Labyrinthe, vous pouvez également apprécier un parcours d’adresse ainsi qu’un parc de structures gonflables.',
        'https://www.malido.fr/',
        var_id_compte,
        var_id_adresse,
        'standard',
        NULL,
        NULL,
        NULL,
        NULL
    )
    RETURNING "id_offre" INTO var_id_offre_labyrinthe_malido;

    INSERT INTO sae._image
    (
        "lien_fichier"
    )
    VALUES
    ('banner.jpg'),
    ('slider1.jpg'),
    ('slider2.jpg'),
    ('slider3.jpg');

    INSERT INTO sae._offre_contient_image (
        "id_offre",
        "id_image"
    )
    VALUES
    (var_id_offre_labyrinthe_malido, 'banner.jpg'),
    (var_id_offre_labyrinthe_malido, 'slider1.jpg'),
    (var_id_offre_labyrinthe_malido, 'slider2.jpg'),
    (var_id_offre_labyrinthe_malido, 'slider3.jpg');

    INSERT INTO sae._offre_possede_tag (
        "id_offre",
        "nom_tag"
    )
    VALUES
    (var_id_offre_labyrinthe_malido, 'Sport'),
    (var_id_offre_labyrinthe_malido, 'Découverte'),
    (var_id_offre_labyrinthe_malido, 'Artisanat'),
    (var_id_offre_labyrinthe_malido, 'Nature'),
    (var_id_offre_labyrinthe_malido, 'Famille'),
    (var_id_offre_labyrinthe_malido, 'Groupe');

    INSERT INTO sae._tarif_publique (
        "nom_tarif",
        "prix",
        "id_offre"
    )
    VALUES
    ('Adultes', 9, var_id_offre_labyrinthe_malido),
    ('Enfants', 8, var_id_offre_labyrinthe_malido),
    ('Gratuit en dessous de 4 ans', 0, var_id_offre_labyrinthe_malido),
    ('Prix de groupes (15 minimum) : Adultes', 8, var_id_offre_labyrinthe_malido),
    ('Prix de groupes (15 minimum) : Enfants', 7, var_id_offre_labyrinthe_malido);

    INSERT INTO sae._prestation ("nom_prestation", "description")
    VALUES (
        'Labyrinthe',
        '" À droite ou à gauche, zut... un cul de sac ! La sortie, on verra plus tard. Pour l''instant une seule idée : pénétrer dans ce champ de maïs et voir de ses propres yeux ce qui s''y passe". Parsemé d''embûches, d''impasses et autres subterfuges, le labyrinthe de la Ferme de Malido à Saint-Alban dans les Côtes d’Armor vous emmène dans de nouvelles péripéties : cette année, cap sur la Coupe du monde de football. Profitez d''un passage en Bretagne pour venir au Labyrinthe de la Ferme de Malido à Saint-Alban et y vivre un moment agréable entre amis ou en famille. En plus du Labyrinthe, vous pouvez également apprécier un parcours d’adresse ainsi qu’un parc de structures gonflables.'
    )
    RETURNING "nom_prestation" INTO var_nom_prestation;

    INSERT INTO sae._offre_activite_propose_prestation (
        "nom_prestation",
        "id_offre_activite"
    )
    VALUES (
        var_nom_prestation,
        var_id_offre_labyrinthe_malido
    );

    INSERT INTO _date
    (
        "date"
    )
    VALUES (
        '2025-01-11'
    )
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO _offre_dates_mise_en_ligne
    (
        "id_offre",
        "id_date"
    )
    VALUES (
        var_id_offre_labyrinthe_malido,
        var_id_date
    );






    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Durand',
        'Alex',
        'alex.d42@email.com',
        '+33645781234',
        '$2y$10$ezFow.R8vj0D717cODruuOuJfzhtWx8U3Zgt2iizrjlsNneBrgnrm', -- P@ssword123
        'AlexD42'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-25')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-20')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_armoripark,
        4,
        'Super journée en famille !',
        'Nous avons passé une superbe journée à Armoripark avec nos enfants ! Ils ont adoré les structures gonflables et la luge d’été. La tyrolienne est impressionnante, mais bien sécurisée. Seul petit bémol : un peu d’attente sur certaines attractions en pleine saison.',
        'famille',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-05')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-02')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_aquarev,
        5,
        'Un superbe espace vert',
        'Un vrai bol d’air frais ! J’ai adoré l’ambiance du parc, notamment la bambouseraie et les pontons de pêche. C’est un lieu parfait pour une balade tranquille, et en plus, c’est gratuit. Vraiment un bel atout pour la région.',
        'affaires',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-10')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-08')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        5,
        'Une visite captivante et bien documentée',
        'Un musée qui vaut vraiment le détour. Les témoignages des résistants et les explications historiques sont très bien présentés. J’ai particulièrement apprécié le film projeté, qui apporte un éclairage poignant sur cette période. Bravo aux organisateurs pour ce travail de mémoire.',
        'amis',
        var_id_date,
        var_id_date2
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Lemoine',
        'Marie',
        'marie.lgn@email.com',
        '+33723569810',
        '$2y$10$SaQApdiFBqhSe8JaCmnSq./vdHhiK9UvNRAXgMJ4n05S8myD8JXOK', -- M4rie!Lgn
        'Marie_Lgn'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-10')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-08')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_aquarev,
        5,
        'Idéal pour une balade en famille !',
        'Un lieu magnifique pour une sortie avec les enfants. Ils ont adoré la plaine de jeux et le labyrinthe ! L’endroit est propre, spacieux et accessible à tous. Et en plus, c’est gratuit ! Je recommande vivement.',
        'famille',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-05')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-01')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        5,
        'Un incontournable pour les amateurs de fruits de mer',
        'Tout était parfait ! Les plateaux de fruits de mer sont généreux et d’une qualité irréprochable. Le personnel est très accueillant et l’ambiance maritime rend l’expérience encore plus plaisante. Une adresse que je recommande à 100 % !',
        'famille',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Paulin',
        'Jean',
        'jpaul75@email.com',
        '+33698743215',
        '$2y$10$r2gIggXMjklQHIc8Xo8Gl.DIlc9WhmJM5b//3oKEyXvkujnYwtxMi', -- Jp@ul_1975
        'JPaul_75'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-18')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-15')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_armoripark,
        3,
        'Sympa mais peut mieux faire',
        'L’endroit est agréable, mais certaines attractions mériteraient un peu de rénovation. J’ai trouvé le tarif correct pour la diversité des activités proposées, mais il pourrait y avoir plus de zones ombragées pour les journées très chaudes.',
        'solo',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-28')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-25')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_labyrinthe_malido,
        4,
        'Une activité originale et fun',
        'Une belle découverte ! Le labyrinthe est bien conçu, avec des passages vraiment déroutants. On s’est pris au jeu et on a mis plus de temps que prévu à sortir 😅. L’ambiance est bonne, et le parcours d’adresse est un petit plus sympa à la fin. À faire au moins une fois !',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-28')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-25')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        4,
        'Un musée instructif et bien documenté',
        'Très bonne visite ! Les espaces d’exposition sont bien agencés et les explications sont claires. On sent que l’histoire du lieu est mise en valeur avec soin. Petit bémol : j’aurais aimé plus d’objets d’époque exposés.',
        'affaires',
        var_id_date,
        var_id_date2
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        NULL,
        NULL,
        'gamerx@email.com',
        NULL,
        '$2y$10$OgS0DP2jh9hh0aeHw7AWduVOIcHU2Yy7fhjOcs771/Ij4hMdkE/LW', -- Xg@mer2024
        'GamerX'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-01')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-29')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        5,
        'Très immersif et bien expliqué',
        'J’ai été vraiment pris par l’histoire racontée dans ce musée. La scénographie est bien pensée et les documents exposés sont fascinants. On ressort avec une meilleure compréhension de cette période et un profond respect pour ceux qui ont combattu. Un must pour les passionnés d’histoire.',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-01')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-30')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        5,
        'Excellent restaurant, je reviendrai !',
        'Une vraie pépite ! J’ai goûté un plateau de fruits de mer et c’était un régal. Les huîtres étaient ultra fraîches et le poisson cuit à la perfection. Le personnel était super accueillant et l’ambiance très agréable. Hâte d’y retourner !',
        'amis',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Martin',
        'Clara',
        'clara.moon@email.com',
        '+33677123456',
        '$2y$10$nnBrhiWyQzKDdgDseDcm1ueAlj9AtfJI6GDAUd53TPeACEyWE5udG', -- MoonCl@r4
        'Clara_Moon'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-10')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-07')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_armoripark,
        5,
        'Génial pour une sortie entre amis !',
        'On a passé un après-midi génial avec des amis ! Le pentogliss est super fun, et la tyrolienne est impressionnante ! Le cadre est sympa, et le mini-golf a bien conclu la journée. Vraiment un endroit à recommander pour une sortie détente et fun.',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-01')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-30')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_labyrinthe_malido,
        5,
        'Super expérience en famille !',
        'Nous avons passé un excellent moment dans le labyrinthe ! Les enfants se sont bien amusés à essayer de trouver la sortie, et les petites énigmes sur la Coupe du Monde étaient une touche sympa. En plus, les structures gonflables à la fin ont fait leur bonheur ! Un très bon rapport qualité/prix.',
        'famille',
        var_id_date,
        var_id_date2
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Robert',
        'Kévin',
        'kevin.rox@email.com',
        NULL,
        '$2y$10$6FZrrLizgd/eHAz6zCaAXu7OeftTTMu0wa4LqRfB2XmffhFCb7HiO', -- RoxKev!n99
        'Kevin_Rox'
    ) RETURNING "id_compte" INTO var_id_compte;


   INSERT INTO sae._date ("date")
    VALUES ('2025-03-15')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-12')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_aquarev,
        4,
        'Belle découverte !',
        'Le parc est vraiment agréable et bien entretenu. On a apprécié le parcours sportif et le terrain multi-sport. Seul petit regret, j’aurais aimé plus d’animations en dehors des mercredis d’été, mais sinon, c’est top !',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-10')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-08')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_labyrinthe_malido,
        5,
        'Génial, on a adoré !',
        'Une activité originale qui change des parcs classiques. Le thème de la Coupe du Monde ajoute une touche fun, et les petits jeux à l’intérieur du labyrinthe rendent le parcours encore plus intéressant. On a bien rigolé entre amis, et les structures gonflables à la fin étaient la cerise sur le gâteau !',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-28')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-25')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        4,
        'Très bon mais un peu cher',
        'Le cadre est idyllique et l’ambiance agréable. Les plats sont très bons, surtout le poisson qui était parfaitement cuit. Par contre, les prix sont un peu élevés pour les portions. Le service était correct, même si un peu lent en heure de pointe.',
        'amis',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        3,
        var_id_compte,
        var_id_offre_coste_mor
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Chevalier',
        'Sophie',
        'sophie.c@email.com',
        '+33788991122',
        '$2y$10$mQ/IBSvjLHSlv8QI1QNyTunGGhCwdj2x1xUcw5A4BUJgbIapLDGtC', -- SophC1234
        'SophieC'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-02-28')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-02-25')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_armoripark,
        5,
        'Parfait pour les petits et les grands !',
        'Nous avons emmené nos enfants (5 et 8 ans), et ils se sont éclatés ! L’espace est sécurisé, bien pensé, et les activités sont variées. Coup de cœur pour la volière et les animaux de la ferme qui apportent une belle touche nature au parc.',
        'famille',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-20')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-18')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_labyrinthe_malido,
        3,
        'Bien mais un peu court',
        'Le concept est chouette et l’ambiance familiale est agréable, mais j’ai trouvé que l’activité était un peu courte. On est sortis assez rapidement, et j’aurais aimé plus de défis ou d’interactions dans le labyrinthe. Malgré ça, ça reste une sortie sympa, surtout pour les enfants.',
        'couple',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-20')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-18')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        5,
        'Un devoir de mémoire essentiel',
        'Ce musée est un lieu de mémoire très important. Les récits des résistants et les documents d’époque sont bouleversants. J’y suis allée avec mes enfants et ils ont été captivés. Une visite à faire absolument pour ne pas oublier.',
        'famille',
        var_id_date,
        var_id_date2
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        NULL,
        NULL,
        'darkwolf89@email.com',
        NULL,
        '$2y$10$mb8cpiaX1dIF3ItpuUD0TOK15SqW5Jaev9KYvNnhZTqu2OK.33G1O', -- DWolfP@ss
        'DarkWolf89'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-05')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-01')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_armoripark,
        2,
        'Déçu par l’entretien du parc',
        'Honnêtement, je m’attendais à mieux. Certaines attractions étaient fermées sans explication, et l’attente était longue pour celles ouvertes. Dommage, car l’endroit a du potentiel, mais il faudrait vraiment améliorer l’organisation.',
        'couple',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-15')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-12')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        3,
        'Bon repas, mais service à revoir',
        'La cuisine est bonne, mais l’attente était interminable. Nous avons attendu presque 30 minutes pour être servis, et le personnel semblait débordé. Dommage, car le cadre est magnifique et les plats bien préparés.',
        'solo',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        2,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        3,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        3,
        var_id_compte,
        var_id_offre_coste_mor
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        'Bernard',
        'Elise',
        'elise.b@email.com',
        '+33611223344',
        '$2y$10$NHkJYGAnJLNwvHAq9PgpS.jiK7ovVRRDcGLBvTY9tsVIgyo5626k2', -- Eli$e2023
        'EliseB'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-22')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-20')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_aquarev,
        5,
        'Un endroit parfait pour se ressourcer',
        'J’adore cet endroit ! C’est calme, bien aménagé et parfait pour une promenade en pleine nature. La bambouseraie est superbe et les aires de pique-nique sont bien pensées. C’est un vrai plaisir d’y passer du temps en famille ou même seule pour se détendre.',
        'solo',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-01')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-30')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        5,
        'Un musée passionnant et émouvant',
        'Une visite riche en émotions et en enseignements. L’exposition est bien conçue et permet de mieux comprendre le rôle du maquis de Plésidy pendant la Seconde Guerre mondiale. Mention spéciale à la salle de projection qui apporte un vrai plus à l’expérience. À voir absolument !',
        'solo',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-02')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-30')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        5,
        'Un régal face à la mer !',
        'Un superbe restaurant avec une vue magnifique sur la plage et le château de Costaérès. Les fruits de mer étaient d’une fraîcheur exceptionnelle et les moules-frites absolument délicieuses. Service aux petits soins, ambiance chaleureuse… une très belle découverte !',
        'couple',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    );



    INSERT INTO sae.compte_membre (
        "nom_compte",
        "prenom",
        "email",
        "tel",
        "mot_de_passe",
        "pseudo"
    )
    VALUES 
    (
        NULL,
        'Maxime',
        'max.power@email.com',
        '+33666778899',
        '$2y$10$WkCo858k9ANxae.TrQV12.PS6WZFZOgbbK78qAT7shxWD.wOYQL1C', -- M@x!mumP0wer
        'Max_Power'
    ) RETURNING "id_compte" INTO var_id_compte;


    INSERT INTO sae._date ("date")
    VALUES ('2025-03-04')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-31')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_musee_resistance_argoat,
        4,
        'Un lieu chargé d’histoire',
        'Un musée très intéressant qui permet de mieux comprendre la Résistance locale. Les documents d’archives et les témoignages sont bien mis en valeur. J’aurais aimé un peu plus d’animations interactives, mais l’ensemble est très bien fait. Un bel hommage aux résistants.',
        'amis',
        var_id_date,
        var_id_date2
    );


    INSERT INTO sae._date ("date")
    VALUES ('2025-04-01')
    RETURNING "id_date" INTO var_id_date;

    INSERT INTO sae._date ("date")
    VALUES ('2025-03-29')
    RETURNING "id_date" INTO var_id_date2;

    INSERT INTO sae._avis (
        "id_membre",
        "id_offre",
        "note",
        "titre",
        "commentaire",
        "contexte_visite",
        "publie_le",
        "visite_le"
    )
    VALUES (
        var_id_compte,
        var_id_offre_coste_mor,
        4,
        'Très bon restaurant avec une vue incroyable',
        'J’ai adoré la localisation du restaurant, juste en face du château de Costaérès. Les fruits de mer étaient frais et bien préparés. Mention spéciale au service, très professionnel et sympathique. Une belle adresse pour un repas en bord de mer !',
        'affaires',
        var_id_date,
        var_id_date2
    );

    INSERT INTO _note_detaillee
    (
        "nom_note",
        "note",
        "id_membre",
        "id_offre"
    )
    VALUES (
        'Cuisine',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Service',
        5,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Ambiance',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    ), (
        'Rapport qualité prix',
        4,
        var_id_compte,
        var_id_offre_coste_mor
    );


END $$;

COMMIT;
-- ROLLBACK;

