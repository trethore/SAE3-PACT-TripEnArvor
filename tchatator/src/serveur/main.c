#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#include <time.h>
#include <stdbool.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <libpq-fe.h>

#define BUFFER_SIZE 1024

int portServeur = 12345;
int dureeBanissement = 0;
int dureeBlocage = 0;
int nbRequetesMaxParMinute = 12;
int nbRequetesMaxParHeure = 90;
int tailleMaxMessage = 1000;
int tailleFileAttente = 5;
int tailleBlocHistorique = 20;
char cheminFichierLog[1024];
char cleApiAdmin[256];
int signalRechargement = SIGHUP;
// Serveur
int fdServeur;
bool estVerbeux = false;
// Client actuel
char ipClient[INET_ADDRSTRLEN];
int idClient = -1;
int estClientPro = false;

// Fichiers
const char *CONFIG_FILE = "config.cfg";
const char *PUNISHMENT_FILE = "punishment.txt";
// Externes

extern PGconn* getConnection();
extern PGresult* getAllMembers();
extern PGresult* getAllPrivateProfessionals();
extern PGresult* getAllPublicProfessionals();
extern PGresult* getUsersList(int isPro);
extern int createMessage(int id_emeteur, int id_receveur, const char *contenu);
extern PGresult* getMessagesForUser(int id_receveur);
extern int updateMessage(int id_message, const char *new_content);
extern int deleteMessage(int id_message);
extern int loginWithKey(const char *apiKey, int *isPro, int *compteId);
extern int isProfessional(int compteId);
extern int isMessageFromSender(int msgid, int client_id);
extern char* getEmailFromId(int id_compte);

// Prototypes des fonctions

/**
 * Affiche l'aide du programme et quitte.
 */
void help();

/**
 * Charge la configuration depuis le fichier config.cfg.
 */
void getConfig();

/**
 * Initialise et démarre le serveur socket.
 */
void startSocketServer();

/**
 * Arrête le serveur proprement lors de la réception d'un signal.
 * @param sig Numéro du signal reçu.
 */
void stopServer(int sig);

/**
 * Écrit un message dans le fichier de log.
 * @param niveau Niveau de log (0: INFO, 1: WARN, 2: ERROR).
 * @param texte Message à enregistrer.
 */
void logInFile(int niveau, const char* texte);

/**
 * Écrit un message dans le fichier de log avec l'adresse IP du client.
 * @param niveau Niveau de log.
 * @param ipClient Adresse IP du client.
 * @param texte Message à enregistrer.
 */
void logInFileWithIp(int niveau, const char* ipClient, const char* texte);

/**
 * Retourne la date et l'heure actuelles sous forme de chaîne formatée.
 * @return Chaîne de caractères représentant la date et l'heure.
 */
char* getDateHeure();

/**
 * Gère les messages reçus par le serveur et exécute les actions correspondantes.
 * @param message Message reçu du client.
 * @param fdClient Descripteur de socket du client.
 * @return 0 si le message a été traité, -1 si la connexion doit être fermée.
 */
int gereMessage(const char* message, int fdClient);

/**
 * Envoie un message au client via le socket.
 * @param fdClient Descripteur de socket du client.
 * @param message Message à envoyer.
 */
void sendMessage(int fdClient, const char* message);

/**
 * Envoie un message au client via le socket avec un code d'erreur.
 * @param fdClient Descripteur du client.
 * @param codeErreur Code d'erreur à envoyer.
 * @param texte Message à envoyer.
 */
void sendErrorMessage(int fdClient, const char* codeErreur, const char* texte);

/**
 * Authentifie un utilisateur à l'aide d'une clé API.
 * @param message Message contenant la clé API.
 * @param fdClient Descripteur du client.
 * @param idClient Pointeur vers l'ID du client authentifié.
 * @param estClientPro Pointeur vers le statut professionnel du client.
 * @return 1 si l'authentification réussit, 0 sinon.
 */
int login(const char* message, int fdClient, int *idClient, int *estClientPro);

/**
 * Gère la déconnexion du client.
 * @param message Message reçu.
 * @param fdClient Descripteur du client.
 * @param idClient Pointeur vers l'ID du client.
 * @param estClientPro Pointeur vers le statut professionnel du client.
 * @return -1 si le client se déconnecte, 0 sinon.
 */
int quitServer(const char* message, int fdClient, int *idClient, int *estClientPro);

/**
 * Liste les utilisateurs disponibles pour le client connecté.
 * @param message Message reçu.
 * @param fdClient Descripteur du client.
 * @return 1 si la requête est traitée, 0 sinon.
 */
int listUsers(const char* message, int fdClient);

/**
 * Permet l'envoi d'un message à un utilisateur.
 * @param message Message reçu.
 * @param fdClient Descripteur du client.
 * @return 1 si le message est envoyé, 0 sinon.
 */
