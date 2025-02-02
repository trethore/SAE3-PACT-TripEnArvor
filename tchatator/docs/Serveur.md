# Tchatator - Serveur

## Compilation du serveur

Pour compiler le serveur, assurez-vous d'avoir les bibliothèques nécessaires installées, notamment `libpq` pour PostgreSQL. Utilisez la commande suivante :

```sh
gcc -Wall -o tchatator main.c sha256.c bdd.c -I/usr/include/postgresql -lpq
```

## Exécution du serveur

Une fois compilé, lancez le serveur avec la commande :

```sh
./tchatator
```

### Options disponibles

- `--help` : Affiche l'aide du programme et quitte.
- `--verbose` : Active le mode verbeux.
- `--test` : Effectue des tests sur le serveur.

Exemple :
```sh
./tchatator --verbose
```

## Configuration

Le serveur charge sa configuration depuis le fichier `config.cfg`. Ce fichier doit contenir les paramètres suivants :

```ini
port=12345
ban_duration=0
block_duration=0
max_requests_per_minute=12
max_requests_per_hour=90
max_message_size=1000
queue_size=5
history_block_size=20
log_file_path=logs.txt
admin_api_key=<SECRET_KEY>
reload_signal=1
```

Pour recharger la configuration sans arrêter le serveur, envoyez le signal approprié :

```sh
kill -SIGHUP <pid_du_serveur>
```

## Fichiers et leur rôle

- `serveur.c` : Code source du serveur.
- `config.cfg` : Fichier de configuration.
- `punishment.txt` : Historique des sanctions appliquées aux utilisateurs.
- `logs.txt` : Fichier de log des actions du serveur.

## Interaction avec le serveur

Le serveur fonctionne sur le port défini dans `config.cfg`. Les clients doivent envoyer des requêtes formatées via une connexion socket.

### Liste des commandes

- `LOGIN:<clé_api>` : Authentifie un utilisateur avec une clé API.
- `QUIT` : Déconnecte un utilisateur.
- `GETUSERS` : Liste les utilisateurs disponibles.
- `LISTMSG` : Liste les messages non lus.
- `LISTHIST` : Récupère l'historique des messages.
- `MSG:<id_destinataire>,<contenu>` : Envoie un message.
- `MDFMSG:<id_message>,<nouveau_contenu>` : Modifie un message.
- `DLTMSG:<id_message>` : Supprime un message.
- `BLOCKUSR:<id_utilisateur>` : Bloque un utilisateur.
- `UNBLOCKUSR:<id_utilisateur>` : Débloque un utilisateur.
- `BANUSR:<id_utilisateur>` : Bannit un utilisateur.
- `UNBANUSR:<id_utilisateur>` : Débannit un utilisateur.

## Tests

Le serveur inclut un mode de test accessible avec l'option `--test`.

```sh
./tchatator --test
```

Les tests incluent :
- Vérification du format de date et heure.
- Vérification de la connexion à la base de données.
- Vérification du système de log.

## Arrêt du serveur

Le serveur peut être arrêté proprement en envoyant un signal `SIGINT`ou en appuyant sur CTRL + C :

```sh
kill -SIGINT <pid_du_serveur>
```

