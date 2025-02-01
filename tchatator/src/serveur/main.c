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
#include <sha256.c>
#include <bdd.c>

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
int reload_signal;
// Server
int server_fd;
bool isVerbose = false;
// client actuel

char client_ip[BUFFER_SIZE];
int client_id = -1;
int isProClient = false;

// fonctions
void help();
void getConfig();
void startSocketServer();
void stopServer(int sig);
void logInFile(int level, const char* text);
char* getDateHeure();
int gereMessage(const char* message, int client_fd);
void sendMessage(const char* message, int client_fd);
void rechargeConfig(int sig);

int login(const char* message, int client_fd, int *client_id, int *isProClient) ;
int quitServer(const char* message, int client_fd, int *client_id, int *isProClient);
int listUsers(const char* message, int client_fd);
int sendUserMessage(const char* message, int client_fd);
int modifyUserMessage(const char* message, int client_fd);
int deleteUserMessage(const char* message, int client_fd);
int listUserMessage(const char* message, int client_fd);
int listUserHistory(const char* message, int client_fd);

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
    startSocketServer();

    return EXIT_SUCCESS;
}

void stopServer(int sig) {
    printf("\nShutting down server...\n");
    close(server_fd);
    exit(EXIT_SUCCESS);
}
void rechargeConfig(int sig) {
    printf("\nSignal reçu (%d), rechargement de la configuration...\n", sig);
    logInFile(0, "Rechargement de la configuration suite à un signal.");
    getConfig();

    char log_message[256];
    snprintf(log_message, sizeof(log_message), 
             "Nouvelle configuration : port=%d, max_requests_per_minute=%d, max_requests_per_hour=%d",
             port, max_requests_per_minute, max_requests_per_hour);
    logInFile(0, log_message);
    printf("\nRechargement de la configuration terminé !\n");
}
void getConfig() {
    const char *filename = "config.cfg";
    FILE *file = fopen(filename, "r");
    if (!file) {
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
    char client_address[INET_ADDRSTRLEN];
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

    if (bind(server_fd, (struct sockaddr*)&server_addr, sizeof(server_addr)) == -1) {
        perror("Erreur lors de la liaison (bind)");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    if (listen(server_fd, queue_size) == -1) {
        perror("Erreur lors de l'écoute (listen)");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    printf("Serveur démarré sur le port %d avec une file d'attente de %d\n", port, queue_size);

    while (1) {
        printf("En attente de connexions...\n");

        int client_fd = accept(server_fd, (struct sockaddr*)&client_addr, &client_addr_len);
        if (client_fd == -1) {
            perror("Erreur lors de l'acceptation de la connexion");
            continue;
        }

        if (inet_ntop(AF_INET, &client_addr.sin_addr, client_address, INET_ADDRSTRLEN) != NULL) {
            printf("Connexion acceptée depuis : %s\n", client_address);

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
                printf("Message reçu de %s : %s\n", client_address, buffer);
                if (gereMessage(buffer, client_fd) == -1) {
                    break;
                }
            } else if (bytes_received == 0) {
                printf("Client %s s'est déconnecté.\n", client_address);
                break;
            } else {
                perror("Erreur lors de la réception des données");
                break;
            }
        }

        close(client_fd); // Fermer la connexion avec le client
    }
}

int gereMessage(const char* message, int client_fd) {
    int result = 0;
    
    result |= login(message, client_fd, &client_id, &isProClient);
    result |= listUsers(message, client_fd);
    result |= sendUserMessage(message, client_fd);

    if (quitServer(message, client_fd, &client_id, &isProClient) == -1) {
        return -1;
    }
       
    if (!result) {
        sendMessage(client_fd, "142/BAD_REQUEST");
    }
    return 0;

}

int modifyUserMessage(const char* message, int client_fd) {
    if (strncmp(message, "MDFMSG:", 7) != 0) {
        return 0; 
    }

    if (client_id == -1) {
        sendMessage(client_fd, "145/ERROR: Not logged in");
        return 1;
    }

    int msgid;
    char newmsg[max_message_size + 1]; 
    char formatString[50];

    snprintf(formatString, sizeof(formatString), "%%d,%%%d[^\n]", max_message_size);

    if (sscanf(message + 7, formatString, &msgid, newmsg) < 2) {
        sendMessage(client_fd, "146/ERROR: Invalid message format");
        return 1;
    }

    if (!isMessageFromSender(msgid, client_id)) {
        sendMessage(client_fd, "403/ERROR: Unauthorized to modify message");
        return 1;
    }

    if (!updateMessage(msgid, newmsg)) {
        sendMessage(client_fd, "500/ERROR: Failed to modify message");
        return 1;
    }

    sendMessage(client_fd, "1/OK: Message modified");
    return 1;
}



int sendUserMessage(const char* message, int client_fd) {
    if (strncmp(message, "MSG:", 4) != 0) {
        return 0;
    }
    if (client_id == -1) {
        sendMessage(client_fd, "145/ERROR: Not logged in");
        return 1;
    }

    const char *msgData = message + 4;
    while (*msgData == ' ') msgData++;  

    int recepteur_id;
    char msgContent[max_message_size + 1]; 
    char formatString[50];  

    snprintf(formatString, sizeof(formatString), "%%d,%%%d[^\n]", max_message_size);

    if (sscanf(msgData, formatString, &recepteur_id, msgContent) < 2) {
        sendMessage(client_fd, "146/ERROR: Invalid message format");
        return 1;
    }

    int emetteur_id = client_id;

    int messageId = createMessage(emetteur_id, recepteur_id, msgContent);
    if (messageId == 0) {
        sendMessage(client_fd, "500/ERROR: Failed to send message");
        return 1;
    }

    char response[50];
    snprintf(response, sizeof(response), "1/OK:%d", messageId);
    sendMessage(client_fd, response);
    return 1;
}

int listUsers(const char* message, int client_fd) {
    if (strcmp(message, "GETUSERS") != 0) {
        return 0;
    }
    if (client_id == -1) {
        sendMessage(client_fd, "145/ERROR: Not logged in");
        return 1;
    }
    PGresult *res = getUsersList(isProClient);
    if (!res) {
        sendMessage(client_fd, "500/ERROR: Unable to retrieve user list");
        return 0;
    }
    int rows = PQntuples(res);
    char response[BUFFER_SIZE] = "1/OK:"; 

    for (int i = 0; i < rows; i++) {
        char entry[256];
        
        if (isProClient) {
            snprintf(entry, sizeof(entry), "%s,%s;", PQgetvalue(res, i, 0), PQgetvalue(res, i, 1));
        } else {
            snprintf(entry, sizeof(entry), "%s,%s,%s;", PQgetvalue(res, i, 0), PQgetvalue(res, i, 1), PQgetvalue(res, i, 2));
        }

        if (strlen(response) + strlen(entry) < BUFFER_SIZE - 1) {
            strcat(response, entry);
        } else {
            break;
        }
    }

    PQclear(res);
    sendMessage(client_fd, response);
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
        sendMessage(client_fd, "103/DENIED");
        return 1;
    }
    
    if (strcmp(apiKey, admin_api_key) == 0) {
        *client_id = -2;
        *isProClient = 1;
        sendMessage(client_fd, "1/OK:-2,1");
        return 1;
    }
    
    if (!loginWithKey(apiKey, isProClient, client_id)) {
        *client_id = -1;
        sendMessage(client_fd, "103/DENIED");
        return 1;
    }
    
    char response[50];
    snprintf(response, sizeof(response), "1/OK:%d,%d", *client_id, *isProClient);
    sendMessage(client_fd, response);
    return 1;
}

int quitServer(const char* message, int client_fd, int *client_id, int *isProClient) {
    if (strcmp(message, "QUIT") != 0) {
        return 0; 
    }

    *client_id = -1;
    *isProClient = 0;

    sendMessage(client_fd, "1/OK");

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
