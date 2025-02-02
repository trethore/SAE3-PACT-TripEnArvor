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

int port = 12345;
int ban_duration = 0;
int block_duration = 0;
int max_requests_per_minute = 12;
int max_requests_per_hour = 90;
int max_message_size = 1000;
int queue_size = 5;
int history_block_size = 20;
char log_file_path[1024];
char admin_api_key[256];
int reload_signal= SIGHUP;
// Server
int server_fd;
bool isVerbose = false;
// client actuel

char client_ip[INET_ADDRSTRLEN];
int client_id = -1;
int isProClient = false;

// fichiers
const char *CONFIG_FILE = "config.cfg";
const char *PUNISHMENT_FILE = "punishment.log";
// extern

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

// fonctions
void help();
void getConfig();
void startSocketServer();
void stopServer(int sig);
void logInFile(int level, const char* text);
void logInFileWithIp(int level, const char* client_ip, const char* text);
char* getDateHeure();
int gereMessage(const char* message, int client_fd);
void sendMessage(int client_fd, const char* message);
void rechargeConfig(int sig);

int login(const char* message, int client_fd, int *client_id, int *isProClient) ;
int quitServer(const char* message, int client_fd, int *client_id, int *isProClient);
int listUsers(const char* message, int client_fd);
int sendUserMessage(const char* message, int client_fd);
int modifyUserMessage(const char* message, int client_fd);
int deleteUserMessage(const char* message, int client_fd);
int listUserMessage(const char* message, int client_fd);
int listUserHistory(const char* message, int client_fd);

int blockUser(const char* message, int client_fd);
int unblockUser(const char* message, int client_fd);
int banUser(const char* message, int client_fd);
int unbanUser(const char* message, int client_fd);
int isBlockedOrBanned(int receveur_id);

bool isSpamming();
// codes d'erreur
#define SUCCESS_OK "1/OK"
#define ERROR_DENIED "103/DENIED"
#define ERROR_FORBIDDEN "104/FORBIDDEN"
#define ERROR_NOT_FOUND "105/NOT_FOUND"
#define ERROR_NOT_LOGIN "107/NOLOGIN"
#define ERROR_TOO_MANY_REQUESTS "110/TOO_MANY_REQUESTS" 
#define ERROR_BAD_REQUEST "142/BAD_REQUEST"
#define ERROR_INTERNAL_ERROR "500/INTERNAL_ERROR"
#define ERROR_INCORRECT_FORMAT "116/MISFMT"

int main(int argc, char *argv[]) {
    for (int i = 1; i < argc; i++) {
        if (strcmp(argv[i], "--help") == 0) {
            help();
            return EXIT_SUCCESS;
        } else if (strcmp(argv[i], "--verbose") == 0) {
            isVerbose = true;
        }
    }
    signal(SIGINT, stopServer);
    signal(reload_signal, rechargeConfig);
    getConfig();
    printf("server file %s\n", log_file_path);
    logInFile(0, "Démarrage du serveur...");
    startSocketServer();

    return EXIT_SUCCESS;
}

