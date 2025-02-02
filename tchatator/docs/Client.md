# Tchatator - Client

## Introduction
Le client **Tchatator** est une application en ligne de commande permettant d'envoyer et de recevoir des messages via le serveur **Tchatator**.

## Compilation
Pour compiler le client, vous devez disposer d'un compilateur **GCC** sur une machine Linux. Utilisez la commande suivante :

```sh
gcc -o client main.c
```

## Exécution
Avant d'exécuter le programme, assurez-vous que le serveur **Tchatator** est en cours d'exécution. Ensuite, lancez le client avec :

```sh
./client
```

L'application vous demandera d'entrer l'adresse IP et le port du serveur.

## Menu et Fonctionnalités
Une fois connecté au serveur, le client propose un menu interactif avec les options suivantes :

1. **Se connecter (Login)** : L'utilisateur doit saisir sa clé API pour s'authentifier. La clé API est disponible sur la page *Mon Compte* du site **PACT**.
2. **Lister les utilisateurs** : Récupère la liste des utilisateurs enregistrés.
3. **Envoyer un message** : Permet d'envoyer un message à un autre utilisateur.
4. **Lister les messages reçus** : Affiche les messages non lus reçus par l'utilisateur.
5. **Lister l'historique** : Affiche l'historique des messages envoyés et reçus.
6. **Modifier un message** : Permet à l'utilisateur de modifier un de ses messages envoyés.
7. **Supprimer un message** : Marque un message comme supprimé.
8. **Bloquer un utilisateur** : Empêche un utilisateur d'envoyer des messages.
9. **Débloquer un utilisateur** : Annule le blocage d'un utilisateur.
10. **Bannir un utilisateur** : Empêche définitivement un utilisateur d'envoyer des messages.
11. **Débannir un utilisateur** : Annule le bannissement d'un utilisateur.
12. **Quitter** : Ferme l'application.

## Communication avec le Serveur
Le client utilise des **requêtes textuelles** conformes au protocole défini dans [Protocole.md](Protocole.md). Voici quelques exemples de commandes envoyées au serveur :

- **Authentification** : `LOGIN:<clé_api>`
- **Envoi de message** : `MSG:<id_destinataire>,<message>`
- **Récupération des messages** : `LISTMSG`
- **Modification d'un message** : `MDFMSG:<id_message>,<nouveau_contenu>`
- **Suppression d'un message** : `DLTMSG:<id_message>`

Le serveur répond avec des messages formatés indiquant le succès ou l'échec des opérations.

## Gestion des Erreurs
- Si une requête est mal formée, le serveur renvoie `MISFMT`.
- Si l'utilisateur tente d'envoyer trop de messages en un temps limité, il reçoit `TOOMRQ`.
- Si la clé API est invalide ou bannie, le serveur renvoie `DENIED`.
- Si un message est trop long (plus de 1000 caractères), le serveur renvoie `PAYLOAD_TOO_LARGE`.

## Sécurité et Restrictions
- **Authentification obligatoire** : Toute action nécessite une clé API valide.
- **Limite de requêtes** : Maximum 12 requêtes par minute et 90 par heure (par default).
- **Blocage et bannissement** : Un utilisateur peut être bloqué temporairement ou banni définitivement par un administrateur.

## Conclusion
Le client **Tchatator** permet une communication efficace avec le serveur via un protocole bien défini. Il est essentiel de suivre les restrictions et les bonnes pratiques pour assurer une expérience fluide et sécurisée.