int sendUserMessage(const char* message, int fdClient);

/**
 * Modifie un message existant.
 * @param message Message reçu contenant l'ID et le nouveau contenu.
 * @param fdClient Descripteur du client.
 * @return 1 si la modification réussit, 0 sinon.
 */
int modifyUserMessage(const char* message, int fdClient);

/**
 * Supprime un message en le marquant comme supprimé.
 * @param message Message reçu contenant l'ID du message.
 * @param fdClient Descripteur du client.
 * @return 1 si la suppression réussit, 0 sinon.
 */
int deleteUserMessage(const char* message, int fdClient);

/**
 * Liste les messages non lus du client.
 * @param message Message reçu.
 * @param fdClient Descripteur du client.
 * @return 1 si la requête est traitée, 0 sinon.
 */
int listUserMessage(const char* message, int fdClient);

/**
 * Récupère l'historique des messages du client.
 * @param message Message reçu.
 * @param fdClient Descripteur du client.
 * @return 1 si la requête est traitée, 0 sinon.
 */
int listUserHistory(const char* message, int fdClient);

/**
 * Bloque un utilisateur pour une période déterminée.
 * @param message Message reçu contenant l'ID de l'utilisateur.
 * @param idClient ID du client qui effectue le blocage.
 * @return 1 si l'opération réussit, 0 sinon.
 */
int blockUser(const char* message, int fdClient);

/**
 * Débloque un utilisateur précédemment bloqué.
 * @param message Message reçu contenant l'ID de l'utilisateur.
 * @param fdClient Descripteur du client.
 * @return 1 si l'opération réussit, 0 sinon.
 */
int unblockUser(const char* message, int fdClient);

/**
 * Bannit un utilisateur définitivement.
 * @param message Message reçu contenant l'ID de l'utilisateur.
 * @param fdClient Descripteur du client.
 * @return 1 si l'opération réussit, 0 sinon.
 */
int banUser(const char* message, int fdClient);

/**
 * Débannit un utilisateur précédemment banni.
 * @param message Message reçu contenant l'ID de l'utilisateur.
 * @param fdClient Descripteur du client.
 * @return 1 si l'opération réussit, 0 sinon.
 */
int unbanUser(const char* message, int fdClient);

/**
 * Vérifie si un utilisateur est bloqué ou banni.
 * @param fdClient Descripteur du client.
 * @return 1 si bloqué ou banni, 0 sinon.
 */
int isBlockedOrBanned(int idDestinataire);

/**
 * Vérifie si un utilisateur a le droit de modérer (bloquer/bannir).
 * @param idCompte ID de l'utilisateur à vérifier.
 * @return 1 si l'utilisateur peut modérer, 0 sinon.
 */
int canModerate(int idCompte);

/**
 * Convertit une date/heure sous forme de chaîne en timestamp.
 * @param datetime Chaîne représentant la date et l'heure.
 * @return Valeur timestamp (time_t) ou -1 en cas d'erreur.
 */
time_t parseDateTime(const char *datetime);

/**
 * Effectue des tests sur le serveur.
 * --test pour activer les tests.
 */

void test();

bool isSpamming();
// Codes d'erreur
#define SUCCESS_OK "1/OK"
#define ERROR_DENIED "103/DENIED"
#define ERROR_FORBIDDEN "104/FORBIDDEN"
#define ERROR_NOT_FOUND "105/NOT_FOUND"
#define ERROR_NOT_LOGIN "107/NOLOGIN"
#define ERROR_TOO_MANY_REQUESTS "110/TOO_MANY_REQUESTS" 
#define ERROR_BAD_REQUEST "142/BAD_REQUEST"
#define ERROR_INTERNAL_ERROR "500/INTERNAL_ERROR"
#define ERROR_INCORRECT_FORMAT "116/MISFMT"
// Autres

#define MAX_INT 2147483647

int main(int argc, char *argv[]) {
    for (int i = 1; i < argc; i++) {
        if (strcmp(argv[i], "--help") == 0) {
            help();
            return EXIT_SUCCESS;
        } else if (strcmp(argv[i], "--verbose") == 0) {
            estVerbeux = true;
        } else if (strcmp(argv[i], "--test") == 0) {
            test();
            return EXIT_SUCCESS;
        }
    }
    signal(SIGINT, stopServer);
    signal(signalRechargement, getConfig);
    getConfig();
    printf("Chemin du fichier de log : %s\n", cheminFichierLog);
    logInFile(0, "Démarrage du serveur...");
    startSocketServer();

    return EXIT_SUCCESS;
}

