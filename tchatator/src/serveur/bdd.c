#include <stdio.h>
#include <stdlib.h>
#include <openssl/sha.h>
#include <string.h>

int main() {

    return EXIT_SUCCESS;
}


void generate_api_key(const char *str1, const char *str2, const char *str3, unsigned char *output) {
    // Concaténation des chaînes
    char combined[256];
    snprintf(combined, sizeof(combined), "%s%s%s", str1, str2, str3);

    // Hachage avec SHA-256
    SHA256((unsigned char *)combined, strlen(combined), output);
}


