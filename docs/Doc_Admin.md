# **Documentation : Consultation et gestion des données par l'administrateur**

## **Objectif**
Comment consulter, désactiver ou supprimer les données d’un utilisateur ou d’une offre afin de vérifier leur conformité aux normes légales (ex. RGPD).

---

## **Pré-requis**
1. **Accès à la base de données**  
   - Identifiants administrateur (login et mot de passe).
   - Droits suffisants pour consulter, désactiver et supprimer les données des utilisateurs et des offres.

2. **Outils nécessaires**
   - Logiciel de gestion de base de données (ex. SQL Workbench, pgAdmin).

---

## **Étape 1 : Connexion à la base de données**

1. Ouvrir le logiciel de gestion de base de données.  
2. Saisir les informations de connexion :  
   - **Hôte** : Adresse IP ou URL du serveur.  
   - **Port** : (par défaut : `3306` pour MySQL, `5432` pour PostgreSQL, etc.).  
   - **Nom d’utilisateur** : `admin` ou équivalent.  
   - **Mot de passe** : Mot de passe associé.  
3. Tester la connexion. Si elle est réussie, accéder à l’interface principale.

---

## **Étape 2 : Consultation des données d’une offre**

1. Identifier la table contenant les informations des offres (exemple : `offers`).
2. Exécuter la requête suivante pour consulter toutes les données d’une offre spécifique :
   ```sql
   SELECT * FROM offers WHERE offer_id = [ID_DE_L_OFFRE];
   ```
   Remplacer `[ID_DE_L_OFFRE]` par l’identifiant unique de l’offre à vérifier.
3. Examiner les champs pertinents, comme :
   - **Nom de l’offre**
   - **Description**
   - **Données associées (dates, prix, etc.)**
4. Vérifier que les informations respectent les normes légales, telles que :
   - Données minimales nécessaires.
   - Absence de données sensibles non autorisées.

---

## **Étape 3 : Consultation des données d’un utilisateur**

1. Identifier la table contenant les informations des utilisateurs (exemple : `users`).
2. Exécuter la requête suivante pour consulter les données d’un utilisateur spécifique :
   ```sql
   SELECT * FROM users WHERE user_id = [ID_UTILISATEUR];
   ```
   Remplacer `[ID_UTILISATEUR]` par l’identifiant unique de l’utilisateur à vérifier.
3. Examiner les champs pertinents, comme :
   - **Nom**
   - **Adresse e-mail**
   - **Informations personnelles collectées**
4. Vérifier que les données respectent les exigences du RGPD, notamment :
   - Consentement pour les données collectées.
   - Possibilité d’anonymisation ou de suppression.

---

## **Étape 4 : Désactivation des données**

### Désactiver une offre
1. Identifier la table contenant les offres (exemple : `offers`).
2. Exécuter la requête suivante pour désactiver une offre :
   ```sql
   UPDATE offers SET status = 'inactive' WHERE offer_id = [ID_DE_L_OFFRE];
   ```
   Remplacer `[ID_DE_L_OFFRE]` par l’identifiant unique de l’offre à désactiver.

### Désactiver un utilisateur
1. Identifier la table contenant les utilisateurs (exemple : `users`).
2. Exécuter la requête suivante pour désactiver un utilisateur :
   ```sql
   UPDATE users SET status = 'inactive' WHERE user_id = [ID_UTILISATEUR];
   ```
   Remplacer `[ID_UTILISATEUR]` par l’identifiant unique de l’utilisateur à désactiver.

---

## **Étape 5 : Suppression des données**

### Supprimer les données d’une offre
1. Identifier la table contenant les offres (exemple : `offers`).
2. Exécuter la requête suivante pour supprimer une offre :
   ```sql
   DELETE FROM offers WHERE offer_id = [ID_DE_L_OFFRE];
   ```
   Remplacer `[ID_DE_L_OFFRE]` par l’identifiant unique de l’offre à supprimer.

### Supprimer les données d’un utilisateur
1. Identifier la table contenant les utilisateurs (exemple : `users`).
2. Exécuter la requête suivante pour supprimer un utilisateur :
   ```sql
   DELETE FROM users WHERE user_id = [ID_UTILISATEUR];
   ```
   Remplacer `[ID_UTILISATEUR]` par l’identifiant unique de l’utilisateur à supprimer.

---

## **Étape 6 : Précautions**

1. **Logs et traçabilité** : Documenter toutes les consultations, désactivations et suppressions dans un fichier ou une table dédiée pour assurer la traçabilité.
   Exemple :
   ```sql
   INSERT INTO logs (action, user_id, timestamp) VALUES ('consultation', [ID_UTILISATEUR], CURRENT_TIMESTAMP);
   ```
2. **Modifications** : Ne pas modifier directement les données sans justification légale.
3. **Sécurisation** : Ne jamais exposer les identifiants administrateurs ou les données consultées.
4. **Archivage** : Pour les données sensibles, envisager un archivage sécurisé avant suppression.

---