void stopServer(int sig) {
    logInFile(0, "Stop...");
    printf("\nArrêt du Serveur\n");
    close(fdServeur);
    exit(EXIT_SUCCESS);
}
void rechargeConfig(int sig) {
    printf("\nSignal reçu (%d), rechargement de la configuration...\n", sig);
    logInFile(0, "Rechargement de la configuration suite à un signal");
    getConfig();

    char messageLog[256];
    snprintf(messageLog, sizeof(messageLog), 
             "Nouvelle configuration : port=%d, nbRequetesMaxParMinute=%d, nbRequetesMaxParHeure=%d",
             portServeur, nbRequetesMaxParMinute, nbRequetesMaxParHeure);
    logInFile(0, messageLog);
    printf("\nRechargement de la configuration terminé !\n");
}
void getConfig() {
    const char *nomFichier = CONFIG_FILE;
    FILE *fichier = fopen(nomFichier, "r");
    if (!fichier) {
        logInFile(2, "Erreur ouverture config");
        perror("Erreur lors de l'ouverture du fichier de configuration");
        exit(EXIT_FAILURE);
    }

    char ligne[256];
    while (fgets(ligne, sizeof(ligne), fichier)) {
        if (ligne[0] == '#' || ligne[0] == '\n') continue;

        char cle[128];
        char valeur[128];

        if (sscanf(ligne, "%127[^=]=%127s", cle, valeur) == 2) {
            if (strcmp(cle, "port") == 0) {
                portServeur = atoi(valeur);
            } else if (strcmp(cle, "ban_duration") == 0) {
                dureeBanissement = atoi(valeur);
            } else if (strcmp(cle, "block_duration") == 0) {
                dureeBlocage = atoi(valeur);
            } else if (strcmp(cle, "max_requests_per_minute") == 0) {
                nbRequetesMaxParMinute = atoi(valeur);
            } else if (strcmp(cle, "max_requests_per_hour") == 0) {
                nbRequetesMaxParHeure = atoi(valeur);
            } else if (strcmp(cle, "max_message_size") == 0) {
                tailleMaxMessage = atoi(valeur);
            } else if (strcmp(cle, "queue_size") == 0) {
                tailleFileAttente = atoi(valeur);
            } else if (strcmp(cle, "history_block_size") == 0) {
                tailleBlocHistorique = atoi(valeur);
            } else if (strcmp(cle, "log_file_path") == 0) {
                strncpy(cheminFichierLog, valeur, sizeof(cheminFichierLog) - 1);
                cheminFichierLog[sizeof(cheminFichierLog) - 1] = '\0';
            } else if (strcmp(cle, "admin_api_key") == 0) {
                strncpy(cleApiAdmin, valeur, sizeof(cleApiAdmin) - 1);
            } else if (strcmp(cle, "reload_signal") == 0) {
                signalRechargement = atoi(valeur);
            } else {
                fprintf(stderr, "Clé inconnue : %s\n", cle);
            }
        }
    }

    fclose(fichier);
}


void startSocketServer() {
    struct sockaddr_in adresseServeur, adresseClient;
    socklen_t tailleAdresseClient = sizeof(adresseClient);
    char tampon[BUFFER_SIZE];

    fdServeur = socket(AF_INET, SOCK_STREAM, 0);
    if (fdServeur == -1) {
        perror("Erreur lors de la création du socket");
        exit(EXIT_FAILURE);
    }

    memset(&adresseServeur, 0, sizeof(adresseServeur));
    adresseServeur.sin_family = AF_INET;
    adresseServeur.sin_addr.s_addr = INADDR_ANY;
    adresseServeur.sin_port = htons(portServeur);
    
    int option = 1;
    setsockopt(fdServeur, SOL_SOCKET, SO_REUSEADDR, &option, sizeof(option));

    if (bind(fdServeur, (struct sockaddr*)&adresseServeur, sizeof(adresseServeur)) == -1) {
        perror("Erreur lors du bind");
        close(fdServeur);
        exit(EXIT_FAILURE);
    }

    if (listen(fdServeur, tailleFileAttente) == -1) {
        perror("Erreur lors du listen");
        close(fdServeur);
        exit(EXIT_FAILURE);
    }

    printf("Serveur démarré sur le port %d avec une file d'attente de %d\n", portServeur, tailleFileAttente);
    logInFile(0, "Serveur démarré !");
    while (1) {
        printf("En attente de connexions\n");

        int fdClient = accept(fdServeur, (struct sockaddr*)&adresseClient, &tailleAdresseClient);
        if (fdClient == -1) {
            perror("Erreur lors de l'acceptation de la connexion");
            continue;
        }

        if (inet_ntop(AF_INET, &adresseClient.sin_addr, ipClient, INET_ADDRSTRLEN) != NULL) {
            printf("Connexion acceptée depuis : %s\n", ipClient);
            char buffer[256]; // segmentation fault qui ma fait perdere 2h de ma vie
            snprintf(buffer, sizeof(buffer), "Connexion acceptée depuis : %s", ipClient);
            logInFile(0, buffer);
        } else {
            perror("Erreur lors de la récupération de l'adresse IP du client");
            close(fdClient);
            continue;
        }

        while (1) {
            memset(tampon, 0, BUFFER_SIZE); 

            ssize_t octetsRecus = recv(fdClient, tampon, BUFFER_SIZE - 1, 0);
            if (octetsRecus > 0) {
                tampon[octetsRecus] = '\0';
                printf("Message reçu de %s : %s\n", ipClient, tampon);
                logInFileWithIp(0, ipClient, tampon);
                if (gereMessage(tampon, fdClient) == -1) {
                    break;
                }
            } else if (octetsRecus == 0) {
                printf("Client %s s'est déconnecté.\n", ipClient);
                idClient = -1;
                estClientPro = 0;
                break;
            } else {
                perror("Erreur lors de la réception des données");
                break;
            }
        }

        close(fdClient); 
    }
}

