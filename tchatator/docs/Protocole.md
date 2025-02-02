# Protocole de communication du serveur Tchatator

## 1. Introduction
Le serveur Tchatator utilise un protocole basé sur des messages texte échangés via des sockets TCP. Les clients peuvent envoyer des requêtes sous forme de commandes textuelles et recevoir des réponses du serveur selon un format spécifique decrit par ce document.

## 2. Format des messages
Chaque message envoyé au serveur suit la structure suivante :

```
COMMANDE:PARAMETRES
```

Les réponses du serveur sont sous le format :

```
CODE_ERREUR: MESSAGE
```

### Codes de réponse
- `1/OK` : Succès
- `103/DENIED` : Accès refusé
- `104/FORBIDDEN` : Action interdite
- `105/NOT_FOUND` : Ressource non trouvée
- `107/NOLOGIN` : Utilisateur non authentifié
- `110/TOO_MANY_REQUESTS` : Trop de requêtes envoyées
- `142/BAD_REQUEST` : Requête mal formée
- `500/INTERNAL_ERROR` : Erreur interne au serveur
- `116/MISFMT` : Format incorrect

---

## 3. Commandes disponibles

### 3.1. Authentification

#### `LOGIN:<cle_api>`
Authentifie un utilisateur avec sa clé API.

**Réponses :**
- `1/OK: Connexion réussie`
- `107/NOLOGIN: Connexion échouée`
- `142/BAD_REQUEST: Clé d'API manquante`

#### `QUIT`
Déconnecte un utilisateur.

**Réponses :**
- `1/OK`

---

### 3.2. Gestion des utilisateurs

#### `GETUSERS`
Récupère la liste des utilisateurs disponibles.

**Réponses :**
- `1/OK`
- `user:<id>,<email>,<nom>` (pour chaque utilisateur)
- `userend` (fin de la liste)
- `500/INTERNAL_ERROR`

#### `BLOCKUSR:<id>`
Bloque un utilisateur.

**Réponses :**
- `1/OK`
- `104/FORBIDDEN`
- `500/INTERNAL_ERROR`

#### `UNBLOCKUSR:<id>`
Débloque un utilisateur.

**Réponses :**
- `1/OK`
- `104/FORBIDDEN`
- `500/INTERNAL_ERROR`

#### `BANUSR:<id>`
Bannit un utilisateur.

**Réponses :**
- `1/OK`
- `104/FORBIDDEN`
- `500/INTERNAL_ERROR`
#### `UNBANUSR:<id>`

Débannit un utilisateur.

**Réponses :**
- `1/OK`
- `104/FORBIDDEN`
- `500/INTERNAL_ERROR`

---

### 3.3. Envoi et gestion des messages

#### `MSG:<id_destinataire>,<contenu>`
Envoie un message à un utilisateur.

**Réponses :**
- `1/OK:<id_message>`
- `104/FORBIDDEN: Utilisateur bloqué ou banni`
- `142/BAD_REQUEST: Message trop long`
- `500/INTERNAL_ERROR`

#### `LISTMSG`
Récupère les messages non lus.

**Réponses :**
- `1/OK`
- `msg:<expéditeur>,<destinataire>,<contenu>`
- `msgend`
- `500/INTERNAL_ERROR`

#### `LISTHIST[:id_message]`
Récupère l'historique des messages.

**Réponses :**
- `1/OK`
- `msg:<expéditeur>,<destinataire>,<date_envoi>,<date_modif>,<contenu>`
- `msgend`
- `500/INTERNAL_ERROR`

#### `MDFMSG:<id_message>,<nouveau_contenu>`
Modifie un message.

**Réponses :**
- `1/OK: Message modifié`
- `104/FORBIDDEN: Modification non autorisée`
- `116/MISFMT: Mauvais format de requête`
- `500/INTERNAL_ERROR`

#### `DLTMSG:<id_message>`
Supprime un message.

**Réponses :**
- `1/OK: Message supprimé`
- `104/FORBIDDEN: Suppression non autorisée`
- `500/INTERNAL_ERROR`

---

## 4. Gestion des limitations et sécurité

### 4.1. Système anti-spam
Un utilisateur ne peut pas dépasser :
- `nbRequetesMaxParMinute` requêtes par minute
- `nbRequetesMaxParHeure` requêtes par heure

Si ces limites sont atteintes, le serveur répondra :
```
110/TOO_MANY_REQUESTS: Trop de requêtes
```

### 4.2. Système de modération
- Seuls les administrateurs et utilisateurs professionnels peuvent bloquer ou bannir.
- Un administrateur (`idClient == -2`) a tous les droits.
- Les actions sont enregistrées dans `punishment.txt` et plus generalement dans votre fichier de log.