void stopServer(int sig) {
    logInFile(0, "Stop...");
    printf("\nArret du Serveur\n");
    close(server_fd);
    exit(EXIT_SUCCESS);
}
void rechargeConfig(int sig) {
    printf("\nSignal reçu (%d), rechargement de la configuration...\n", sig);
    logInFile(0, "Rechargement de la configuration suite à un signal");
    getConfig();

    char log_message[256];
    snprintf(log_message, sizeof(log_message), 
             "Nouvelle configuration : port=%d, max_requests_per_minute=%d, max_requests_per_hour=%d",
             port, max_requests_per_minute, max_requests_per_hour);
    logInFile(0, log_message);
    printf("\nRechargement de la configuration terminé !\n");
}
void getConfig() {
    const char *filename = CONFIG_FILE;
    FILE *file = fopen(filename, "r");
    if (!file) {
        logInFile(2, "Erreur ouverture config");
        perror("Erreur lors de l'ouverture du fichier de configuration");
        exit(EXIT_FAILURE);
    }

    char line[256];
    while (fgets(line, sizeof(line), file)) {
        if (line[0] == '#' || line[0] == '\n') continue;

        char key[128];
        char value[128];

        if (sscanf(line, "%127[^=]=%127s", key, value) == 2) {
            if (strcmp(key, "port") == 0) {
                port = atoi(value);
            } else if (strcmp(key, "ban_duration") == 0) {
                ban_duration = atoi(value);
            } else if (strcmp(key, "block_duration") == 0) {
                block_duration = atoi(value);
            } else if (strcmp(key, "max_requests_per_minute") == 0) {
                max_requests_per_minute = atoi(value);
            } else if (strcmp(key, "max_requests_per_hour") == 0) {
                max_requests_per_hour = atoi(value);
            } else if (strcmp(key, "max_message_size") == 0) {
                max_message_size = atoi(value);
            } else if (strcmp(key, "queue_size") == 0) {
                queue_size = atoi(value);
            } else if (strcmp(key, "history_block_size") == 0) {
                history_block_size = atoi(value);
            } else if (strcmp(key, "log_file_path") == 0) {
                strncpy(log_file_path, value, sizeof(log_file_path) - 1);
                log_file_path[sizeof(log_file_path) - 1] = '\0';
            } else if (strcmp(key, "admin_api_key") == 0) {
                strncpy(admin_api_key, value, sizeof(admin_api_key) - 1);
            } else if (strcmp(key, "reload_signal") == 0) {
                reload_signal = atoi(value);
            } else {
                fprintf(stderr, "Cle inconnue : %s\n", key);
            }
        }
    }

    fclose(file);
}


