# Documentation du Protocole Tchatator

Ce document décrit le protocole utilisé pour le projet **Tchatator**, permettant la gestion des échanges asynchrones entre les clients et les professionnels via une API.

## Structure Générale des Requêtes
- **Requête** : `<ACTION>:<paramètres>`  
- **Réponse** : `<CODE>/<MESSAGE>:<détails>`

Chaque requête commence par une action suivie des paramètres nécessaires. Les réponses suivent un format standardisé pour simplifier le traitement par les clients.

---

## Actions Supportées

### 1. Identification

**Requête :**
```text
LOGIN:<clé_api>
```

**Réponses :**
- `1/OK` : Authentification réussie.
- `103/DENIED` : Accès refusé, clé invalide ou bannie.

### 2. Envoi d’un Message

**Requête :**
```text
MSG:<clé_api>,<destinataire>,<longueur_message>,<contenu>
```

**Réponses :**
- `1/OK` : Message reçu et stocké.
- `103/DENIED` : Clé API invalide ou bannie.
- `116/MISFMT` : Format du message incorrect.
- `113/PAYLOAD_TOO_LARGE` : Message trop long.
- `126/TOOMANYREQ` : Trop de messages envoyés en un temps limité.

### 3. Récupération des Messages Non Lus

**Requête :**
```text
GET_UNREAD:<clé_api>
```

**Réponses :**
- `1/OK:<messages>` : Liste des messages non lus.
- `103/DENIED` : Clé API invalide ou bannie.

### 4. Récupération de l’Historique

**Requête :**
```text
GET_HISTORY:<clé_api>,<id_message_debut>,<limite>
```

**Réponses :**
- `1/OK:<historique>` : Bloc de messages historiques.
- `103/DENIED` : Clé API invalide ou bannie.
- `104/NOT_FOUND` : Aucun message trouvé correspondant aux paramètres.

### 5. Modification d’un Message

**Requête :**
```text
EDIT_MSG:<clé_api>,<id_message>,<nouveau_contenu>
```

**Réponses :**
- `1/OK` : Message modifié avec succès.
- `103/DENIED` : Clé API invalide ou bannie.
- `103/FORBIDDEN` : Tentative de modification non autorisée.
- `104/NOT_FOUND` : Message introuvable.

### 6. Suppression d’un Message

**Requête :**
```text
DEL_MSG:<clé_api>,<id_message>
```

**Réponses :**
- `1/OK` : Message marqué comme supprimé.
- `103/DENIED` : Clé API invalide ou bannie.
- `103/FORBIDDEN` : Tentative de suppression non autorisée.
- `104/NOT_FOUND` : Message introuvable.

### 7. Blocage d’un Client

**Requête :**
```text
BLOCK_CLIENT:<clé_api>,<id_client>,<durée_en_heures>
```

**Réponses :**
- `1/OK` : Blocage appliqué.
- `103/DENIED` : Clé API invalide ou bannie.
- `103/FORBIDDEN` : Droits insuffisants.

### 8. Bannissement d’un Client

**Requête :**
```text
BAN_CLIENT:<clé_api>,<id_client>
```

**Réponses :**
- `1/OK` : Bannissement appliqué.
- `103/DENIED` : Clé API invalide ou bannie.
- `103/FORBIDDEN` : Droits insuffisants.

---

## Gestion des Erreurs et Limites

### Limite de Requêtes
- **Maximum par minute** : 12 requêtes.
- **Maximum par heure** : 90 requêtes.
- Toute requête excédant ces limites retourne :
  - `129/TOO_MANY_REQUESTS` : Trop de requêtes.

### Gestion des Messages
- **Taille maximale d’un message** : 1000 caractères.
- Les messages dépassant cette limite retournent :
  - `113/PAYLOAD_TOO_LARGE`.

---

## Logs
Les logs permettent de suivre les activités du service. Chaque ligne inclut :
- **Date+heure**
- **IP du client**
- **Action effectuée**
- **Résultat de l’action**

**Exemple :**
```text
[2025-01-13 12:00:00] 192.168.1.1 LOGIN:1/OK
[2025-01-13 12:01:00] 192.168.1.2 MSG:1/OK
```

---

## Sécurité
1. **Authentification** : Utilisation de clés API uniques pour chaque utilisateur.
2. **Limitations** : Réduction des risques de spam et de flood par des restrictions sur le nombre de requêtes.
3. **Permissions** : Gestion stricte des droits selon le type d’utilisateur.

---

## Exemple de Scénario d’Utilisation
### Envoi d’un message
1. Le client envoie une requête :
   ```text
   MSG:clé_api123,user456,15,Bonjour!
   ```
2. Le serveur répond :
   ```text
   1/OK
   ```

### Blocage d’un client
1. Le professionnel envoie une requête :
   ```text
   BLOCK_CLIENT:clé_api123,user789,24
   ```
2. Le serveur répond :
   ```text
   1/OK
   ```
