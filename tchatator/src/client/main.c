#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <sys/select.h>

#define TAILLE_BUFFER 1024

/**
 * Affiche le menu principal.
 */
void afficherMenu() {
    printf("\n----- MENU -----\n");
    printf("1. Se connecter (Login)\n");
    printf("2. Lister les utilisateurs\n");
    printf("3. Envoyer un message\n");
    printf("4. Lister les messages reçus\n");
    printf("5. Lister l'historique\n");
    printf("6. Modifier un message\n");
    printf("7. Supprimer un message\n");
    printf("8. Bloquer un utilisateur\n");
    printf("9. Débloquer un utilisateur\n");
    printf("10. Bannir un utilisateur\n");
    printf("11. Débannir un utilisateur\n");
    printf("12. Quitter\n");
    printf("Votre choix : ");
}

/**
 * Supprime le caractère de saut de ligne en fin de chaîne.
 * @param chaine La chaîne de caractères à traiter.
 */
void supprimerSautDeLigne(char *chaine) {
    size_t longueur = strlen(chaine);
    if (longueur > 0 && chaine[longueur - 1] == '\n') {
        chaine[longueur - 1] = '\0';
    }
}

/**
 * Affiche une ligne de réponse reçue du serveur de manière formatée.
 * @param ligne La ligne de réponse du serveur.
 */
void afficherReponse(const char *ligne) {
    if (strncmp(ligne, "user:", 5) == 0) {
        char *donnees = strdup(ligne + 5);
        char *token = strtok(donnees, ",");
        int compteur = 1;
        while (token != NULL) {
            if (compteur == 1) {
                printf("  ID : %s\n", token);
            } else if (compteur == 2) {
                printf("  Email : %s\n", token);
            } else {
                printf("  Info supplémentaire : %s\n", token);
            }
            token = strtok(NULL, ",");
            compteur++;
        }
        free(donnees);
    } else if (strncmp(ligne, "msg:", 4) == 0) {
        char *data = strdup(ligne + 4);
        if (!data) {
            perror("Erreur strdup");
            return;
        }
        char *donnees = data;
        char *expediteur   = strsep(&donnees, ",");
        char *destinataire = strsep(&donnees, ",");
        char *dateEnvoi    = strsep(&donnees, ",");
        char *dateModif    = strsep(&donnees, ",");
        char *contenu      = donnees;
        if (!expediteur)   expediteur   = "";
        if (!destinataire) destinataire = "";
        if (!dateEnvoi)    dateEnvoi    = "";
        if (!dateModif)    dateModif    = "";
        if (!contenu)      contenu      = "";
        printf("Message reçu :\n");
        printf("  Expéditeur      : %s\n", expediteur);
        printf("  Destinataire    : %s\n", destinataire);
        printf("  Date d'envoi    : %s\n", dateEnvoi);
        printf("  Date modifiée   : %s\n", dateModif);
        printf("  Contenu         : %s\n", contenu);
        free(data);
    } else if ((strcmp(ligne, "userend") == 0) || (strcmp(ligne, "msgend") == 0)) {
        ;
    } else {
        printf("Réponse du serveur : %s\n", ligne);
    }
}

/**
 * Lit une ligne (ou un bloc) de réponse du serveur pour les commandes simples.
 * @param fdSocket Descripteur de socket.
 */
void lireReponseUneLigne(int fdSocket) {
    char tampon[TAILLE_BUFFER];
    int octets = recv(fdSocket, tampon, sizeof(tampon) - 1, 0);
    if (octets > 0) {
        tampon[octets] = '\0';
        char *ligne = strtok(tampon, "\n");
        while (ligne != NULL) {
            afficherReponse(ligne);
            ligne = strtok(NULL, "\n");
        }
    }
}

/**
 * Lit la réponse du serveur (plusieurs lignes) jusqu'à "msgend" ou "userend".
 * @param fdSocket Descripteur de socket.
 */