int gereMessage(const char* message, int fdClient) {
    int resultat = 0;

    if (quitServer(message, fdClient, &idClient, &estClientPro) == -1) {
        return -1;
    }
    if (isSpamming()) {
        sendErrorMessage(fdClient, ERROR_TOO_MANY_REQUESTS, "Trop de requêtes\n");
        return 1;
    }
     
    resultat |= login(message, fdClient, &idClient, &estClientPro);
    if (idClient == -1) {
        if (resultat == 1) {
            sendErrorMessage(fdClient, ERROR_NOT_LOGIN, "Veuillez vous connecter\n");
        } else {
            sendErrorMessage(fdClient, ERROR_BAD_REQUEST, "Mauvaise requête\n");
        }
        return 0;
    }

    resultat |= listUsers(message, fdClient);
    resultat |= sendUserMessage(message, fdClient);
    resultat |= modifyUserMessage(message, fdClient);
    resultat |= deleteUserMessage(message, fdClient);
    resultat |= listUserMessage(message, fdClient);
    resultat |= listUserHistory(message, fdClient);
    resultat |= blockUser(message, fdClient);
    resultat |= unblockUser(message, fdClient);
    resultat |= banUser(message, fdClient);
    resultat |= unbanUser(message, fdClient);

    if (!resultat) {
        sendErrorMessage(fdClient, ERROR_BAD_REQUEST, "Mauvaise requête\n");
    }
    return 0;
}

int listUserHistory(const char* message, int fdClient) {
    int idMessage = -1;

    if (strncmp(message, "LISTHIST:", 9) == 0) {
        if (sscanf(message + 9, "%d", &idMessage) != 1) {
            sendErrorMessage(fdClient, ERROR_INCORRECT_FORMAT, "Mauvais format de requête\n");
            return 1;
        }
    } else if (strcmp(message, "LISTHIST") != 0) {
        return 0;
    }

    PGconn *connexion = getConnection();
    if (!connexion) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Connexion BDD échouée\n");
        return 1;
    }

    char requete[512];
    if (idMessage == -1) {
        snprintf(requete, sizeof(requete),
            "SELECT id_emeteur, id_receveur, date_envoi, date_modif, contenu "
            "FROM sae._message WHERE (id_emeteur = %d OR id_receveur = %d) "
            "AND supprime = FALSE "
            "ORDER BY date_envoi DESC LIMIT %d;",
            idClient, idClient, tailleBlocHistorique);
    } else {
        snprintf(requete, sizeof(requete),
            "SELECT id_emeteur, id_receveur, date_envoi, date_modif, contenu "
            "FROM sae._message WHERE (id_emeteur = %d OR id_receveur = %d) "
            "AND supprime = FALSE AND id_message <= %d "
            "ORDER BY date_envoi DESC LIMIT %d;",
            idClient, idClient, idMessage, tailleBlocHistorique);
    }

    PGresult *resultatReq = PQexec(connexion, requete);
    if (PQresultStatus(resultatReq) != PGRES_TUPLES_OK) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de récupération de l'historique\n");
        PQclear(resultatReq);
        PQfinish(connexion);
        return 1;
    }

    int nombreLignes = PQntuples(resultatReq);
    sendErrorMessage(fdClient, SUCCESS_OK, "\n");

    for (int i = 0; i < nombreLignes; i++) {
        int idExpediteur = atoi(PQgetvalue(resultatReq, i, 0));
        int idDestinataire = atoi(PQgetvalue(resultatReq, i, 1));
        char *dateEnvoi = PQgetvalue(resultatReq, i, 2);
        char *dateModification = PQgetvalue(resultatReq, i, 3);
        char *contenu = PQgetvalue(resultatReq, i, 4);

        char *emailExpediteur = getEmailFromId(idExpediteur);
        char *emailDestinataire = getEmailFromId(idDestinataire);

        if (emailExpediteur && emailDestinataire) {
            char tamponMessage[1024];
            snprintf(tamponMessage, sizeof(tamponMessage), 
                     "msg:%s,%s,%s,%s,%s\n", emailExpediteur, emailDestinataire, dateEnvoi, dateModification, contenu);
            sendMessage(fdClient, tamponMessage);
        }

        free(emailExpediteur);
        free(emailDestinataire);
    }

    sendMessage(fdClient, "msgend\n");

    PQclear(resultatReq);
    PQfinish(connexion);
    return 1;
}