void startSocketServer() {
    struct sockaddr_in server_addr, client_addr;
    socklen_t client_addr_len = sizeof(client_addr);
    char buffer[BUFFER_SIZE];

    server_fd = socket(AF_INET, SOCK_STREAM, 0);
    if (server_fd == -1) {
        perror("Erreur lors de la création du socket");
        exit(EXIT_FAILURE);
    }

    memset(&server_addr, 0, sizeof(server_addr));
    server_addr.sin_family = AF_INET;
    server_addr.sin_addr.s_addr = INADDR_ANY;
    server_addr.sin_port = htons(port);
    
    int opt = 1;
    setsockopt(server_fd, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));

    if (bind(server_fd, (struct sockaddr*)&server_addr, sizeof(server_addr)) == -1) {
        perror("Erreur lors du bind");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    if (listen(server_fd, queue_size) == -1) {
        perror("Erreur lors du listen)");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    printf("Serveur démarré sur le port %d avec une file d'attente de %d\n", port, queue_size);
    logInFile(0, "Serveur démarré !");
    while (1) {
        printf("En attente de connexions\n");

        int client_fd = accept(server_fd, (struct sockaddr*)&client_addr, &client_addr_len);
        if (client_fd == -1) {
            perror("Erreur lors de l'acceptation de la connexion");
            continue;
        }

        if (inet_ntop(AF_INET, &client_addr.sin_addr, client_ip, INET_ADDRSTRLEN) != NULL) { // recuperation de l'adresse IP dans client_address
            printf("Connexion acceptée depuis : %s\n", client_ip);
            logInFile(0,strcat("Connexion acceptée depuis : ", client_ip));
        } else {
            perror("Erreur lors de la récupération de l'adresse IP du client");
            close(client_fd);
            continue;
        }

        while (1) {
            memset(buffer, 0, BUFFER_SIZE); 

            ssize_t bytes_received = recv(client_fd, buffer, BUFFER_SIZE - 1, 0);
            if (bytes_received > 0) {
                buffer[bytes_received] = '\0';
                printf("Message reçu de %s : %s\n", client_ip, buffer);
                logInFileWithIp(0, client_ip, buffer);
                if (gereMessage(buffer, client_fd) == -1) {
                    break;
                }
            } else if (bytes_received == 0) {
                printf("Client %s s'est déconnecté.\n", client_ip);
                client_id = -1;
                isProClient = 0;
                break;
            } else {
                perror("Erreur lors de la réception des données");
                break;
            }
        }

        close(client_fd); 
    }
}

int gereMessage(const char* message, int client_fd) {
    int result = 0;

    if (quitServer(message, client_fd, &client_id, &isProClient) == -1) {
        return -1;
    }
    if (isSpamming()) {
        sendMessage(client_fd, strcat(ERROR_TOO_MANY_REQUESTS,": Trop de requetes\n"));
        return 1;
    }
     
    result |= login(message, client_fd, &client_id, &isProClient);
    if (client_id == -1) {
        if (result == 1) {
            sendMessage(client_fd, strcat(ERROR_NOT_LOGIN, ": Veuilley vous connecter\n"));
        } else {
            sendMessage(client_fd,  strcat(ERROR_BAD_REQUEST, ": Mauvaise requete\n"));
        }
        return 0;
    }

    result |= listUsers(message, client_fd);
    result |= sendUserMessage(message, client_fd);
    result |= modifyUserMessage(message, client_fd);
    result |= deleteUserMessage(message, client_fd);
    result |= listUserMessage(message, client_fd);
    result |= listUserHistory(message, client_fd);
    result |= blockUser(message, client_fd);
    result |= unblockUser(message, client_fd);
    result |= banUser(message, client_fd);
    result |= unbanUser(message, client_fd);

    if (!result) {
        sendMessage(client_fd,  strcat(ERROR_BAD_REQUEST, ": Mauvaise requete\n"));
    }
    return 0;

}

int listUserHistory(const char* message, int client_fd) {
    int message_id = -1;

    if (strncmp(message, "LISTHIST:", 9) == 0) {
        if (sscanf(message + 9, "%d", &message_id) != 1) {
            sendMessage(client_fd,  strcat(ERROR_INCORRECT_FORMAT, ": Mauvais format de requete\n"));
            return 1;
        }
    } else if (strcmp(message, "LISTHIST") != 0) {
        return 0;
    }

    PGconn *conn = getConnection();
    if (!conn) {
        sendMessage(client_fd,  strcat(ERROR_INTERNAL_ERROR, ": Connexion BDD échouée\n"));
        return 1;
    }

    char query[512];
    if (message_id == -1) {
        snprintf(query, sizeof(query),
            "SELECT id_emeteur, id_receveur, date_envoi, date_modif, contenu "
            "FROM sae._message WHERE (id_emeteur = %d OR id_receveur = %d) "
            "AND supprime = FALSE "
            "ORDER BY date_envoi DESC LIMIT %d;",
            client_id, client_id, history_block_size);
    } else {
        snprintf(query, sizeof(query),
            "SELECT id_emeteur, id_receveur, date_envoi, date_modif, contenu "
            "FROM sae._message WHERE (id_emeteur = %d OR id_receveur = %d) "
            "AND supprime = FALSE AND id_message <= %d "
            "ORDER BY date_envoi DESC LIMIT %d;",
            client_id, client_id, message_id, history_block_size);
    }

    PGresult *res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de récupération des messages\n"));
        PQclear(res);
        PQfinish(conn);
        return 1;
    }

    int num_rows = PQntuples(res);
    sendMessage(client_fd, strcat(SUCCESS_OK, ":"));

    for (int i = 0; i < num_rows; i++) {
        int sender_id = atoi(PQgetvalue(res, i, 0));
        int receiver_id = atoi(PQgetvalue(res, i, 1));
        char *date_envoi = PQgetvalue(res, i, 2);
        char *date_modif = PQgetvalue(res, i, 3);
        char *content = PQgetvalue(res, i, 4);

        char *sender_email = getEmailFromId(sender_id);
        char *receiver_email = getEmailFromId(receiver_id);

        if (sender_email && receiver_email) {
            char msg_buffer[1024];
            snprintf(msg_buffer, sizeof(msg_buffer), 
                     "msg:%s,%s,%s,%s,%s", sender_email, receiver_email, date_envoi, date_modif, content);
            sendMessage(client_fd, msg_buffer);
        }

        free(sender_email);
        free(receiver_email);
    }

    sendMessage(client_fd, "msgend");

    PQclear(res);
    PQfinish(conn);
    return 1;
}

