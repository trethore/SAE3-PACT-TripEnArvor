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

    INSERT INTO sae._abonnement (nom_abonnement) 
    VALUES
    ('gratuit'),
    ('standard'),
    ('premium');


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

END $$;



COMMIT;
-- ROLLBACK;

