#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>


typedef struct {
  char name[50];
  int type; // 1 pour voiture, 2 pour camion, 3 pour bus
} scar;

typedef struct {
   int pos;
   int etat; // 0 si vide, 1 si plein
   scar cli;
} spark;

void menu();
void entrer();
void sortir();
void ecrire_tab_fichier(spark tab[], int taille, const char *nom_fichier);
spark* lire_tab_fichier(int *taille, const char *nom_fichier);
void remise_zero();
void changer_mot_de_passe();
void admin();
bool verifier_mot_de_passe();
void teste();


int main() {
    menu();
    return 0;
}

void menu() {
    int choix;
    printf("Bienvenue chez SalamaParking ! Nous sommes heureux de vous accueillir.\n");
    printf("1 - Pour faire entrer un vehicule, choisir 1...\n");
    printf("2 - Pour faire sortir un vehicule, choisir 2...\n");
    printf("Choix : ");
    scanf("%d", &choix);
    printf("\n");
    if (choix == 1) {
        system("cls");
        entrer();
        printf("\n\n");
        system("cls");
        menu();
    } else if (choix == 2) {
        system("cls");
        sortir();
        printf("\n\n");
        system("cls");
        menu();
    }else if(choix == 00){
       system("cls");
       teste();
    } else {
    printf("Desole, c est un mauvais choix. Tu dois choisir entre les options disponibles.\n\n");
    menu();
    }
}

void entrer() {
    int p;
    int i = 0, inf = 0;
    int taille;
    spark *tab = lire_tab_fichier(&taille, "parking.txt");

    while (i < taille && inf == 0) {
        if (tab[i].etat == 0) {
            p = i + 1;
            inf = 1;
        }
        i++;
    }

    if (inf == 0) {
        printf("Malheureusement le parking est plein...\n");
        system("pause");
        system("cls");
        menu();
    } else {
        tab[p - 1].etat = 1;
        printf("Bienvenue a notre parking [--SalamaParking--] \nLe tarif de notre parking est de :\n\n");
        printf("3 dinars tunisiens pour les voitures.\n5 dinars tunisiens pour les camions.\n8 dinars tunisiens pour les bus.\n\n");
        printf("Merci d'entrer la matricule de votre vehicule.\n");
        printf("Matricule de vehicule : ");
        scanf("%s", tab[p - 1].cli.name);

        printf("\nSi votre vehicule est une voiture, entrez 0 ; si c'est un camion, entrez 1 ; sinon si c'est un bus, entrez 2.\n");
        printf("Choix : ");
        scanf("%d", &tab[p - 1].cli.type);

        while (tab[p - 1].cli.type < 0 || tab[p - 1].cli.type > 2) {
            printf("\nDesole, c est un mauvais choix. Vous devez choisir entre les options disponibles.\n");
            printf("Choix : ");
            scanf("%d", &tab[p - 1].cli.type);
        }
        printf("\nMerci de reculer dans la position %d\n", tab[p - 1].pos);
        system("pause");
    }

    ecrire_tab_fichier(tab, taille, "nvparking.txt");
    remove("parking.txt");
    rename("nvparking.txt", "parking.txt");
    free(tab);
}

void sortir() {
    int pos;
    char name[50];
    int taille;
    spark *tab = lire_tab_fichier(&taille, "parking.txt");

        // Demander la position du véhicule
    printf("Veuillez entrer la position de votre parking (1 a 50) : ");
    scanf("%d", &pos);
       // Vérifier si la position est occupée (etat == 1)
    if (pos < 1 || pos > 50) {
        printf("\nPosition invalide. Elle doit etre entre 1 et 50, vous devez verifier votre psoition.\n");
        system("pause");
        system("cls");
        menu();
        return;
    }

    if (tab[pos - 1].etat == 0) {
        printf("\nCette position est incorrect, vous devez verifier votre psoition.\n\n");
        system("pause");
        system("cls");
        menu();
        return;
    }
        // Demander la matrecule du véhicule
    printf("Veuillez entrer la Matrecule de votre vehicule : ");
    scanf("%s", name);

    // Vérifier si le nom du véhicule correspond à celui dans la position
    if (strcmp(tab[pos - 1].cli.name, name) != 0) {
        printf("\nLa Matrecule du vehicule ne correspond pas a cette positon. Essayez à nouveau.\n\n");
        system("pause");
        system("cls");
        menu();
        return;
    }

    // Afficher le tarif selon le type de véhicule
    if (tab[pos - 1].cli.type == 0) {
        printf("\nTarif : 3 dinars TN pour une voiture.\n");
    } else if (tab[pos - 1].cli.type == 1) {
        printf("\nTarif : 5 dinars TN pour un camion.\n");
    } else if (tab[pos - 1].cli.type == 2) {
        printf("\nTarif : 8 dinars TN pour un bus.\n");
    }


    // Libérer la place (mettre l'état à 0)
    tab[pos - 1].etat = 0;
    printf("\nMerci d avoir choisi notre parking [--SalamaParking--], votre vehicule a quitte le parking.\n");

    // Sauvegarder les changements dans le fichier
    ecrire_tab_fichier(tab, taille, "nvparking.txt");
    remove("parking.txt");
    rename("nvparking.txt", "parking.txt");
    free(tab);
     system("pause");
}

