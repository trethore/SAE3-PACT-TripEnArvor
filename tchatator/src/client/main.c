#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
int main() {
    int port, choixMenu = 0, choixRecep, choixConv, choixModif;
    char cleAPI[256];
    printf("Client Loaded !");    

    printf("############ CONFIGURATION ############\n");
    printf("Veuillez inserez votre port : ");
    scanf("%d", &port);
    printf("Veuillez inserez votre cle API : ");
    scanf("%c", cleAPI);

    do {
        printf("\n\n");
        printf("################# MENU #################\n");
        printf("1. Envoyer un message\n");
        printf("2. Lire les messages\n");
        printf("3. Modifier un message\n");
        printf("\nTapez le chiffre associé à une action : ");
        scanf("%d", choixMenu);

        switch (choixMenu) {
        case 1:
            printf("\n\n");
            printf("################# ENVOYER UN MESSAGE #################\n");
            printf("Choisir votre destinataire : ");
            // Comment choisir le destinataire ?
            printf("Ecrivez votre message : ");
            // Comment envoyer un message ?
            break;

        case 2:
            printf("\n\n");
            printf("################# LIRE LES MESSAGES #################\n");
            printf("1. Voir les messages non lus\n");
            printf("2. Afficher une conversation\n");
            printf("\nTapez le chiffre associé à une action : ");
            scanf("%d", choixRecep);
            switch (choixRecep)
            {
            case 1:
                // Affichage des msgs non lus
                break;
            
            case 2:
                // Affichage d'une conversation
                printf("\n\n");
                printf("############ AFFICHAGE D'UNE CONVERSATION ############\n");
                printf("1. Conversation avec pro 1");
                printf("2. Conversation avec pro 1");
                printf("\nTapez le chiffre associé à une conversation : ");
                scanf("%d", choixConv);
                switch (choixConv)
                {
                case 1:
                    // Affichage de la conversation avec pro 1
                    break;

                case 2:
                    // Affichage de la conversation avec pro 2
                    break;
                
                default:
                    break;
                }
                break;

            case 3:
                printf("\n\n");
                printf("############# MODIFICATION D'UN MESSAGE #############\n");
                scanf("%d", choixModif);
                break;

            default:
                break;
            }
            break;
        
        default:
            break;
        }
    } while (choixMenu != -1);

    return EXIT_SUCCESS;
}