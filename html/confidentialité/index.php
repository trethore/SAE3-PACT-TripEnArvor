<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
    <script src="/scripts/header.js"></script>
</head>

<body class="front">
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>

        </div>
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <h1>Politique de confidentialité</h1>
        <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Incidunt odio consequatur, laudantium neque nihil similique. Ducimus dolore, autem, blanditiis ex libero porro rem officiis minima laboriosam totam, eos accusamus tempore.
        </p>
    </main>
    <footer>
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
        <a href="/confidentialité/" target="_blank">Politique de confidentialité</a> - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        <a href="/cgu/" target="_blank">Conditions générales</a> - ©
        Redden's, Inc.
        </div>
    </footer>
</body>

</html>