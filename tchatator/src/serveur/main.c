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

// client actuel

char client_ip[BUFFER_SIZE];
int client_id = -1;
int isProClient = false;

// fonctions
void readConfig();
void startSocketServer();
void handle_sigint(int sig);
void logInFile(int level, const char* text);
char* getDateHeure();


int main() {
    signal(SIGINT, handle_sigint);
    readConfig();
    startSocketServer();

    return EXIT_SUCCESS;
}

void handle_sigint(int sig) {
    printf("\nShutting down server...\n");
    close(server_fd);
    exit(EXIT_SUCCESS);
}

void readConfig() {
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
            strncpy(client_ip, client_address, BUFFER_SIZE - 1);
            printf("Connexion acceptée depuis : %s\n", client_ip);
        } else {
            perror("Erreur lors de la récupération de l'adresse IP du client");
        }

        printf("Connexion traitée pour le client : %s\n", client_ip);
        close(client_fd);  
    }
}



void logInFile(int level, const char* text) {
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

    char* date_heure = malloc(20 * sizeof(char));
    if (date_heure == NULL) {
        perror("Erreur d'allocation de mémoire");
        exit(EXIT_FAILURE);
    }

    sprintf(date_heure, "%04d-%02d-%02d:%02d:%02d:%02d",
            tm.tm_year + 1900,
            tm.tm_mon + 1,
            tm.tm_mday,
            tm.tm_hour,
            tm.tm_min,
            tm.tm_sec);

    return date_heure;
}