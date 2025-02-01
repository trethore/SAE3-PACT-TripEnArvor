#include <stdio.h>
#include <stdint.h>
#include <string.h>
#include <stdlib.h>

#define ROTRIGHT(a,b) (((a) >> (b)) | ((a) << (32-(b))))
#define CH(x,y,z) (((x) & (y)) ^ (~(x) & (z)))
#define MAJ(x,y,z) (((x) & (y)) ^ ((x) & (z)) ^ ((y) & (z)))
#define EP0(x) (ROTRIGHT(x,2) ^ ROTRIGHT(x,13) ^ ROTRIGHT(x,22))
#define EP1(x) (ROTRIGHT(x,6) ^ ROTRIGHT(x,11) ^ ROTRIGHT(x,25))
#define SIG0(x) (ROTRIGHT(x,7) ^ ROTRIGHT(x,18) ^ ((x) >> 3))
#define SIG1(x) (ROTRIGHT(x,17) ^ ROTRIGHT(x,19) ^ ((x) >> 10))

static const uint32_t k[64] = {
    0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
    0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
    0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
    0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
    0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
    0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
    0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
};

void sha256Transform(uint32_t state[8], const uint8_t data[]) {
    uint32_t m[64], s[8], t1, t2;
    int i, j;

    for (i = 0, j = 0; i < 16; ++i, j += 4)
        m[i] = (data[j] << 24) | (data[j+1] << 16) | (data[j+2] << 8) | data[j+3];
    for ( ; i < 64; ++i)
        m[i] = SIG1(m[i - 2]) + m[i - 7] + SIG0(m[i - 15]) + m[i - 16];

    for (i = 0; i < 8; ++i)
        s[i] = state[i];

    for (i = 0; i < 64; ++i) {
        t1 = s[7] + EP1(s[4]) + CH(s[4],s[5],s[6]) + k[i] + m[i];
        t2 = EP0(s[0]) + MAJ(s[0],s[1],s[2]);
        s[7] = s[6];
        s[6] = s[5];
        s[5] = s[4];
        s[4] = s[3] + t1;
        s[3] = s[2];
        s[2] = s[1];
        s[1] = s[0];
        s[0] = t1 + t2;
    }

    for (i = 0; i < 8; ++i)
        state[i] += s[i];
}

void sha256Init(uint32_t state[8]) {
    state[0] = 0x6a09e667;
    state[1] = 0xbb67ae85;
    state[2] = 0x3c6ef372;
    state[3] = 0xa54ff53a;
    state[4] = 0x510e527f;
    state[5] = 0x9b05688c;
    state[6] = 0x1f83d9ab;
    state[7] = 0x5be0cd19;
}

void sha256Update(uint32_t state[8], const uint8_t data[], uint64_t len) {
    uint8_t buffer[64] = {0};
    uint64_t bitlen = len * 8;
    memcpy(buffer, data, len);
    buffer[len] = 0x80;
    *((uint64_t*)&buffer[56]) = __builtin_bswap64(bitlen);
    sha256Transform(state, buffer);
}

void sha256Final(uint32_t state[8], uint8_t hash[32]) {
    for (int i = 0; i < 8; ++i) {
        hash[i * 4] = (state[i] >> 24) & 0xff;
        hash[i * 4 + 1] = (state[i] >> 16) & 0xff;
        hash[i * 4 + 2] = (state[i] >> 8) & 0xff;
        hash[i * 4 + 3] = state[i] & 0xff;
    }
}

char* computeSha256(const char *input) {
    uint32_t state[8];
    uint8_t hash[32];
    size_t inputLen = strlen(input);

    sha256Init(state);
    sha256Update(state, (const uint8_t*)input, inputLen);
    sha256Final(state, hash);

    char *hashStr = malloc(65);
    if (!hashStr) {
        return NULL;
    }
    for (int i = 0; i < 32; ++i) {
        sprintf(hashStr + (i * 2), "%02x", hash[i]);
    }
    hashStr[64] = '\0';

    return hashStr;
}