void ecrire_tab_fichier(spark tab[], int taille, const char *nom_fichier) {
    FILE *fichier = fopen(nom_fichier, "w");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        exit(EXIT_FAILURE);
    }

    for (int i = 0; i < taille; i++) {
        fprintf(fichier, "Position: %d, Etat: %d, Nom: %s, Type: %d\n",
                tab[i].pos, tab[i].etat, tab[i].cli.name, tab[i].cli.type);
    }

    fclose(fichier);
}

spark* lire_tab_fichier(int *taille, const char *nom_fichier) {
    FILE *fichier = fopen(nom_fichier, "r");
    if (fichier == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        exit(EXIT_FAILURE);
    }

    // Allouer de la mémoire pour le tableau
    spark *tab = (spark *)malloc(50 * sizeof(spark));
    *taille = 0;

    // Lire chaque ligne du fichier et remplir le tableau
    while (fscanf(fichier, "Position: %d, Etat: %d, Nom: %49[^,], Type: %d\n",
                  &tab[*taille].pos, &tab[*taille].etat, tab[*taille].cli.name, &tab[*taille].cli.type) == 4) {
        (*taille)++;
    }

    fclose(fichier);
    return tab; // Retourner le tableau alloué dynamiquement
}
// si coix de menu 00 ouvrir adimin menu
void admin (){
  int n ;
  printf("\nMenu Admin en cour d execution ....\n");
  printf("1 - Remise a zero du programme ( Parking Vide ) .\n");
  printf("2 - Changer le mot de passe du  Menu Admin .\n");
  printf("3 - Retour au menu principale .\n");
  printf("choix : ");
  scanf("%d",&n);
  while (n<0 || n>3){
    printf("Mauvais choix....\n");
    printf("Nouveau choix : ");
    scanf("%d",&n);
  }
   if (n==3){
  printf("\n\n");
  system("cls");
  menu();
  }else if(n==2) {
     system("cls");
     changer_mot_de_passe();
     printf("\n\n");
     system("cls");
     menu();
  }else {
     remise_zero();
     printf("\n\n");
     system("cls");
     menu();
  }
}

void changer_mot_de_passe() {
    FILE *fichier = fopen("mdp.txt", "r");
    char ancien_mdp[100];
    char nouveau_mdp[100];
    char confirmation_mdp[100];
    char mdp_fichier[100];

    // Lecture du mot de passe actuel à partir du fichier
    fscanf(fichier, "%99s", mdp_fichier);
    fclose(fichier);

    // Demande de l'ancien mot de passe
    printf("Veuillez entrer l ancien mot de passe : ");
    scanf("%99s", ancien_mdp);
    if (!verifier_mot_de_passe(ancien_mdp)){
        printf("Ancien mot-de-passe est incorrect...\n ");
        system("pause");
        admin();
    }

    // Vérification de l'ancien mot de passe
    if (strcmp(ancien_mdp, mdp_fichier) != 0) {
        printf("Mot de passe incorrect. Changement annule.\n");
        return;
    }

    // Demande du nouveau mot de passe
    printf("Veuillez entrer le nouveau mot de passe : ");
    scanf("%99s", nouveau_mdp);

    // Confirmation du nouveau mot de passe
    printf("Veuillez confirmer le nouveau mot de passe : ");
    scanf("%99s", confirmation_mdp);

    // Vérification de la confirmation
    if (strcmp(nouveau_mdp, confirmation_mdp) != 0) {
        printf("Les mots de passe ne correspondent pas. Changement annule.\n");
        system("pause");
        return;
    }

    // Mise à jour du fichier avec le nouveau mot de passe
    fichier = fopen("mdp.txt", "w");


    fprintf(fichier, "%s\n", nouveau_mdp);
    printf("Mot de passe changé avec succes.\n");
    fclose(fichier);
    system("pause");
}

void remise_zero(){

    int taille;
    spark *tab = lire_tab_fichier(&taille, "parking.txt");
    for (int i=0 ; i<50; i++ ){
        tab[i].etat=0 ;

    }
    ecrire_tab_fichier(tab, taille, "nvparking.txt");
    remove("parking.txt");
    rename("nvparking.txt", "parking.txt");
    free(tab);
    printf("Remise a zero est terminer...\n");
    system("pause");
}



bool verifier_mot_de_passe(const char *mot_de_passe) {
    FILE *fichier = fopen("mdp.txt", "r");
    char mdp_fichier[100];

    // Lire le mot de passe depuis le fichier
    fscanf(fichier, "%99s", mdp_fichier);
    fclose(fichier);

    // Comparer le mot de passe fourni avec celui du fichier
    if (strcmp(mot_de_passe, mdp_fichier) == 0) {
        return true;
    } else {
        return false;
    }
}

void teste (){
   char mdp[100];
   printf("Mot_DE_Passe : ");
   scanf("%99s", mdp );
   if ( verifier_mot_de_passe(mdp) ){
    system("cls");
    admin();
   }else {
   printf("Mot_De_Passe incorrect !\n\n");
   system("pause");
   system("cls");
   menu();
   }
 }