int listUserMessage(const char* message, int client_fd) {
    if (strcmp(message, "LISTMSG") != 0) {
        return 0;
    }

    PGconn *conn = getConnection();
    if (!conn) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Connexion BDD échouée\n"));
        return 1;
    }

    char query[256];
    snprintf(query, sizeof(query),
        "SELECT id_message, id_emeteur, id_receveur, contenu FROM sae._message "
        "WHERE id_receveur = %d AND lu = FALSE AND supprime = FALSE;", client_id);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de récupération des messages\n"));
        PQclear(res);
        PQfinish(conn);
        return 1;
    }

    int num_rows = PQntuples(res);
    sendMessage(client_fd, strcat(SUCCESS_OK, ":")); 

    for (int i = 0; i < num_rows; i++) {
        int msg_id = atoi(PQgetvalue(res, i, 0));
        int sender_id = atoi(PQgetvalue(res, i, 1));
        int receiver_id = atoi(PQgetvalue(res, i, 2));
        char *content = PQgetvalue(res, i, 3);

        char *sender_email = getEmailFromId(sender_id);
        char *receiver_email = getEmailFromId(receiver_id);

        if (sender_email && receiver_email) {
            char msg_buffer[1024];
            snprintf(msg_buffer, sizeof(msg_buffer), "msg:%s,%s,%s", sender_email, receiver_email, content);
            sendMessage(client_fd, msg_buffer);
        }

        char update_query[256];
        snprintf(update_query, sizeof(update_query), 
                 "UPDATE sae._message SET lu = TRUE WHERE id_message = %d;", msg_id);
        PGresult *update_res = PQexec(conn, update_query);

        if (PQresultStatus(update_res) != PGRES_COMMAND_OK) {
            sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de mise à jour du message (lu)\n"));
        }

        PQclear(update_res);

        free(sender_email);
        free(receiver_email);
    }

    sendMessage(client_fd, "msgend"); 

    PQclear(res);
    PQfinish(conn);
    return 1;
}


int deleteUserMessage(const char* message, int client_fd) {
    int msgid;
    
    if (sscanf(message, "DLTMSG:%d", &msgid) != 1) {
        return 0;
    }

    PGconn *conn = getConnection();
    if (!conn) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Connexion BDD échouée\n"));
        return 1;
    }

    int is_sender = isMessageFromSender(msgid, client_id);
    int is_admin = (client_id == -2);

    if (!is_sender && !is_admin) {
        sendMessage(client_fd, strcat(ERROR_FORBIDDEN, ": Suppression non autorisée\n"));
        PQfinish(conn);
        return 1;
    }

    if (!deleteMessage(msgid)) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de suppression du message\n"));
        PQfinish(conn);
        return 1;
    }

    sendMessage(client_fd, strcat(SUCCESS_OK, ": Message supprimé\n"));
    PQfinish(conn);
    return 1;
}