int listUserMessage(const char* message, int fdClient) {
    if (strcmp(message, "LISTMSG") != 0) {
        return 0;
    }

    PGconn *connexion = getConnection();
    if (!connexion) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Connexion BDD échouée\n");
        return 1;
    }

    char requete[256];
    snprintf(requete, sizeof(requete),
        "SELECT id_message, id_emeteur, id_receveur, contenu FROM sae._message "
        "WHERE id_receveur = %d AND lu = FALSE AND supprime = FALSE;", idClient);

    PGresult *resultatReq = PQexec(connexion, requete);

    if (PQresultStatus(resultatReq) != PGRES_TUPLES_OK) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de récupération des messages\n");
        PQclear(resultatReq);
        PQfinish(connexion);
        return 1;
    }

    int nombreLignes = PQntuples(resultatReq);
    sendErrorMessage(fdClient, SUCCESS_OK, ":");

    for (int i = 0; i < nombreLignes; i++) {
        int idMsg = atoi(PQgetvalue(resultatReq, i, 0));
        int idExpediteur = atoi(PQgetvalue(resultatReq, i, 1));
        int idDestinataire = atoi(PQgetvalue(resultatReq, i, 2));
        char *contenu = PQgetvalue(resultatReq, i, 3);

        char *emailExpediteur = getEmailFromId(idExpediteur);
        char *emailDestinataire = getEmailFromId(idDestinataire);

        if (emailExpediteur && emailDestinataire) {
            char tamponMessage[1024];
            snprintf(tamponMessage, sizeof(tamponMessage), "msg:%s,%s,%s", emailExpediteur, emailDestinataire, contenu);
            sendMessage(fdClient, tamponMessage);
        }

        char requeteMaj[256];
        snprintf(requeteMaj, sizeof(requeteMaj), 
                 "UPDATE sae._message SET lu = TRUE WHERE id_message = %d;", idMsg);
        PGresult *resultatMaj = PQexec(connexion, requeteMaj);

        if (PQresultStatus(resultatMaj) != PGRES_COMMAND_OK) {
            sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de mise à jour du message (lu)\n");
        }

        PQclear(resultatMaj);

        free(emailExpediteur);
        free(emailDestinataire);
    }

    sendMessage(fdClient, "msgend"); 

    PQclear(resultatReq);
    PQfinish(connexion);
    return 1;
}


int deleteUserMessage(const char* message, int fdClient) {
    int idMsg;
    
    if (sscanf(message, "DLTMSG:%d", &idMsg) != 1) {
        return 0;
    }

    PGconn *connexion = getConnection();
    if (!connexion) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Connexion BDD échouée\n");
        return 1;
    }

    int estEmetteur = isMessageFromSender(idMsg, idClient);
    int estAdmin = (idClient == -2);

    if (!estEmetteur && !estAdmin) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Suppression non autorisée\n");
        PQfinish(connexion);
        return 1;
    }

    if (!deleteMessage(idMsg)) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de suppression du message\n");
        PQfinish(connexion);
        return 1;
    }

    sendErrorMessage(fdClient, SUCCESS_OK, "Message supprimé\n");
    PQfinish(connexion);
    return 1;
}


int modifyUserMessage(const char* message, int fdClient) {
    if (strncmp(message, "MDFMSG:", 7) != 0) {
        return 0; 
    }

    int idMsg;
    char nouveauMsg[tailleMaxMessage + 1]; 
    char formatChaine[50];

    snprintf(formatChaine, sizeof(formatChaine), "%%d,%%%d[^\n]", MAX_INT);

    if (sscanf(message + 7, formatChaine, &idMsg, nouveauMsg) < 2) {
        sendErrorMessage(fdClient, ERROR_INCORRECT_FORMAT, "Mauvais format de requête\n");
        return 1;
    }
    if (strlen(nouveauMsg) > tailleMaxMessage) {
        sendErrorMessage(fdClient, ERROR_BAD_REQUEST, "Message trop long\n");
        return 1;
    }

    if (!isMessageFromSender(idMsg, idClient)) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Modification non autorisée\n");
        return 1;
    }

    if (!updateMessage(idMsg, nouveauMsg)) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de modification du message\n");
        return 1;
    }

    sendErrorMessage(fdClient, SUCCESS_OK, "Message modifié\n");
    return 1;
}



