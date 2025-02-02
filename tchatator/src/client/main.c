#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <sys/select.h>

#define BUFFER_SIZE 1024

/* Affiche le menu principal */
void displayMenu() {
    printf("\n----- MENU -----\n");
    printf("1. Login\n");
    printf("2. List Users\n");
    printf("3. Send Message\n");
    printf("4. List Messages\n");
    printf("5. List History\n");
    printf("6. Modify Message\n");
    printf("7. Delete Message\n");
    printf("8. Quit\n");
    printf("Votre choix : ");
}

/* Lit la réponse du serveur en utilisant select pour gérer les délais */
void readResponse(int sockFd) {
    char buffer[BUFFER_SIZE];
    fd_set readfds;
    struct timeval tv;

    // Boucle de lecture : on continue tant que le socket dispose de données à lire
    while (1) {
        FD_ZERO(&readfds);
        FD_SET(sockFd, &readfds);
        tv.tv_sec = 1;   // délai d'attente d'une seconde
        tv.tv_usec = 0;

        int ret = select(sockFd + 1, &readfds, NULL, NULL, &tv);
        if (ret > 0 && FD_ISSET(sockFd, &readfds)) {
            int bytes = recv(sockFd, buffer, sizeof(buffer) - 1, 0);
            if (bytes > 0) {
                buffer[bytes] = '\0';
                printf("Réponse du serveur : %s\n", buffer);
                // Si la réponse contient "msgend", on considère la fin de la réponse multi-lignes
                if (strstr(buffer, "msgend") != NULL) {
                    break;
                }
            } else {
                // Connexion fermée ou erreur
                break;
            }
        } else {
            break;
        }
    }
}

/* Supprime le caractère de saut de ligne en fin de chaîne */
void trimNewline(char *str) {
    size_t len = strlen(str);
    if(len > 0 && str[len - 1] == '\n') {
        str[len - 1] = '\0';
    }
}