int modifyUserMessage(const char* message, int client_fd) {
    if (strncmp(message, "MDFMSG:", 7) != 0) {
        return 0; 
    }


    int msgid;
    char newmsg[max_message_size + 1]; 
    char formatString[50];

    snprintf(formatString, sizeof(formatString), "%%d,%%%d[^\n]", 2147483647);

    if (sscanf(message + 7, formatString, &msgid, newmsg) < 2) {
        sendMessage(client_fd, strcat(ERROR_INCORRECT_FORMAT, ": Mauvais format de requete\n"));
        return 1;
    }
    if (strlen(newmsg) > max_message_size) {
        sendMessage(client_fd, strcat(ERROR_INCORRECT_FORMAT, ": Message trop long\n"));
        return 1;
    }

    if (!isMessageFromSender(msgid, client_id)) {
        sendMessage(client_fd, strcat(ERROR_FORBIDDEN, ": Modification non autorisée\n"));
        return 1;
    }

    if (!updateMessage(msgid, newmsg)) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de modification du message\n"));
        return 1;
    }

    sendMessage(client_fd, strcat(SUCCESS_OK, ": Message modifié\n"));
    return 1;
}



int sendUserMessage(const char* message, int client_fd) {
    if (strncmp(message, "MSG:", 4) != 0) {
        return 0;
    }

    const char *msgData = message + 4;
    while (*msgData == ' ') msgData++;  

    int recepteur_id;
    char msgContent[max_message_size + 1]; 
    char formatString[50];  

    snprintf(formatString, sizeof(formatString), "%%d,%%%d[^\n]", max_message_size);

    if (sscanf(msgData, formatString, &recepteur_id, msgContent) < 2) {
        sendMessage(client_fd, strcat(ERROR_INCORRECT_FORMAT, ": Mauvais format de requete\n"));
        return 1;
    }
    if (isBlockedOrBanned(recepteur_id) == 1) {
        sendMessage(client_fd,  strcat(ERROR_FORBIDDEN, ": Utilisateur bloqué ou banni\n"));
        return 1;
    }
    int emetteur_id = client_id;

    int messageId = createMessage(emetteur_id, recepteur_id, msgContent);
    if (messageId == 0) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de l'envoi du message\n"));
        return 1;
    }

    char response[50];
    snprintf(response, sizeof(response), "%s:%d",SUCCESS_OK, messageId);
    sendMessage(client_fd, response);
    return 1;
}

int listUsers(const char* message, int client_fd) {
    if (strcmp(message, "GETUSERS") != 0) {
        return 0;
    }

    PGconn *conn = getConnection();
    if (!conn) {
        sendMessage(client_fd,strcat(ERROR_INTERNAL_ERROR, ": Connexion BDD échouée\n"));
        return 1;
    }

    PGresult *res = getUsersList(isProClient);
    if (!res) {
        sendMessage(client_fd, strcat(ERROR_INTERNAL_ERROR, ": Échec de récupération des utilisateurs\n"));
        PQfinish(conn);
        return 1;
    }

    int num_rows = PQntuples(res);
    sendMessage(client_fd, strcat(SUCCESS_OK, ":")); 

    for (int i = 0; i < num_rows; i++) {
        char user_buffer[256];

        if (isProClient) {
            snprintf(user_buffer, sizeof(user_buffer), "user:%s,%s\n", 
                     PQgetvalue(res, i, 0), PQgetvalue(res, i, 1));
        } else {
            snprintf(user_buffer, sizeof(user_buffer), "user:%s,%s,%s\n", 
                     PQgetvalue(res, i, 0), PQgetvalue(res, i, 1), PQgetvalue(res, i, 2));
        }
        sendMessage(client_fd, user_buffer);
    }
    sendMessage(client_fd, "userend\n");

    PQclear(res);
    PQfinish(conn);
    return 1;
}


int login(const char* message, int client_fd, int *client_id, int *isProClient)  {
    if (strncmp(message, "LOGIN:", 6) != 0) {
        return 0;
    }
    
    const char *apiKey = message + 6;
    while (*apiKey == ' ') {
        apiKey++;
    }
    
    if (strlen(apiKey) == 0) {
        sendMessage(client_fd, strcat(ERROR_DENIED, ": Mauvais format de clé\n"));
        return 1;
    }
    
    if (strcmp(apiKey, admin_api_key) == 0) {
        *client_id = -2;
        *isProClient = 1;
        sendMessage(client_fd, strcat(SUCCESS_OK, ": Admin connecté\n"));
        return 1;
    }
    
    if (!loginWithKey(apiKey, isProClient, client_id)) {
        *client_id = -1;
        sendMessage(client_fd, strcat(ERROR_DENIED, ": Clé invalide\n"));
        return 1;
    }
    
    char response[50];
    snprintf(response, sizeof(response), "%s:%d,%d",SUCCESS_OK ,*client_id, *isProClient);
    sendMessage(client_fd, response);
    return 1;
}

