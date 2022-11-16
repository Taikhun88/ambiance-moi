# Aide mémo projet symfony  

### Feature Login  
Au préalable, avoir créé la bdd (donc configuration variable Database URL dans .env ou .env.local et symfony console d:d:c)
1. Création de la table user avec la commande make:user
2. Migrer le tout avec les commandes indiquées dans le terminal
3. Créer le formulaire d'inscription avec la commande make:registration-form (penser à installer le bundle verify email bundle au préalable)
4. Configurer la variable MAILER_DSN et obtenir autorisation et clé du compte mail en question pour l'envoi de mails via l'app
5. Pour le bon fonctionnement, penser à installer le bundle propre au compte mail. Si un compte google alors commande google mailer etc.  

https://symfony.com/doc/current/security.html#form-login  
<hr>  
   
### CRUD User  
 1. Ajouter les properties requises à l'entité User via la commande make:entity et les schema update force ou migrate
 2. Créer les templates pour le CRUD
 3. Créer le builder form via un UserFormType.php et paramétrer l'option data. Ajouter les champs form row dans les templates
 4. Dans les méthodes de création et update, faire appel à l'objet Form pour l'affichage, la soumission et vérification (processing form).
 5. Penser à persist et ou flush les data 
 6. Créer le message de confirmation post action et paramétrer la redirectionToRoute 

 https://symfony.com/doc/current/forms.html
<hr>  

 ### Flash messages  
 1. Créer le add message dans le controller via l'abstract
 2. Créer le template partial _flash.html.twig et créer la boucle des message en fonction du label appelé
 3. Intégrer le include dans le base.html.twig  

 https://nouvelle-techno.fr/articles/9-verification-d-adresse-email-sans-bundle-symfony-6
<hr>  

 ##### DEBUG
 1. FileType de User.php. Lors de l'edit, la page ne peut afficher l'input. Passer le mapped à false dans le formtype de l'input concerné. Le bug se présente car l'input en edit charge un nom de fichier (string) depuis la database alors qu'il doit permettre de upload un file (objet).