int sendUserMessage(const char* message, int fdClient) {
    if (strncmp(message, "MSG:", 4) != 0) {
        return 0;
    }

    const char *donneesMessage = message + 4;
    while (*donneesMessage == ' ') donneesMessage++;  

    int idDestinataire;
    char contenuMessage[tailleMaxMessage + 1]; 
    char formatChaine[50];  

    snprintf(formatChaine, sizeof(formatChaine), "%%d,%%%d[^\n]", MAX_INT);

    if (sscanf(donneesMessage, formatChaine, &idDestinataire, contenuMessage) < 2) {
        sendErrorMessage(fdClient, ERROR_INCORRECT_FORMAT, "Mauvais format de requête\n");
        return 1;
    }
    if (strlen(contenuMessage) > tailleMaxMessage) {
        sendErrorMessage(fdClient, ERROR_BAD_REQUEST, "Message trop long\n");
        return 1;
    }

    if (isBlockedOrBanned(idDestinataire) == 1) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Utilisateur bloqué ou banni\n");
        return 1;
    }
    int idExpediteur = idClient;

    int idMessage = createMessage(idExpediteur, idDestinataire, contenuMessage);
    if (idMessage == 0) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de l'envoi du message\n");
        return 1;
    }

    char reponse[50];
    snprintf(reponse, sizeof(reponse), "%s:%d", SUCCESS_OK, idMessage);
    sendMessage(fdClient, reponse);
    return 1;
}

int listUsers(const char* message, int fdClient) {
    if (strcmp(message, "GETUSERS") != 0) {
        return 0;
    }

    PGconn *connexion = getConnection();
    if (!connexion) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Connexion BDD échouée\n");
        return 1;
    }

    PGresult *resultatReq = getUsersList(estClientPro);
    if (!resultatReq) {
        sendErrorMessage(fdClient, ERROR_INTERNAL_ERROR, "Échec de récupération des utilisateurs\n");
        PQfinish(connexion);
        return 1;
    }

    int nombreLignes = PQntuples(resultatReq);
    sendErrorMessage(fdClient, SUCCESS_OK, "\n");

    for (int i = 0; i < nombreLignes; i++) {
        char tamponUtilisateur[256];

        if (estClientPro) {
            snprintf(tamponUtilisateur, sizeof(tamponUtilisateur), "user:%s,%s\n", 
                     PQgetvalue(resultatReq, i, 0), PQgetvalue(resultatReq, i, 1));
        } else {
            snprintf(tamponUtilisateur, sizeof(tamponUtilisateur), "user:%s,%s,%s\n", 
                     PQgetvalue(resultatReq, i, 0), PQgetvalue(resultatReq, i, 1), PQgetvalue(resultatReq, i, 2));
        }
        printf("%s", tamponUtilisateur);
        sendMessage(fdClient, tamponUtilisateur);
    }
    sendMessage(fdClient, "userend\n");

    PQclear(resultatReq);
    PQfinish(connexion);
    return 1;
}


int login(const char* message, int fdClient, int *idClient, int *estClientPro)  {
    if (strncmp(message, "LOGIN:", 6) != 0) {
        return 0;
    }
    
    const char *cleApi = message + 6;
    while (*cleApi == ' ') {
        cleApi++;
    }
    
    if (strlen(cleApi) == 0) {
        sendErrorMessage(fdClient, ERROR_BAD_REQUEST, "Clé d'API manquante\n");
        return 1;
    }
    
    if (strcmp(cleApi, cleApiAdmin) == 0) {
        *idClient = -2;
        *estClientPro = 1;
        sendErrorMessage(fdClient, SUCCESS_OK, "Connection Admin\n");
        return 1;
    }
    
    if (!loginWithKey(cleApi, estClientPro, idClient)) {
        *idClient = -1;
        sendErrorMessage(fdClient, ERROR_NOT_LOGIN, "Connexion échouée\n");
        return 1;
    }
    
    sendErrorMessage(fdClient, SUCCESS_OK, "Connexion réussie\n");
    return 1;
}

int quitServer(const char* message, int fdClient, int *idClient, int *estClientPro) {
    if (strcmp(message, "QUIT") != 0) {
        return 0; 
    }

    *idClient = -1;
    *estClientPro = 0;

    sendMessage(fdClient, SUCCESS_OK);

    close(fdClient);
    printf("Client disconnected.\n");

    return -1; 
}



void logInFile(int niveau, const char* texte) {
    if (estVerbeux) {
        printf("%s\n", texte);
    }

    const char* prefixNiveau;
    FILE* fichier;

    switch (niveau) {
        case 0:
            prefixNiveau = "[INFO]";
            break;
        case 1:
            prefixNiveau = "[WARN]";
            break;
        case 2:
            prefixNiveau = "[ERROR]";
            break;
        default:
            prefixNiveau = "[UNKNOWN]";
    }

    fichier = fopen(cheminFichierLog, "a");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier de log");
        exit(EXIT_FAILURE);
    }

    char* dateHeure = getDateHeure();

    fprintf(fichier, "%s[%s]: %s\n", prefixNiveau, dateHeure, texte);

    free(dateHeure);
    fclose(fichier);
}


