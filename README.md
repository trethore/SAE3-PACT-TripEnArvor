# SAE3-PACT-TripEnArvor
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 1
SAÉ 3 & 4
Sujet
L’association TripEnArvor vient de confier le développement de sa plateforme à votre entreprise. Afin
d’assurer son indépendance, il s’agit d’une plateforme que TripEnArvor souhaite réaliser de toutes
pièces. Il ne s’agit donc pas d’installer, ni d’intégrer ou de vous appuyer sur une plateforme existante,
et toute utilisation d’outils ou de librairies externes devra faire l’objet d’une validation par vos
encadrants.
Ce document provenant de l’association, son contenu n’est pas ou peu technique. Il conviendra donc de
savoir lire entre les lignes ce qu’elle attend. Savoir comprendre ce que le client veut, savoir le guider et
lui suggérer des fonctionnalités, voire le mettre en garde sur des besoins ou des souhaits dont il ne
maîtrise pas les implications, tout cela fait partie du travail attendu et de votre rôle en tant que
concepteur·rice·s et développeur·euse·s.
Vous devrez donc commencer par analyser le besoin, trier les idées et produire les User Stories avant de
passer aux étapes de conception et de réalisation du logiciel. Toutes les équipes doivent analyser
l’intégralité du besoin et produire les User Stories de l’ensemble du logiciel. Par la suite, vos clients
vous guideront sur la priorisation des fonctionnalités à concevoir et développer. Les priorités varieront
d’une équipe à l’autre, en fonction entre autres de votre parcours BUT2 (A ou C).
L’association TripEnArvor et son projet PACT
Association à but non lucratif Loi 1901, TripEnArvor a pour objectif de promouvoir le
territoire Costarmoricain : activités, parcs d’attractions, visites, spectacles et restaurants.
Elle bénéficie ainsi d’un financement de la Région Bretagne et du Conseil Général des
Côtes d’Armor, et répond à des problématiques de valorisation du patrimoine culturel
et social du département.
L’enjeu majeur de l’association TripEnArvor pour 2025 est le développement et la mise en service de
sa Plateforme d’Avis et Conseils Touristiques (PACT), devant contribuer au renforcement du lien entre
les professionnels du tourisme (établissements privés, associations, secteur public) et la population
(locale et touristique).
A l’instar de nombreuses plateformes de promotion d’activités touristiques, la PACT est un site web de
type Front Office / Back Office (FO / BO). Le Front Office permet aux Visiteurs (Membres de la PACT ou
internautes non authentifiés) de rechercher des Offres touristiques et consulter les Avis des Membres.
Quant au Back Office, il permet aux Professionnels de faire la promotion de leurs Offres et interagir
avec les Membres déposant leur Avis. Le Back Office est un espace du site bien distinct1 du Front Office :
l’identité visuelle du Back Office est clairement différente de celle du Front Office.
L’usage de la plateforme est gratuit pour les Visiteurs (Membres ou non authentifiés). Concernant les
Professionnels, deux scénarios sont possibles :
• L’Offre est soumise par un Professionnel du secteur public ou associatif. On parle alors d’une Offre
Gratuite, dont la promotion sur la plateforme n’est pas facturée au Professionnel.
• L’Offre est soumise par un Professionnel du secteur privé. On parle dans ce cas d’une Offre Payante
(Standard ou Premium), dont la promotion sur la plateforme est facturée mensuellement au
Professionnel. Pour ce type d’Offre, le site propose d’autres options (également payantes), venant
s’ajouter au montant mensuel prélevé. Ces prélèvements doivent contribuer à l’auto-financement
de la plateforme : hébergement, supervision et maintenance.
1 Cette « distinction » est seulement visuelle : l’accès et l’hébergement FO/BO se font sur le même serveur.
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 2
Le Front Office de la PACT
Depuis la page d’accueil du site, un visiteur peut consulter les Offres à disposition :
• Offres promues « À la Une » (sélection aléatoire et temporaire de quelques Offres ayant souscrit
une option spécifique) ;
• Nouveautés (Offres publiées au cours des deux dernières semaines2
, de la plus récente à la plus
ancienne) ;
• Offres consultées récemment (de la consultation la plus récente à la plus ancienne) ;
• En recherchant des Offres par mot clé, lieu, catégorie.
Les Offres sont affichées sous forme de « cartes » : titre, résumé, photo principale, ville, prix minimal
ou gamme de prix (pour la Restauration), note, nombre d’avis, catégorie, dénomination du
Professionnel ayant déposé l’Offre, etc.
Quel que soit son parcours, le visiteur peut filtrer les Offres affichées :
• Par catégorie : Restauration, Spectacles, Visites, Activités, Parcs d’attractions ;
• Par lieu : commune, lieu-dit ;
• Sur une période donnée en fonction de la date d’évènement (s’il s’agit d’un spectacle ou d’une visite)
ou des dates d’ouverture (s’il s’agit d’une attraction, d’activités, de boutiques ou de restaurants) ;
• Par notion de « ouvert » / « fermé » (prévoir aussi un affichage d’un « ferme bientôt » quand l’heure
de fin approche) ;
• Par intervalle de prix minimal (ou par gamme de prix pour la Restauration);
• Par fourchette sur la note générale des Avis (de 1 à 5).
Le visiteur peut également trier les Offres affichées :
• Par prix croissant/décroissant (les prix pouvant être variables pour une même Offre, on se base sur
le prix minimal indiqué dans la grille tarifaire par le Professionnel, ou sur la gamme de prix pour la
Restauration) ;
• Par note générale des Avis.
L’usage du Front Office étant avant tout en situation de mobilité, ce dernier doit cibler prioritairement
un affichage sur smartphone ou tablette. Il est d’ailleurs envisagé à moyen terme d’enrichir la recherche
d’Offres avec visualisation et sélection par carte interactive, avec des critères de filtre propres à la
géolocalisation rendue possible avec les smartphones : « autour de moi », dans un rayon donné, etc.
Lorsqu’il choisit une Offre, le visiteur peut :
• Consulter toutes les informations de l’Offre : catégorie, nom du Professionnel proposant l’Offre,
titre, résumé, description détaillée, photos mises en avant par le Professionnel, coordonnées
téléphoniques, adresse postale, site web, grille tarifaire, périodes d’ouverture, etc.
• Consulter les Avis émis par les Membres de la PACT : commentaires, notes, photos, réponses du
Professionnel, etc.
• Visualiser le lieu exact de l’Offre et pouvoir être guidé avec son smartphone. L’accès à un moyen de
guidage (carte) doit être bien visible et aisé.
2 Ce temps doit être « paramétrable » pour pouvoir réaliser des démos probantes.
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 3
Le Back Office de la PACT
Pour administrer ses Offres, un utilisateur doit être authentifié avec son compte Professionnel sur le
Back Office. Cela étant fait, il peut également y gérer les données de son compte et modérer les Avis
déposés par les Membres à propos de ses Offres.
À partir de la liste de ses Offres, le Professionnel peut :
• Créer une nouvelle Offre ;
• Consulter le détail d’une Offre ;
• Modifier une Offre (ses informations, ses options, mais pas son type) ;
• Mettre une Offre hors ligne.
L’aspect visuel de la liste des Offres est similaire à celui du Front Office (fonctionnalités de tri et filtres
compris), à ceci près :
• Une signalétique particulière indique le type de l’Offre : Offre Gratuite, Standard, Premium ;
• Des signalétiques particulières indiquent pour une Offre le nombre d’Avis :
– Non encore consultés par le Professionnel ;
– Auxquels il n’a pas répondu (hors Avis non consultés) ;
– « Blacklistés ».
Ces signalétiques doivent lui permettre un accès direct aux Avis spécifiques à l’Offre.
• Des informations complémentaires apparaissent : date de dernière mise à jour de l’Offre, nature de
l’option payante souscrite (et ses dates de début et de fin) ;
L’aspect visuel du détail d’une Offre doit être le même lors de la création et de la modification. Les
informations non visibles des Visiteurs doivent être clairement identifiables à l’écran. Le Professionnel
peut prévisualiser son Offre, lui permettant ainsi de la visualiser telle qu’affichée aux Visiteurs
(esthétique et le contenu).
Les Offres et leurs options
Pour créer une Offre, le Professionnel choisit un type d’Offre. Deux types d’Offres Payantes peuvent
être souscrites : l’Offre Standard et l’Offre Premium. L’Offre Premium, plus chère que l’Offre Standard,
donne à son Professionnel un « droit de veto » (blacklistage). Il peut en effet arriver que l’expérience
d’un Membre soit le reflet d’une situation exceptionnelle ou que ce dernier soit tenté de faire du
chantage au Professionnel pour obtenir une compensation (remise ou sur-classement). Ce type d’Offre
laisse la liberté au Professionnel de « blacklister » un maximum de 3 Avis pour l’Offre 3 . Les
conséquences de chaque choix (Offre Gratuite, Standard ou Premium) doivent être clairement
indiquées, car passée cette étape le type d’Offre ne peut être changé.
Le Professionnel saisit ensuite les détails de son Offre :
• Seules les informations apparaissant dans le résumé de l’Offre sont obligatoires ;
• Il peut saisir des informations complémentaires : description détaillée, adresse précise
« localisable » 4 (en plus de la commune qui est une information obligatoire), coordonnées
téléphoniques, site web, etc. ;
• Il peut télécharger une ou plusieurs photos ou illustrations et les visualiser instantanément.
3 Sur 12 mois glissants (un Avis blacklisté il y a plus de 12 mois n’est plus comptabilisé). 4 L’adresse renseignée doit être validée et les coordonnées GPS déduites par l’application.
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 4
Si l’Offre choisie est Payante, le professionnel peut en plus :
• Saisir une grille tarifaire (chaque tarif de la grille étant composé d’une dénomination et d’un prix).
La grille doit contenir a minima un tarif. Par ailleurs, le tarif le plus bas doit être mis en exergue car
c’est celui qui sera mis en avant à l’affichage de l’Offre sur le Front Office (résumé et filtres).
• Sélectionner une option payante de promotion de l’Offre en indiquant la semaine de lancement et
la durée :
– L’option « En Relief » met l’Offre en exergue lors de son affichage dans les listes ;
– L’option « À la Une », plus chère que la précédente, met l’Offre en avant sur la page d’accueil du
site (en plus de de la mettre en exergue tout comme l’option « En Relief »).
Le Professionnel peut ensuite mettre son Offre « en ligne ». Pour les Offres Payantes, le site demande
confirmation en rappelant : les conditions légales, le montant mensuel de l’Offre hors option, ainsi que
le montant prévisionnel qui sera facturé le premier mois (cf. modalités de Facturation). Si le
Professionnel n’a pas encore saisi ses informations bancaires, c’est à ce moment-là qu’il doit le faire.
Un Professionnel ne peut supprimer une Offre : pour que l’Offre n’apparaisse plus sur la PACT, il lui faut
demander sa mise « hors ligne ». L’Offre est alors retirée de la liste des Offres visibles sur le Front Office.
Elle reste visible côté Back Office Professionnel avec un statut « hors ligne », attendant sa remise en
ligne.
La remise « en ligne » étant un acte engageant financièrement, celle-ci doit se faire depuis l’interface
de modification de l’Offre (suivant un processus similaire à celui de la création d’une Offre).
Facturation
La facturation de chaque Professionnel est mensuelle et réalisée le 1er jour du mois suivant.
Chaque Offre est facturée du nombre de jours où elle a été « en ligne » sur le mois passé, en fonction
du type de l’Offre (Standard ou Premium) :
• Les jours avant sa création ne sont pas comptabilisés ;
• La facturation d’une Offre mise « hors ligne » est interrompue le jour suivant la mise « hors ligne » ;
• La facturation d’une Offre remise « en ligne » est faite à partir du jour de remise « en ligne ».
Attention cependant à ne pas facturer deux fois un même jour (si l’Offre a été mise « hors ligne »
puis remise « en ligne » le même jour).
Les options (« À la Une » et « En Relief ») sont souscrites pour une durée multiple d’une semaine (7
jours), sur un maximum de 4 semaines, et planifiées pour lancement le lundi d’une semaine donnée.
Une option est facturée intégralement dès lors qu’elle est activée (autrement dit si sa date de
lancement était au cours du mois passé).
• Tant que la date de lancement n’est pas atteinte, l’option peut être annulée ou modifiée sans
conséquence pour le Professionnel ;
• Passée la date de lancement, le Professionnel ne pourra modifier l’option. Il pourra l’annuler (avec
effet immédiat côté Front Office) et en souscrire éventuellement une autre, mais l’option annulée
lui sera tout de même intégralement facturée.
Pour éviter toute déconvenue, sont constamment visibles sur le Back Office pour le Propriétaire les
montantstotaux prévisionnels des Offres et des options à facturer pour le mois en cours. Ces montants
doivent bien entendu tenir compte des règles de facturation indiquées ci-dessus.
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 5
Interactions entre Membres et Professionnels de la PACT
Un Membre peut écrire un Avis sur une Offre depuis la page de détails de l’Offre : note, titre,
commentaire, date de la visite, contexte de la visite (affaires, couple, famille, amis, solo), photos. Cela
étant, pour limiter le risque de commentaires frauduleux, le dépôt d’un Avis est soumis à conditions :
• Être authentifié sur la PACT en tant que Membre (la création d’un compte nécessitant l’acceptation
des conditions générales d’utilisation).
• Certifier, à chaque Avis déposé, que l’Avisreflète sa propre expérience et son opinion sur cette Offre,
qu’il n’a aucun lien (Professionnel ou personnel) avec le Professionnel de tourisme de cette Offre et
qu’il n’a reçu aucune compensation financière ou autre de sa part pour rédiger cet Avis.
• Il n’est possible de déposer qu’un seul Avis par Membre et par Offre :si le Membre souhaite déposer
un nouvel Avis pour une même Offre, il doit d’abord supprimer son ancien Avis.
Depuis son espace, le Professionnel peut consulter la liste des Avis concernant ses Offres. A l’instar
d’une application de messagerie :
• Les Avis peuvent être triés du plus récent au plus ancien ou inversement ;
• Les Avis non encore consultés et ceux auxquels il n’a pas répondu sont mis en exergue ;
• Le titre de l’Offre concernée, le pseudo du Membre, le commentaire de l’Avis, sa note, sa date, le
contexte, ses photos et la réponse éventuelle du Professionnel sont directement affichés dans la
liste : il n’y pas utilité d’ouvrir une fenêtre pour consulter les détails (cela étant il faut que l’affichage
reste ergonomique).
Les nombres totaux d’Avis non consultés et auxquels le Professionnel n’a pas répondu sont affichés. Par
ailleurs, pour un Avis sélectionné, le Professionnel peut :
• Afficher les détails de l’Offre concernée ;
• Répondre à l’Avis émis ;
• Supprimer sa réponse à un Avis ;
• « Blacklister » l’Avis (si l’Offre concernée est de type Premium) ;
• Signaler à l’Administrateur un Avis ne respectant pas les conditions d’utilisation de la PACT.
À noter : un Avis « blacklisté » n’est plus visible que de son rédacteur et du Professionnel de l’Offre
concernée, avec une indication visuelle spécifique au « blacklistage ». Si un Avis « blacklisté » est
supprimé par son rédacteur, le Professionnel récupère une possibilité de « blacklister » un autre Avis.
Depuis son espace, un Membre peut consulter la liste des Avis qu’il a déposés. Les modalités d’affichage
des Avis sont identiques à celles de l’affichage pour un Propriétaire à l’exception près que la
dénomination du Propriétaire de l’Offre concernée apparaît en lieu et place du pseudo du Membre.
Pour un Avis sélectionné, le Membre peut :
• Afficher les détails de l’Offre concernée ;
• Supprimer son Avis, entraînant la suppression de toutes les données liées à l’Avis et réincrémentant
le compteur « droit de veto » du Professionnel (si l’Avis était « blacklisté » depuis moins de 12 mois) ;
• Signaler à l’Administrateur une réponse du Professionnel ne respectant pas les conditions
d’utilisation de la PACT.
Un visiteur (authentifié ou non) peut lui aussi interagir sur la PACT. Il peut :
• Signaler à l’Administrateur un Avis de Membre ou une réponse du Professionnel ne respectant pas
les conditions d’utilisation de la PACT ;
• Mettre un « Pouce » vers le haut ou le bas pour approuver ou désapprouver l’Avis. Chaque « Pouce »
est accompagné d’un compteur visible par tous indiquant le nombre de personnes ayant approuvé
ou désapprouvé l’Avis. Il n’existe aucune modération ni contrôle sur le nombre de « Pouces ».
IUT de Lannion / Département Info. SAÉ 3 & 4 – Sujet
Année 2024-2025 / Semestres 3 & 4 6
Les comptes utilisateur de la PACT
La création d’un compte Membre est assez simple : le visiteur saisit ses informations de base (nom,
prénom, pseudonyme, e-mail, adresse postale, téléphone, mot de passe avec vérification). Une fois
validé, son compte est instantanément créé et ce dernier est automatiquement authentifié sur le Front
Office.
La création d’un compte Professionnel est semblable à celle d’un Membre à quelques exceptions près :
• Contrairement aux Membres, le Professionnel n’a pas de pseudonyme : Pour renforcer la confiance
des Visiteurs dans la PACT, ce dernier apparaîtra aux autres directement par sa dénomination
(appellation simplifiée de sa raison sociale) ainsi que son numéro de SIREN dans le cas d'un
professionnel privé.
• Le Professionnel a la possibilité de saisir tout de suite ses informations bancaires indispensables au
prélèvement mensuel du coût des Offres publiées. Dans ce cas, une vérification du relevé d’identité
bancaire est effectuée. S’il ne le fait pas, il devra le faire au plus tard au moment de la publication
de sa première Offre payante ou depuis l’interface de modification de son compte.
Un visiteur authentifié comme Professionnel ou Membre peut consulter et modifier les informations
de son compte. Bien entendu, toute modification d’information du compte est soumise à re-vérification
du mot de passe. Par ailleurs, il est prévu à moyen terme que la connexion comme la réinitialisation du
mot de passe se fasse à l’aide d’un mécanisme de double authentification.
Pour favoriser le dynamisme de la PACT et ainsi maximiser ses chances de succès, le Front Office doit
régulièrement inciter les visiteurs à se créer un compte Membre ou Propriétaire, par des liens discrets
mais avec une stratégie marketing adaptée et réfléchie en amont du projet.
Pour pouvoir supprimer son compte, un Professionnel ne doit plus avoir d’Offre « en ligne » et ses
dernières Offres doivent toutes avoir été facturées (autrement dit, la demande de suppression de
compte ne pourra être prise en compte que le 1er jour du mois suivant la résiliation de la dernière Offre
« en ligne » du Professionnel). La demande de suppression de compte est envoyée directement à
l’administrateur de la PACT qui effectuera les opérations nécessaires directement en Base de Données
(hors scope de l’application actuelle).
Contrairement à un Professionnel qui doit contacter l’administrateur de la PACT, un Membre peut
demander la suppression de son compte depuis son espace sur le Front Office. Après les vérifications
d’usage, toutes les données associées au Membre sont supprimées, à l’exception de ses Avis :
• Les Avis « blacklistés » sont intégralement supprimés (redonnant un « droit de veto »
supplémentaire au Professionnel de l’Offre concernée) ;
• Les autres Avis sont anonymisés : le pseudo est remplacé par un mot clé générique, les photos sont
supprimées, mais le reste des données est conservé.
Administration et modération
L’association souhaite limiter au maximum les interventions de l’administrateur de la PACT. Cependant,
ce dernier doit pouvoir agir sur les données de la plateforme (consulter, filtrer, trier, supprimer ou
désactiver des données) pour se conformer aux règles en vigueur, notamment en cas :
• De non-respect de la loi Informatique et Libertés et du RGPD ;
• De non-respect de la charte du site relative aux contenus délictuels (notamment à la suite du
signalement d’un Avis).
Enfin, le site doit respecter les mentions légales prévues par la loi sur la confiance dans l’économie
numérique.
