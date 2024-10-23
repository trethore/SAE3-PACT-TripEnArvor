<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/styleguide.css"/>
    <link rel="stylesheet" href="/style/styleHFB.css"/>
    <link rel="stylesheet" href="/style/style-details-offre-pro.css"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>

<body>

    <header id="header">
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT Pro</div>
        <div class="search-box">
        <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
        <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="index.html"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <div class="display-ligne-espace bouton-modifier">
        <p class="transparent">.</p> 
        <div>
            <div id="confirm">
                <p>Voulez-vous mettre votre offre hors ligne ?</p>
                <div class="close">
                    <button onclick="closeConfirmConfirmer()">Mettre hors ligne</button>
                    <button onclick="closeConfirmAnnuler()">Annuler</button>
                </div>
            </div>
            <button id="bouton1" onclick="showConfirm()">Mettre hors ligne</button>
            <button id="bouton2">Modifier l'offre</button>
        </div>
    </div>

    <main id="body">
        <section class="fond-blocs"><!---->

            <h1>Côté Plage</h1>
            <div class="galerie-images-presentation"> 
                <img src="/images/universel/photos/hotel_2.png" alt="Image 1">
                <img src="/images/universel/photos/hotel_2_2.png" alt="Image 2">
                <img src="/images/universel/photos/hotel_2_3.png" alt="Image 3">
                <img src="/images/universel/photos/hotel_2_4.png" alt="Image 4">
                <img src="/images/universel/photos/hotel_2_5.png" alt="Image 5">
            </div>

            <div class="display-ligne-espace">
                <p><em>Restaurant, Ouvert</em></p>
                <p>1 Rue Koz Ker, 56370 SARZEAU</p>
            </div>
                
            <div class="display-ligne">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <p>(49)</p>
                <a href="#avis">Voir les avis</a>
            </div>

            <div class="display-ligne-espace">
                <p>Proposée par : David Guillet</p>
                <button>Voir les tarifs</button>
            </div>

        </section>

        <section class="double-blocs"><!---->

            <div class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <li><img src="/images/universel/icones/cuisine.png"><h2>Cuisine traditionnelle</h2></li>
                    <li><img src="/images/universel/icones/mer.png"><h2>Vue sur mer</h2></li>
                    <li><img src="/images/universel/icones/coeur.png"><h2>Service attentionné</h2></li>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <h2>À propos de Côté Plage :</h2>
                <p>Repris en 2010, le restaurant propose une cuisine traditionnelle axée sur les poissons et fruits de mer, mettant en avant des produits locaux et de saison. Depuis sa reprise, David Guillet a renouvelé l'équipe et introduit de nouvelles initiatives, telles qu'une terrasse lounge pour les apéritifs, dans une ambiance conviviale et les pieds dans l’eau​</p>
                <a href="https://les-embruns-du-phare.fr"><img src="/images/universel/icones/lien.png" alt="epingle" class="epingle">restaurant-cote-plage.fr</a>
                <p>Numéro : 04 94 66 97 64</p>
            </div>
    
        </section>

        <section class="fond-blocs"><!---->

            <h2>Description détaillée de l'offre :</h2>
            <p>Les pieds dans l’eau sur la plage du Roaliguen. Nous proposons une cuisine de caractère, inventive à base de produits locaux et de saison. Tout cela dans une ambiance conviviale.</p>
            <p>Nouveau : Terrasse Lounge pour vos moments apéritif.</p>
            <p>Ouvert d'avril à novembre.<br>Fermé le mercredi en juillet-août.<br>Fermé le mercredi et le jeudi hors saison.</p>

        </section>

        <section class="double-blocs"><!---->

            <div class="fond-blocs bloc-tarif">
                <div>
                    <h2>Tarifs :</h2>
                    <table>
                        <tr>
                            <td>Dunburi de Poulet 22 €</td>
                            <td>Burger de Boeuf 24,50 €</td>
                        </tr>
                        <tr>
                            <td>Menu Petit Plagiste 11,80 €</td>
                            <td>Veggie Bowl 18 €</td>
                        </tr>
                    </table>
                </div>

                <button>Accéder à la carte complète</button>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture :</h2>
                <ul>
                    <li><em>Lundi : 09h - 23h</em></li>
                    <li><em>Mardi : 09h - 23h</em></li>
                    <li><em>Mercredi : 09h - 23h</em></li>
                    <li><em>Jeudi : 09h - 23h</em></li>
                    <li><em>Vendredi : 09h - 23h</em></li>
                    <li><em>Samedi : 09h - 23h</em></li>
                    <li><em>Dimanche : 09h - 20h</em></li>
                </ul>
            </div> 
    
        </section>

        <section class="fond-blocs">

            <h1>Localisation</h1>
            <div id="map" class="carte"></div>

        </section>

        <section class="fond-blocs avis">

            <div class="display-ligne">
                <h2>Note moyenne :</h2>
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <p>49 avis</p>
            </div>

            <div class="fond-blocs-avis">

                <div class="display-ligne-espace">

                    <div class="display-ligne">
                        <img src="/images/universel/icones/avatar-homme-1.png" class="avatar">
                        <p><strong>Stanislas</strong></p>
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <p><em>14/08/2023</em></p>
                    </div>

                    <p><strong>⁝</strong></p>
                </div>

                <p>Restaurant très bon avec des ingrédients de qualité</p>

                <div class="display-ligne-espace">
                    <p class="transparent">.</p>
                    <div class="display-notation">
                        <a href="#"><strong>Répondre</strong></a>
                        <p>0</p><img src="/images/universel/icones/pouce-up.png" class="pouce">
                        <p>0</p><img src="/images/universel/icones/pouce-down.png" class="pouce">
                    </div>
                </div>

            </div>        

        </section>        
         
        <div class="navigation display-ligne-espace">
            <button>Retour à la liste des offres</button>
            <button><img src="/images/universel/icones/fleche-haut.png"></button>
        </div>

    </main>

    <footer id="footer">
        <div class="footer-top">
        <div class="footer-top-left">
            <span class="footer-subtitle">P.A.C.T</span>
            <span class="footer-title">TripEnArmor</span>
        </div>
        <div class="footer-top-right">
            <span class="footer-connect">Restons connectés !</span>
            <div class="social-icons">
            <a href="https://x.com/?locale=fr">
                <div class="social-icon" style="background-image: url('/images/universel/icones/x.png');"></div>
            </a>
            <a href="https://www.facebook.com/?locale=fr_FR">
                <div class="social-icon" style="background-image: url('/images/universel/icones/facebook.png');"></div>
            </a>
            <a href="https://www.youtube.com/">
                <div class="social-icon" style="background-image: url('/images/universel/icones/youtube.png');"></div>
            </a>
            <a href="https://www.instagram.com/">
                <div class="social-icon" style="background-image: url('/images/universel/icones/instagram.png');"></div>
            </a>
            </div>
        </div>


        <!-- Barre en bas du footer incluse ici -->

        </div>
        <div class="footer-bottom">
        Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        Conditions générales - ©
        Redden’s, Inc.
        </div>
        
    </footer>

    <script>

        let map = L.map('map').setView([47.497745757735, -2.772722737126], 13); 
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        L.marker([47.497745757735, -2.772722737126]).addTo(map)
            .bindPopup('Côté Plage<br>Sarzeau')
            .openPopup();

    </script>

    <script>

        let confirmDiv = document.getElementById("confirm");

        function showConfirm() {
            confirmDiv.style.display = "block";
            let header = document.getElementById('header');
            header.style.filter = "blur(10px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(10px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(10px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(10px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(10px)";
            let popup = document.getElementById('confirm');
            popup.style.filter = "none";
        }

        function showFinal() {
            confirmDiv.style.display = "block";
        }

        function closeConfirmAnnuler() {
            confirmDiv.style.display = "none";
            let header = document.getElementById('header');
            header.style.filter = "blur(0px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(0px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(0px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(0px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(0px)";
        }

        function closeConfirmConfirmer() {
            confirmDiv.style.display = "none";
            let header = document.getElementById('header');
            header.style.filter = "blur(0px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(0px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(0px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(0px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(0px)";
            alert("Offre hors ligne");
        }

        function 

    </script>

</body>

</html>