int quitServer(const char* message, int client_fd, int *client_id, int *isProClient) {
    if (strcmp(message, "QUIT") != 0) {
        return 0; 
    }

    *client_id = -1;
    *isProClient = 0;

    sendMessage(client_fd, SUCCESS_OK);

    close(client_fd);
    printf("Client disconnected.\n");

    return -1; 
}



void logInFile(int level, const char* text) {
    if (isVerbose) {
        printf("%s\n", text);
    }

    const char* level_prefix;
    FILE* file;

    switch (level) {
        case 0:
            level_prefix = "[INFO]";
            break;
        case 1:
            level_prefix = "[WARN]";
            break;
        case 2:
            level_prefix = "[ERROR]";
            break;
        default:
            level_prefix = "[UNKNOWN]";
    }

    file = fopen(log_file_path, "a");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier de log");
        exit(EXIT_FAILURE);
    }

    char* date_heure = getDateHeure();

    fprintf(file, "%s[%s]: %s\n", level_prefix, date_heure, text);

    free(date_heure);
    fclose(file);
}


char* getDateHeure() {
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);

    char* date_heure = malloc(72 * sizeof(char)); 
    if (date_heure == NULL) {
        perror("Erreur d'allocation de mémoire");
        exit(EXIT_FAILURE);
    }

    snprintf(date_heure, 72, "%04d-%02d-%02d:%02d:%02d:%02d",
             tm.tm_year + 1900,
             tm.tm_mon + 1,
             tm.tm_mday,
             tm.tm_hour,
             tm.tm_min,
             tm.tm_sec);

    return date_heure;
}

void help() {
    printf("Usage: tchatator [OPTIONS]\n");
    printf("Options disponibles :\n");
    printf("  --help        Affiche cette aide et quitte.\n");
    printf("  --verbose     Active le mode verbeux.\n");
    printf("\nPour plus d'informations veuillez consulter Protocole.md ou Config.cfg\n");
}

void logPunishment(const char *action, int emetteur_id, int compte_id) {
    FILE *file = fopen("punishment.txt", "a");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier punishment.txt");
        exit(EXIT_FAILURE);
    }

    char *date_heure = getDateHeure();
    fprintf(file, "[%s]:%s,%d,%d\n", date_heure, action, emetteur_id, compte_id);

    free(date_heure);
    fclose(file);
}

int canModerate(int compte_id) {
    return (compte_id == -2 || isProfessional(compte_id));
}
int blockUser(const char* message, int client_id) {
    
    int compte_id;
    if (sscanf(message, "BLOCKUSR:%d", &compte_id) != 1) {
        return 0; 
    }

    if (!canModerate(client_id)) {
        return 1; 
    }

    logPunishment("block", client_id, compte_id);
    return 1;
}

int unblockUser(const char* message, int client_id) {
    int compte_id;
    if (sscanf(message, "UNBLOCKUSR:%d", &compte_id) != 1) {
        return 0;
    }

    if (!canModerate(client_id)) {
        return 1;
    }

    logPunishment("unblock", client_id, compte_id);
    return 1;
}

int banUser(const char* message, int client_id) {
    int compte_id;
    if (sscanf(message, "BANUSR:%d", &compte_id) != 1) {
        return 0;
    }

    if (!canModerate(client_id)) {
        return 1;
    }

    logPunishment("ban", client_id, compte_id);
    return 1;
}