void lireReponseListe(int fdSocket) {
    char tampon[TAILLE_BUFFER];
    fd_set readfds;
    struct timeval tv;
    while (1) {
        FD_ZERO(&readfds);
        FD_SET(fdSocket, &readfds);
        tv.tv_sec = 5;
        tv.tv_usec = 0;
        int ret = select(fdSocket + 1, &readfds, NULL, NULL, &tv);
        if (ret > 0 && FD_ISSET(fdSocket, &readfds)) {
            int octets = recv(fdSocket, tampon, sizeof(tampon) - 1, 0);
            if (octets > 0) {
                tampon[octets] = '\0';
                char *ligne = strtok(tampon, "\n");
                while (ligne != NULL) {
                    afficherReponse(ligne);
                    ligne = strtok(NULL, "\n");
                }
                if (strstr(tampon, "msgend") != NULL || strstr(tampon, "userend") != NULL) {
                    break;
                }
            } else {
                break;
            }
        } else {
            break;
        }
    }
}

/**
 * Point d'entrée du programme.
 * @return Code de retour du programme.
 */
int main() {
    char ipServeur[100];
    int portServeur;
    int fdSocket;
    struct sockaddr_in adresseServeur;
    char tampon[TAILLE_BUFFER];

    printf("Entrez l'adresse IP du serveur : ");
    if (fgets(ipServeur, sizeof(ipServeur), stdin) == NULL) {
        perror("Erreur de lecture de l'adresse IP");
        exit(EXIT_FAILURE);
    }
    supprimerSautDeLigne(ipServeur);

    printf("Entrez le port du serveur : ");
    if (fgets(tampon, sizeof(tampon), stdin) == NULL) {
        perror("Erreur de lecture du port");
        exit(EXIT_FAILURE);
    }
    portServeur = atoi(tampon);
    if (portServeur <= 0) {
        fprintf(stderr, "Port invalide.\n");
        exit(EXIT_FAILURE);
    }

    fdSocket = socket(AF_INET, SOCK_STREAM, 0);
    if (fdSocket == -1) {
        perror("Erreur lors de la création du socket");
        exit(EXIT_FAILURE);
    }

    memset(&adresseServeur, 0, sizeof(adresseServeur));
    adresseServeur.sin_family = AF_INET;
    adresseServeur.sin_port = htons(portServeur);
    if (inet_pton(AF_INET, ipServeur, &adresseServeur.sin_addr) <= 0) {
        perror("Adresse IP invalide");
        close(fdSocket);
        exit(EXIT_FAILURE);
    }

    if (connect(fdSocket, (struct sockaddr *)&adresseServeur, sizeof(adresseServeur)) == -1) {
        perror("Erreur lors de la connexion au serveur");
        close(fdSocket);
        exit(EXIT_FAILURE);
    }
    printf("Connecté au serveur %s:%d\n", ipServeur, portServeur);

    int choix;
    while (1) {
        afficherMenu();
        if (fgets(tampon, sizeof(tampon), stdin) == NULL) {
            break;
        }
        choix = atoi(tampon);
        memset(tampon, 0, sizeof(tampon));
        switch (choix) {
            case 1: {
                char cleApi[256];
                printf("Entrez votre clé API : ");
                if (fgets(cleApi, sizeof(cleApi), stdin) == NULL) {
                    continue;
                }
                supprimerSautDeLigne(cleApi);
                snprintf(tampon, sizeof(tampon), "LOGIN:%s", cleApi);
                break;
            }
            case 2: {
                strcpy(tampon, "GETUSERS");
                break;
            }
            case 3: {
                int idDestinataire;
                char contenuMessage[TAILLE_BUFFER];
                char idDestStr[10];
                printf("Entrez l'ID du destinataire : ");
                if (fgets(idDestStr, sizeof(idDestStr), stdin) == NULL) {
                    continue;
                }
                idDestinataire = atoi(idDestStr);
                printf("Entrez votre message : ");
                if (fgets(contenuMessage, sizeof(contenuMessage), stdin) == NULL) {
                    continue;
                }
                supprimerSautDeLigne(contenuMessage);
                snprintf(tampon, sizeof(tampon), "MSG:%d,%.1000s", idDestinataire, contenuMessage);
                break;
            }
            case 4: {
                strcpy(tampon, "LISTMSG");
                break;
            }
            case 5: {
                char entree[20];
                printf("Entrez un ID de message pour paginer (laisser vide pour la dernière page) : ");
                if (fgets(entree, sizeof(entree), stdin) == NULL) {
                    continue;
                }
                supprimerSautDeLigne(entree);
                if (strlen(entree) == 0) {
                    strcpy(tampon, "LISTHIST");
                } else {
                    int idMsg = atoi(entree);
                    snprintf(tampon, sizeof(tampon), "LISTHIST:%d", idMsg);
                }
                break;
            }
            case 6: {
                int idMsg;
                char nouveauContenu[TAILLE_BUFFER];
                char idMsgStr[20];
                printf("Entrez l'ID du message à modifier : ");
                if (fgets(idMsgStr, sizeof(idMsgStr), stdin) == NULL) {
                    continue;
                }
                idMsg = atoi(idMsgStr);
                printf("Entrez le nouveau contenu : ");
                if (fgets(nouveauContenu, sizeof(nouveauContenu), stdin) == NULL) {
                    continue;
                }
                supprimerSautDeLigne(nouveauContenu);
                snprintf(tampon, sizeof(tampon), "MDFMSG:%d,%.1000s", idMsg, nouveauContenu);
                break;
            }
            case 7: {
                int idMsg;
                char idMsgStr[20];
                printf("Entrez l'ID du message à supprimer : ");
                if (fgets(idMsgStr, sizeof(idMsgStr), stdin) == NULL) {
                    continue;
                }
                idMsg = atoi(idMsgStr);
                snprintf(tampon, sizeof(tampon), "DLTMSG:%d", idMsg);
                break;
            }
            case 8: {
                int idUtilisateur;
                char idUtilisateurStr[20];
                printf("Entrez l'ID de l'utilisateur à bloquer : ");
                if (fgets(idUtilisateurStr, sizeof(idUtilisateurStr), stdin) == NULL) {
                    continue;
                }
                idUtilisateur = atoi(idUtilisateurStr);
                snprintf(tampon, sizeof(tampon), "BLOCKUSR:%d", idUtilisateur);
                break;
            }
            case 9: {
                int idUtilisateur;
                char idUtilisateurStr[20];
                printf("Entrez l'ID de l'utilisateur à débloquer : ");
                if (fgets(idUtilisateurStr, sizeof(idUtilisateurStr), stdin) == NULL) {
                    continue;
                }
                idUtilisateur = atoi(idUtilisateurStr);
                snprintf(tampon, sizeof(tampon), "UNBLOCKUSR:%d", idUtilisateur);
                break;
            }
            case 10: {
                int idUtilisateur;
                char idUtilisateurStr[20];
                printf("Entrez l'ID de l'utilisateur à bannir : ");
                if (fgets(idUtilisateurStr, sizeof(idUtilisateurStr), stdin) == NULL) {
                    continue;
                }
                idUtilisateur = atoi(idUtilisateurStr);
                snprintf(tampon, sizeof(tampon), "BANUSR:%d", idUtilisateur);
                break;
            }
            case 11: {
                int idUtilisateur;
                char idUtilisateurStr[20];
                printf("Entrez l'ID de l'utilisateur à débannir : ");
                if (fgets(idUtilisateurStr, sizeof(idUtilisateurStr), stdin) == NULL) {
                    continue;
                }
                idUtilisateur = atoi(idUtilisateurStr);
                snprintf(tampon, sizeof(tampon), "UNBANUSR:%d", idUtilisateur);
                break;
            }
            case 12: {
                strcpy(tampon, "QUIT");
                break;
            }
            default: {
                printf("Choix invalide.\n");
                continue;
            }
        }
        if (write(fdSocket, tampon, strlen(tampon)) == -1) {
            perror("Erreur lors de l'envoi de la commande");
            break;
        }
        if (strcmp(tampon, "QUIT") == 0) {
            printf("Déconnexion...\n");
            break;
        }
        if (choix == 2 || choix == 4 || choix == 5) {
            lireReponseListe(fdSocket);
        } else {
            lireReponseUneLigne(fdSocket);
        }
    }
    close(fdSocket);
    return 0;
}
