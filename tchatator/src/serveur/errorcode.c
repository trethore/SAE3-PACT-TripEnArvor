#include <stdio.h>

#ifndef ERRORCODE_H
#define ERRORCODE_H

// Codes de succès
#define SUCCESS_OK "1/OK"
#define SUCCESS_MESSAGE_SENT "1/OK: Message envoyé"
#define SUCCESS_MESSAGE_MODIFIED "1/OK: Message modifié"
#define SUCCESS_MESSAGE_DELETED "1/OK: Message supprimé"
#define SUCCESS_BLOCK_APPLIED "1/OK: Blocage appliqué"
#define SUCCESS_BAN_APPLIED "1/OK: Bannissement appliqué"
#define SUCCESS_LOGIN "1/OK: Connexion réussie"

// Codes d'erreur
#define ERROR_DENIED "103/DENIED"  
#define ERROR_FORBIDDEN "104/FORBIDDEN"  
#define ERROR_NOT_FOUND "105/NOT_FOUND" 
#define ERROR_BAD_REQUEST "106/BAD_REQUEST"
#define ERROR_UNAUTHORIZED "107/NOLOGIN: Not logged in" 
#define ERROR_INVALID_MESSAGE_FORMAT "108/MSGFORM: Invalid message format"
#define ERROR_MESSAGE_TOO_LARGE "109/PAYLOAD_TOO_LARGE"
#define ERROR_TOO_MANY_REQUESTS "110/TOO_MANY_REQUESTS" 
#define ERROR_MESSAGE_FAILED "111/ERROR: Failed to send message"  
#define ERROR_MESSAGE_MODIFY_FAILED "112/ERROR: Failed to modify message"  
#define ERROR_UNAUTHORIZED_MODIFICATION "113/ERROR: Unauthorized to modify message" 

#endif // ERRORCODE_H