int unbanUser(const char* message, int client_id) {
    int compte_id;
    if (sscanf(message, "UNBANUSR:%d", &compte_id) != 1) {
        return 0;
    }

    if (!canModerate(client_id)) {
        return 1;
    }

    logPunishment("unban", client_id, compte_id);
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
int isBlockedOrBanned(int receveur_id) {
    if (client_id == -2) {
        return 0;
    }

    FILE *file = fopen(PUNISHMENT_FILE, "r");
    if (!file) {
        perror("Erreur lors de l'ouverture du fichier punishment.txt");
        return -1;
    }

    char line[128];
    char action[10];
    int emetteur, cible;
    char date_heure[20];
    
    time_t now = time(NULL);

    while (fgets(line, sizeof(line), file)) {
        if (sscanf(line, "[%19[^]]]:%9[^,],%d,%d", date_heure, action, &emetteur, &cible) != 4) {
            continue; // Ligne mal formée
        }

        time_t action_time = parseDateTime(date_heure);
        if (action_time == -1) continue;

        int duration = 0;
        if (strcmp(action, "block") == 0) duration = block_duration * 3600;
        if (strcmp(action, "ban") == 0) duration = (ban_duration == 0) ? -1 : ban_duration * 3600;

        if (strcmp(action, "ban") == 0) {
            if (emetteur == -2 && cible == client_id ) { 
                fclose(file);
                return 1;
            }
            if (cible == client_id  && emetteur == receveur_id) { 
                if (duration == -1 || (now - action_time) <= duration) {
                    fclose(file);
                    return 1;
                }
            }
        }

        if (strcmp(action, "block") == 0 && cible == client_id  && emetteur == receveur_id) {
            if ((now - action_time) <= duration) {
                fclose(file);
                return 1;
            }
        }

        if ((strcmp(action, "unblock") == 0 || strcmp(action, "unban") == 0) && cible == client_id ) {
            if (emetteur == receveur_id || emetteur == -2) {
                fclose(file);
                return 0;
            }
        }
    }

    fclose(file);
    return 0;
}

void logInFileWithIp(int level, const char* client_ip, const char* text) {
    char log_message[1024]; 

    snprintf(log_message, sizeof(log_message), "[%s] %s", client_ip, text);

    logInFile(level, log_message);
}

bool isSpamming() {
    FILE *file = fopen(log_file_path, "r");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier de log");
        return false; 
    }

    time_t now = time(NULL);
    struct tm *current_time = localtime(&now);

    int count_last_minute = 0;
    int count_last_hour = 0;
    char line[1024];

    while (fgets(line, sizeof(line), file)) {
        int log_year, log_month, log_day, log_hour, log_minute, log_second;
        char log_ip[BUFFER_SIZE];

        if (sscanf(line, "[INFO][%d-%d-%d:%d:%d:%d]: [%255[^]]]%*[^\n]", 
                   &log_year, &log_month, &log_day, 
                   &log_hour, &log_minute, &log_second, log_ip) == 7) {
            if (strcmp(log_ip, client_ip) == 0) {
                struct tm log_time = {0};
                log_time.tm_year = log_year - 1900;
                log_time.tm_mon = log_month - 1;
                log_time.tm_mday = log_day;
                log_time.tm_hour = log_hour;
                log_time.tm_min = log_minute;
                log_time.tm_sec = log_second;

                time_t log_timestamp = mktime(&log_time);
                double diff_seconds = difftime(now, log_timestamp);
                if (diff_seconds >= 0 && diff_seconds <= 60) {
                    count_last_minute++;
                }
                if (diff_seconds >= 0 && diff_seconds <= 3600) {
                    count_last_hour++;
                }
            }
        }
    }
    fclose(file);

    return (count_last_minute >= max_requests_per_minute || count_last_hour >= max_requests_per_hour);
}
void sendMessage(int client_fd, const char* message) {
    write(client_fd, message, strlen(message));
}