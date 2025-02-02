#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

extern char* computeSha256(const char *input);

// requetes

PGconn* getConnection() {
    const char *conninfo = "host=redden.ventsdouest.dev dbname=sae user=sae password=naviguer-vag1n-eNTendes";
    PGconn *conn = PQconnectdb(conninfo);

    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Erreur de connexion : %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        return NULL;
    }

    return conn;
}
PGresult* getAllMembers() {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    PGresult *res = PQexec(conn, "SELECT * FROM sae.compte_membre");

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des membres : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    PQfinish(conn);
    return res; 
}
PGresult* getAllPrivateProfessionals() {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    PGresult *res = PQexec(conn, "SELECT * FROM sae.compte_professionnel_prive");

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des professionnels privés : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    PQfinish(conn);
    return res;
}
PGresult* getAllPublicProfessionals() {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    PGresult *res = PQexec(conn, "SELECT * FROM sae.compte_professionnel_publique");

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des professionnels publics : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    PQfinish(conn);
    return res; // Caller must call PQclear() after use
}


int createMessage(int id_emeteur, int id_receveur, const char *contenu) {
    PGconn *conn = getConnection();
    if (!conn) return 0;

    char query[1024];
    snprintf(query, sizeof(query),
        "INSERT INTO sae._message (id_emeteur, id_receveur, date_envoi, contenu, lu) "
        "VALUES (%d, %d, NOW(), '%s', false) RETURNING id_message;",
        id_emeteur, id_receveur, contenu);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de l'insertion du message : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return 0;
    }

    int message_id = atoi(PQgetvalue(res, 0, 0));

    PQclear(res);
    PQfinish(conn);
    return message_id; // Return the new message ID
}


PGresult* getMessagesForUser(int id_receveur) {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    char query[256];
    snprintf(query, sizeof(query),
        "SELECT id_message, id_emeteur, date_envoi, date_modif, contenu, lu "
        "FROM sae._message WHERE id_receveur = %d AND supprime = FALSE ORDER BY date_envoi DESC;", 
        id_receveur);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des messages : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    PQfinish(conn);
    return res;
}

int updateMessage(int id_message, const char *new_content) {
    PGconn *conn = getConnection();
    if (!conn) return 0;

    char query[1024];
    snprintf(query, sizeof(query),
        "UPDATE sae._message SET contenu = '%s', date_modif = NOW() WHERE id_message = %d AND supprime = FALSE;",
        new_content, id_message);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        fprintf(stderr, "Erreur lors de la modification du message : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return 0;
    }

    PQclear(res);
    PQfinish(conn);
    return 1; // Success
}

int deleteMessage(int id_message) {
    PGconn *conn = getConnection();
    if (!conn) return 0;

    char query[256];
    snprintf(query, sizeof(query),
        "UPDATE sae._message SET supprime = TRUE WHERE id_message = %d;", id_message);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        fprintf(stderr, "Erreur lors de la suppression du message : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return 0;
    }

    PQclear(res);
    PQfinish(conn);
    return 1; // Success
}


// fonctions

int isProfessional(int compteId) {
    PGconn *conn = getConnection();
    if (!conn) return -1; 

    char query[256];
    snprintf(query, sizeof(query),
             "SELECT EXISTS (SELECT 1 FROM sae._compte_professionnel WHERE id_compte = %d)", compteId);

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la vérification du compte : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return -1;
    }

    int result = atoi(PQgetvalue(res, 0, 0)); 

    PQclear(res);
    PQfinish(conn);
    return result;
}

int loginWithKey(const char *apiKey, int *isPro, int *compteId) {
    PGconn *conn = getConnection();
    if (!conn) return 0;

    char query[512];
    snprintf(query, sizeof(query),
        "SELECT id_compte, email, mot_de_passe FROM sae._compte");

    PGresult *res = PQexec(conn, query);
    
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des comptes : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return 0;
    }

    int found = 0;
    int rows = PQntuples(res);

    for (int i = 0; i < rows; i++) {
        int id = atoi(PQgetvalue(res, i, 0));
        char *email = PQgetvalue(res, i, 1);
        char *password = PQgetvalue(res, i, 2);
        char keyInput[1024];
        snprintf(keyInput, sizeof(keyInput), "%d%.*s%.*s", id,500, email,500, password);

        char *generatedKey = computeSha256(keyInput);

        if (strcmp(generatedKey, apiKey) == 0) {
            *compteId = id;
            *isPro = isProfessional(id);
            found = 1;
            free(generatedKey);
            break;
        }
        free(generatedKey);
    }

    PQclear(res);
    PQfinish(conn);

    if (!found) {
        *compteId = -1;
        *isPro = 0;
    }

    return found;
}

PGresult* getUsersList(int isPro) {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    char query[512];

    if (isPro) {
        snprintf(query, sizeof(query),
            "SELECT id_compte, email FROM sae.compte_membre;");
    } else {
        snprintf(query, sizeof(query),
            "SELECT c.id_compte, c.email, "
            "CASE WHEN p2.siren IS NOT NULL THEN 'private' ELSE 'public' END AS pro_type "
            "FROM sae.compte_professionnel_publique p "
            "FULL JOIN sae.compte_professionnel_prive p2 USING (id_compte) "
            "JOIN sae._compte c USING (id_compte);");

    }

    PGresult *res = PQexec(conn, query);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de la récupération des utilisateurs : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    PQfinish(conn);
    return res; 
}

int isMessageFromSender(int msgid, int client_id) {
    PGconn *conn = getConnection();
    if (conn == NULL) {
        fprintf(stderr, "Database connection is NULL\n");
        return -1;
    }

    const char *query = "SELECT COUNT(*) FROM sae._message WHERE id_message = $1 AND id_emeteur = $2";
    
    const char *paramValues[2];
    char msgidStr[12], clientIdStr[12];
    snprintf(msgidStr, sizeof(msgidStr), "%d", msgid);
    snprintf(clientIdStr, sizeof(clientIdStr), "%d", client_id);
    
    paramValues[0] = msgidStr;
    paramValues[1] = clientIdStr;

    PGresult *res = PQexecParams(conn, query, 2, NULL, paramValues, NULL, NULL, 0);
    
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Query failed: %s\n", PQerrorMessage(conn));
        PQclear(res);
        return -1;
    }

    int count = atoi(PQgetvalue(res, 0, 0));
    PQclear(res);

    return (count > 0) ? 1 : 0;
}

char* getEmailFromId(int id_compte) {
    PGconn *conn = getConnection();
    if (!conn) return NULL;

    char query[256];
    snprintf(query, sizeof(query), "SELECT email FROM sae._compte WHERE id_compte = %d;", id_compte);

    PGresult *res = PQexec(conn, query);
    if (PQresultStatus(res) != PGRES_TUPLES_OK || PQntuples(res) == 0) {
        PQclear(res);
        PQfinish(conn);
        return NULL;
    }

    char *email = strdup(PQgetvalue(res, 0, 0));
    PQclear(res);
    PQfinish(conn);
    return email;
}