int main() {
    char serverIp[100];
    int serverPort;
    int sockFd;
    struct sockaddr_in serverAddr;
    char buffer[BUFFER_SIZE];

    /* Saisie de l'adresse IP et du port */
    printf("Entrez l'adresse IP du serveur : ");
    if (fgets(serverIp, sizeof(serverIp), stdin) == NULL) {
        perror("Erreur de lecture de l'adresse IP");
        exit(EXIT_FAILURE);
    }
    trimNewline(serverIp);

    printf("Entrez le port du serveur : ");
    if (fgets(buffer, sizeof(buffer), stdin) == NULL) {
        perror("Erreur de lecture du port");
        exit(EXIT_FAILURE);
    }
    serverPort = atoi(buffer);
    if (serverPort <= 0) {
        fprintf(stderr, "Port invalide.\n");
        exit(EXIT_FAILURE);
    }

    /* Création du socket */
    sockFd = socket(AF_INET, SOCK_STREAM, 0);
    if (sockFd == -1) {
        perror("Erreur lors de la création du socket");
        exit(EXIT_FAILURE);
    }

    /* Configuration de l'adresse du serveur */
    memset(&serverAddr, 0, sizeof(serverAddr));
    serverAddr.sin_family = AF_INET;
    serverAddr.sin_port = htons(serverPort);
    if (inet_pton(AF_INET, serverIp, &serverAddr.sin_addr) <= 0) {
        perror("Adresse IP invalide");
        close(sockFd);
        exit(EXIT_FAILURE);
    }

    /* Connexion au serveur */
    if (connect(sockFd, (struct sockaddr *)&serverAddr, sizeof(serverAddr)) == -1) {
        perror("Erreur lors de la connexion au serveur");
        close(sockFd);
        exit(EXIT_FAILURE);
    }

    printf("Connecté au serveur %s:%d\n", serverIp, serverPort);

    /* Boucle principale du menu */
    int choice;
    while (1) {
        displayMenu();
        if (fgets(buffer, sizeof(buffer), stdin) == NULL) {
            break;
        }
        choice = atoi(buffer);

        memset(buffer, 0, sizeof(buffer));

        switch (choice) {
            case 1: {
                /* Login : demande la clé API */
                char apiKey[256];
                printf("Entrez votre clé API : ");
                if (fgets(apiKey, sizeof(apiKey), stdin) == NULL) {
                    continue;
                }
                trimNewline(apiKey);
                snprintf(buffer, sizeof(buffer), "LOGIN:%s", apiKey);
                break;
            }
            case 2: {
                /* List Users */
                strcpy(buffer, "GETUSERS");
                break;
            }
            case 3: {
                /* Send Message */
                int receiverId;
                char messageContent[BUFFER_SIZE];
                char receiverStr[10];

                printf("Entrez l'ID du destinataire : ");
                if (fgets(receiverStr, sizeof(receiverStr), stdin) == NULL) {
                    continue;
                }
                receiverId = atoi(receiverStr);
                printf("Entrez votre message : ");
                if (fgets(messageContent, sizeof(messageContent), stdin) == NULL) {
                    continue;
                }
                trimNewline(messageContent);
                // Utilisation d'une précision pour limiter la taille du message à 1000 caractères
                snprintf(buffer, sizeof(buffer), "MSG:%d,%.1000s", receiverId, messageContent);
                break;
            }
            case 4: {
                /* List Messages */
                strcpy(buffer, "LISTMSG");
                break;
            }
            case 5: {
                /* List History */
                char input[20];
                printf("Entrez un ID de message pour paginer (laisser vide pour la dernière page) : ");
                if (fgets(input, sizeof(input), stdin) == NULL) {
                    continue;
                }
                trimNewline(input);
                if (strlen(input) == 0) {
                    strcpy(buffer, "LISTHIST");
                } else {
                    int msgId = atoi(input);
                    snprintf(buffer, sizeof(buffer), "LISTHIST:%d", msgId);
                }
                break;
            }
            case 6: {
                /* Modify Message */
                int msgId;
                char newContent[BUFFER_SIZE];
                char msgIdStr[20];

                printf("Entrez l'ID du message à modifier : ");
                if (fgets(msgIdStr, sizeof(msgIdStr), stdin) == NULL) {
                    continue;
                }
                msgId = atoi(msgIdStr);
                printf("Entrez le nouveau contenu : ");
                if (fgets(newContent, sizeof(newContent), stdin) == NULL) {
                    continue;
                }
                trimNewline(newContent);
                // Utilisation d'une précision pour limiter la taille du message à 1000 caractères
                snprintf(buffer, sizeof(buffer), "MDFMSG:%d,%.1000s", msgId, newContent);
                break;
            }
            case 7: {
                /* Delete Message */
                int msgId;
                char msgIdStr[20];
                printf("Entrez l'ID du message à supprimer : ");
                if (fgets(msgIdStr, sizeof(msgIdStr), stdin) == NULL) {
                    continue;
                }
                msgId = atoi(msgIdStr);
                snprintf(buffer, sizeof(buffer), "DLTMSG:%d", msgId);
                break;
            }
            case 8: {
                /* Quitter */
                strcpy(buffer, "QUIT");
                break;
            }
            default: {
                printf("Choix invalide.\n");
                continue;
            }
        }

        /* Envoi de la commande au serveur */
        if (write(sockFd, buffer, strlen(buffer)) == -1) {
            perror("Erreur lors de l'envoi de la commande");
            break;
        }

        /* Si l'utilisateur a choisi de quitter, on arrête la boucle */
        if (strcmp(buffer, "QUIT") == 0) {
            printf("Déconnexion...\n");
            break;
        }

        /* Lecture et affichage de la réponse du serveur */
        readResponse(sockFd);
    }

    close(sockFd);
    return 0;
}
