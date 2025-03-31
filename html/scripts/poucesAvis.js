$(document).ready(function () {
    $('.pouce').click(function () {
        let $this = $(this);
        let idOffre = $this.data('id-offre');
        let idMembreAvis = $this.data('id-membre-avis');
        let idMembreReaction = $this.data('id-membre-reaction');
        let type = $this.hasClass('pouceHaut') ? 'like' : 'dislike';
        let action = $this.hasClass('active') ? 'remove' : 'add';

        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: {
                id_offre: idOffre,
                id_membre_avis: idMembreAvis,
                id_membre_reaction: idMembreReaction,
                type: type,
                action: action
            },
            success: function () {
                let $nbPouceHaut = $this.siblings('.nbPouceHaut');
                let $nbPouceBas = $this.siblings('.nbPouceBas');
                let $opposite = type === 'like' ? $this.siblings('.pouceBas') : $this.siblings('.pouceHaut');

                if (type === 'like') {
                    if (action === 'add') {
                        $nbPouceHaut.text(parseInt($nbPouceHaut.text()) + 1);
                        if ($opposite.hasClass('active')) {
                            $nbPouceBas.text(parseInt($nbPouceBas.text()) - 1);
                        }
                    } else {
                        $nbPouceHaut.text(parseInt($nbPouceHaut.text()) - 1);
                    }
                } else {
                    if (action === 'add') {
                        $nbPouceBas.text(parseInt($nbPouceBas.text()) + 1);
                        if ($opposite.hasClass('active')) {
                            $nbPouceHaut.text(parseInt($nbPouceHaut.text()) - 1);
                        }
                    } else {
                        $nbPouceBas.text(parseInt($nbPouceBas.text()) - 1);
                    }
                }

                $this.toggleClass('active');
                $opposite.removeClass('active');
            },
            error: function () {
                alert("Une erreur s'est produite, veuillez réessayer.");
            }
        });
    });

    $('.repondre-btn').click(function (event) {
        event.preventDefault();
        let identifiant = $(this).data('id');
        $(`#reponse-form-${identifiant}`).show();
        $(this).hide();
    });

    $('.annuler-reponse').click(function () {
        let identifiant = $(this).data('id');
        $(`#reponse-form-${identifiant}`).hide();
        $(`.repondre-btn[data-id='${identifiant}']`).show();
    });

    $('.valider-reponse').click(function (event) {
        event.preventDefault();
        let identifiant = $(this).data('id');
        let idOffre = $(this).data('id-offre');
        let idMembre = $(this).data('id-membre');
        let texteReponse = $(`#texte-reponse-${identifiant}`).val().trim();
        
        if (texteReponse === "") {
            alert("Veuillez entrer une réponse avant d'envoyer.");
            return;
        }
        
        $.ajax({
            url: "/utils/reponse.php",
            type: "POST",
            data: {
                id_offre: idOffre,
                id_membre: idMembre,
                reponse: texteReponse
            },
            success: function (data) {
                console.log("Réponse envoyée :", data);
                location.reload();
            },
            error: function (error) {
                console.error("Erreur :", error);
            }
        });
    });
});
