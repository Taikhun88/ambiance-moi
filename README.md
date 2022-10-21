# Aide mémo projet symfony  

### Feature Login  
Au préalable, avoir créé la bdd (donc configuration variable Database URL dans .env ou .env.local et symfony console d:d:c)
1. Création de la table user avec la commande make:user
2. Migrer le tout avec les commandes indiquées dans le terminal
3. Créer le formulaire d'inscription avec la commande make:registration-form (penser à installer le bundle verify email bundle au préalable)
4. Configurer la variable MAILER_DSN et obtenir autorisation et clé du compte mail en question pour l'envoi de mails via l'app
5. Pour le bon fonctionnement, penser à installer le bundle propre au compte mail. Si un compte google alors commande google mailer etc.