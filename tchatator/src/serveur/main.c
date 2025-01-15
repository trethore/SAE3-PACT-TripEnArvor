#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <arpa/inet.h>
#include <signal.h>

#define BUFFER_SIZE 1024

int port = 12345;
int ban_duration = 0;
int block_duration = 0;
int max_requests_per_minute = 12;
int max_requests_per_hour = 90;
int max_message_size = 1000;
int socket_size= 5;
int history_block_size = 20;
char log_file_path[1024];
char admin_api_key[256];
int reload_signal;

int server_fd;

void readConfig();
void startSocketServer();
void handle_sigint(int sig);

int main() {
    signal(SIGINT, handle_sigint);
    readConfig();
    startSocketServer();


    return 0;
}

void startSocketServer() {
    struct sockaddr_in address;
    fd_set active_fds, read_fds;
    int opt = 1, addrlen = sizeof(address);

    if ((server_fd = socket(AF_INET, SOCK_STREAM, 0)) == 0) {
        perror("Socket creation failed");
        exit(EXIT_FAILURE);
    }

    if (setsockopt(server_fd, SOL_SOCKET, SO_REUSEADDR | SO_REUSEPORT, &opt, sizeof(opt)) < 0) {
        perror("Setsockopt failed");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(port);

    if (bind(server_fd, (struct sockaddr *)&address, sizeof(address)) < 0) {
        perror("Bind failed");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    if (listen(server_fd, 5) < 0) {
        perror("Listen failed");
        close(server_fd);
        exit(EXIT_FAILURE);
    }

    printf("Server listening on port %d...\n", port);

    FD_ZERO(&active_fds);
    FD_SET(server_fd, &active_fds);

    while (1) {
        read_fds = active_fds;

        if (select(FD_SETSIZE, &read_fds, NULL, NULL, NULL) < 0) {
            perror("Select failed");
            break;
        }

        for (int fd = 0; fd < FD_SETSIZE; ++fd) {
            if (FD_ISSET(fd, &read_fds)) {
                if (fd == server_fd) {
                    // Accept new connection
                    int new_socket;
                    if ((new_socket = accept(server_fd, (struct sockaddr *)&address, (socklen_t *)&addrlen)) < 0) {
                        perror("Accept failed");
                        continue;
                    }
                    printf("New connection established.\n");
                    FD_SET(new_socket, &active_fds);
                } else {
                    // Handle client message
                    char buffer[BUFFER_SIZE] = {0};
                    int valread = read(fd, buffer, BUFFER_SIZE);
                    if (valread > 0) {
                        printf("Received: %s", buffer);
                        char response[] = "1/OK: Test Completed\n";
                        send(fd, response, strlen(response), 0);
                        printf("Response sent.\n");
                    } else {
                        // Client disconnected
                        close(fd);
                        FD_CLR(fd, &active_fds);
                        printf("Connection closed.\n");
                    }
                }
            }
        }
    
    }   
    close(server_fd);

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
        perror("Error opening configuration file");
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
            } else if (strcmp(key, "socket_size") == 0) {
                socket_size = atoi(value);
            } else if (strcmp(key, "history_block_size") == 0) {
                history_block_size = atoi(value);
            } else if (strcmp(key, "log_file_path") == 0) {
                strncpy(log_file_path, value, sizeof(log_file_path) - 1);
            } else if (strcmp(key, "admin_api_key") == 0) {
                strncpy(admin_api_key, value, sizeof(admin_api_key) - 1);
            } else if (strcmp(key, "reload_signal") == 0) {
                reload_signal = atoi(value);
            } else {
                fprintf(stderr, "Unknown key in configuration file: %s\n", key);
            }
        }
    }

    fclose(file);
}
