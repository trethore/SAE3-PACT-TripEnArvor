# Exemples de cas pratiques entre le serveur et le client

Ce document illustre des échanges entre un client et le serveur **Tchatator** en utilisant le protocole défini.

## 1. Connexion d'un utilisateur

**Requête envoyée par le client :**
```text
LOGIN:b8af4b4ee732127740f95f31451b0d9d08297e8983b0709f2c6040fb02b9d0fb
```
**Réponse du serveur :**
```text
1/OK
```

## 2. Récupération des utilisateurs

**Requête envoyée par le client :**
```text
GETUSERS
```
**Réponse du serveur :**
```text
1/OK
user:1,alice@example.com
user:2,bob@example.com
userend
```

## 3. Envoi d'un message

**Requête envoyée par le client :**
```text
MSG:2,Bonjour Bob, comment vas-tu ?
```
**Réponse du serveur :**
```text
1/OK:12345
```
*Le numéro 12345 est l'identifiant unique du message envoyé.*

## 4. Récupération des messages non lus

**Requête envoyée par le client :**
```text
LISTMSG
```
**Réponse du serveur :**
```text
1/OK
msg:alice@example.com,bob@example.com,2025-02-02 14:00:00,,Salut Bob !
msgend
```

## 5. Modification d'un message

**Requête envoyée par le client :**
```text
MDFMSG:12345,Bonjour Bob ! Comment vas-tu aujourd'hui ?
```
**Réponse du serveur :**
```text
1/OK
```

## 6. Suppression d'un message

**Requête envoyée par le client :**
```text
DLTMSG:12345
```
**Réponse du serveur :**
```text
1/OK
```

## 7. Blocage d'un utilisateur

**Requête envoyée par le client (professionnel) :**
```text
BLOCKUSR:3
```
**Réponse du serveur :**
```text
1/OK
```

## 8. Déblocage d'un utilisateur

**Requête envoyée par le client (professionnel) :**
```text
UNBLOCKUSR:3
```
**Réponse du serveur :**
```text
1/OK
```

## 9. Bannissement d'un utilisateur

**Requête envoyée par un administrateur :**
```text
BANUSR:3
```
**Réponse du serveur :**
```text
1/OK
```

## 10. Débannissement d'un utilisateur

**Requête envoyée par un administrateur :**
```text
UNBANUSR:3
```
**Réponse du serveur :**
```text
1/OK
```