char* getDateHeure() {
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);

    char* dateHeure = malloc(72 * sizeof(char)); 
    if (dateHeure == NULL) {
        perror("Erreur d'allocation de mémoire");
        exit(EXIT_FAILURE);
    }

    snprintf(dateHeure, 72, "%04d-%02d-%02d:%02d:%02d:%02d",
             tm.tm_year + 1900,
             tm.tm_mon + 1,
             tm.tm_mday,
             tm.tm_hour,
             tm.tm_min,
             tm.tm_sec);

    return dateHeure;
}

void help() {
    printf("Usage: tchatator [OPTIONS]\n");
    printf("Options disponibles :\n");
    printf("  --help        Affiche cette aide et quitte.\n");
    printf("  --verbose     Active le mode verbeux.\n");
    printf("  --test        Fait des tests.\n");
    printf("\nPour plus d'informations veuillez consulter Protocole.md ou Config.cfg\n");
}

void logPunishment(const char *action, int idEmetteur, int idCompte) {
    FILE *fichier = fopen(PUNISHMENT_FILE, "a");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier punishment.txt");
        exit(EXIT_FAILURE);
    }

    char *dateHeure = getDateHeure();
    fprintf(fichier, "[%s]:%s,%d,%d\n", dateHeure, action, idEmetteur, idCompte);

    free(dateHeure);
    fclose(fichier);
}

int canModerate(int idCompte) {
    return (idCompte == -2 || isProfessional(idCompte));
}
int blockUser(const char* message, int fdClient) {
    
    int idCompte;
    if (sscanf(message, "BLOCKUSR:%d", &idCompte) != 1) {
        return 0; 
    }

    if (!canModerate(idClient)) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Action non autorisée\n");
        return 1; 
    }

    logPunishment("block", idClient, idCompte);
    sendErrorMessage(fdClient, SUCCESS_OK, "Utilisateur bloqué\n");
    return 1;
}

int unblockUser(const char* message, int fdClient) {
    int idCompte;
    if (sscanf(message, "UNBLOCKUSR:%d", &idCompte) != 1) {
        return 0;
    }

    if (!canModerate(idClient)) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Action non autorisée\n");
        return 1;
    }

    logPunishment("unblock", idClient, idCompte);
    sendErrorMessage(fdClient, SUCCESS_OK, "Utilisateur débloqué\n");
    return 1;
}

int banUser(const char* message, int fdClient) {
    int idCompte;
    if (sscanf(message, "BANUSR:%d", &idCompte) != 1) {
        return 0;
    }
    if (!canModerate(idClient)) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Action non autorisée\n");
        return 1;
    }

    logPunishment("ban", idClient, idCompte);
    sendErrorMessage(fdClient, SUCCESS_OK, "Utilisateur banni\n");
    return 1;
}

int unbanUser(const char* message, int fdClient) {
    int idCompte;
    if (sscanf(message, "UNBANUSR:%d", &idCompte) != 1) {
        return 0;
    }

    if (!canModerate(idClient)) {
        sendErrorMessage(fdClient, ERROR_FORBIDDEN, "Action non autorisée\n");
        return 1;
    }

    logPunishment("unban", idClient, idCompte);
    sendErrorMessage(fdClient, SUCCESS_OK, "Utilisateur débanni\n");
    return 1;
}

time_t parseDateTime(const char *datetime) {
    struct tm t = {0};
    if (sscanf(datetime, "%d-%d-%d:%d:%d:%d", &t.tm_year, &t.tm_mon, &t.tm_mday,
               &t.tm_hour, &t.tm_min, &t.tm_sec) != 6) {
        return -1;
    }
    t.tm_year -= 1900;
    t.tm_mon -= 1;
    return mktime(&t);
}
int isBlockedOrBanned(int idDestinataire) {
    if (idClient == -2) {
        return 0;
    }

    FILE *fichier = fopen(PUNISHMENT_FILE, "r");
    if (!fichier) {
        perror("Erreur lors de l'ouverture du fichier punishment.txt");
        return -1;
    }

    char ligne[128];
    char action[10];
    int idEmetteur, idCible;
    char dateHeure[20];
    
    time_t maintenant = time(NULL);

    while (fgets(ligne, sizeof(ligne), fichier)) {
        if (sscanf(ligne, "[%19[^]]]:%9[^,],%d,%d", dateHeure, action, &idEmetteur, &idCible) != 4) {
            continue; 
        }

        time_t tempsAction = parseDateTime(dateHeure);
        if (tempsAction == -1) continue;

        int duree = 0;
        if (strcmp(action, "block") == 0) duree = dureeBlocage * 3600;
        if (strcmp(action, "ban") == 0) duree = (dureeBanissement == 0) ? -1 : dureeBanissement * 3600;

        if (strcmp(action, "ban") == 0) {
            if (idEmetteur == -2 && idCible == idClient ) { 
                fclose(fichier);
                return 1;
            }
            if (idCible == idClient  && idEmetteur == idDestinataire) { 
                if (duree == -1 || (maintenant - tempsAction) <= duree) {
                    fclose(fichier);
                    return 1;
                }
            }
        }

        if (strcmp(action, "block") == 0 && idCible == idClient  && idEmetteur == idDestinataire) {
            if ((maintenant - tempsAction) <= duree) {
                fclose(fichier);
                return 1;
            }
        }

        if ((strcmp(action, "unblock") == 0 || strcmp(action, "unban") == 0) && idCible == idClient ) {
            if (idEmetteur == idDestinataire || idEmetteur == -2) {
                fclose(fichier);
                return 0;
            }
        }
    }

    fclose(fichier);
    return 0;
}

void logInFileWithIp(int niveau, const char* ipClient, const char* texte) {
    char messageLog[1024]; 

    snprintf(messageLog, sizeof(messageLog), "[%s] %s", ipClient, texte);

    logInFile(niveau, messageLog);
}

bool isSpamming() {
    FILE *fichier = fopen(cheminFichierLog, "r");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier de log");
        return false; 
    }

    time_t maintenant = time(NULL);

    int compteurDerniereMinute = 0;
    int compteurDerniereHeure = 0;
    char ligne[1024];

    while (fgets(ligne, sizeof(ligne), fichier)) {
        int anneeLog, moisLog, jourLog, heureLog, minuteLog, secondeLog;
        char ipLog[BUFFER_SIZE];

        if (sscanf(ligne, "[INFO][%d-%d-%d:%d:%d:%d]: [%255[^]]]%*[^\n]", 
                   &anneeLog, &moisLog, &jourLog, 
                   &heureLog, &minuteLog, &secondeLog, ipLog) == 7) {
            if (strcmp(ipLog, ipClient) == 0) {
                struct tm tempsLog = {0};
                tempsLog.tm_year = anneeLog - 1900;
                tempsLog.tm_mon = moisLog - 1;
                tempsLog.tm_mday = jourLog;
                tempsLog.tm_hour = heureLog;
                tempsLog.tm_min = minuteLog;
                tempsLog.tm_sec = secondeLog;

                time_t timestampLog = mktime(&tempsLog);
                double diffSecondes = difftime(maintenant, timestampLog);
                if (diffSecondes >= 0 && diffSecondes <= 60) {
                    compteurDerniereMinute++;
                }
                if (diffSecondes >= 0 && diffSecondes <= 3600) {
                    compteurDerniereHeure++;
                }
            }
        }
    }
    fclose(fichier);

    return (compteurDerniereMinute >= nbRequetesMaxParMinute || compteurDerniereHeure >= nbRequetesMaxParHeure);
}
void sendMessage(int fdClient, const char* message) {
    write(fdClient, message, strlen(message));
}
void sendErrorMessage(int fdClient, const char* codeErreur, const char* texte) {
    char tampon[256];
    strcpy(tampon, codeErreur);
    strcat(tampon, ": ");
    strcat(tampon, texte);
    sendMessage(fdClient, tampon);
}


void test() {
    printf("Début des tests\n");
    //bdd

    PGconn *connexion = getConnection();
    if (!connexion) {
        printf("Test connexion BDD: Echec\n");
    } else {
        printf("Test connexion BDD: OK\n");
        PQfinish(connexion);
    }
    // test  date et heure
    char *dateHeureStr = getDateHeure();
    if (dateHeureStr) {
        printf("Test getDateHeure: %s\n", dateHeureStr);
        int annee, mois, jour, heure, minute, seconde;
        if (sscanf(dateHeureStr, "%d-%d-%d:%d:%d:%d", &annee, &mois, &jour, &heure, &minute, &seconde) == 6) {
            printf("Test getDateHeure: Format correct\n");
        } else {
            printf("Test getDateHeure: Format incorrect\n");
        }
    } else {
        printf("Test getDateHeure: Echec\n");
    }
    free(dateHeureStr);

    const char *validDateTime = "2025-02-02:12:34:56";
    time_t parsedTime = parseDateTime(validDateTime);
    if (parsedTime != -1) {
        printf("Test parseDateTime (valide): OK (%ld)\n", parsedTime);
    } else {
        printf("Test parseDateTime (valide): Echec\n");
    }

    const char *invalidDateTime = "invalid-date-format";
    parsedTime = parseDateTime(invalidDateTime);
    if (parsedTime == -1) {
        printf("Test parseDateTime (invalide): OK\n");
    } else {
        printf("Test parseDateTime (invalide): Echec\n");
    }

    // logs

    logInFile(1, "Test logInFile");
    logInFileWithIp(1, "192.168.0.1", "Test logInFileWithIp");

}