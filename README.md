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

 Remarques : il existe 2 méthodes pour générer un formulaire et intégrer la logique via le Controller. Il est préférable de recourir au createForm via l'abstractController qui est plus léger en logique.
 Il réalise le rendering et la soumission des data en 1 fois contrairemetn au createFormBuilder

 https://symfony.com/doc/current/forms.html
<hr>  

### Password hashing
Hashed passwords must be set automatically when user create their account. The best way is to use make:user command provided by Symfony  

https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords
1. Launch the command that will create the Entity User with all required defaults properties and create a RegistrationController with the hashpassword method  

Note: this allows to get the hashed password for the front process of registration, if you create a method new user in backoffice, better set your own hashpassword. 

2. This needs to add some settings to improve security via security.yaml  

https://symfony.com/doc/current/security/passwords.html  

3. Then you can use the hashpassword method build in the RegistrationController. 
Just be sure to adapt the name of your property password of EntityUser. By default, it is 'plainpassword'  
In your UserController of backoffice, inject the dependance UserPasswordHasherInterface. 
Then add the hashpassword method, it's done

4a. For the front interface to allow user to reset his password, use the symfony command 
Install the bundle first > composer require symfonycasts/reset-password-bundle
Attention à la version de PHP utilisée dans le projet. Préciser version au besoin
https://openclassrooms.com/forum/sujet/probleme-dinstalation-du-reset-password-bundle  

4b. Launch the command > php bin/console make:reset-password  
4c. Follows the steps described in terminal (you can schema update instead of migrating)
5. Configurer le DSN dans .env https://symfony.com/doc/current/mailer.html
<hr>  

 ### Flash messages  
 1. Créer le add message dans le controller via l'abstract
 2. Créer le template partial _flash.html.twig et créer la boucle des message en fonction du label appelé
 3. Intégrer le include dans le base.html.twig  

 https://nouvelle-techno.fr/articles/9-verification-d-adresse-email-sans-bundle-symfony-6
<hr>  

### Pre Authentification, validation du compte utilisateur
1. Une fois le process make:user achevé. Il est nécessaire de créer le userChecker afin que l'utilisateur passe par l'étape de validation de création de compte grâce au lien envoyé par mail.
Créer le UserChecker.php dans le Controller et ajouter la condition de pre authentication souhaitée dans la méthode checkPreAuth

2. Si la méthode de post auth n'est pas utile alors commenter  

https://symfony.com/doc/current/security/user_checkers.html#enabling-the-custom-user-checker
<hr>  

### Personnalisation des input Symfony form avec les variables  

https://symfony.com/doc/current/form/form_customization.html

 ##### DEBUG
 1. FileType de User.php. Lors de l'edit, la page ne peut afficher l'input. Passer le mapped à false dans le formtype de l'input concerné. Le bug se présente car l'input en edit charge un nom de fichier (string) depuis la database alors qu'il doit permettre de upload un file (objet).

 2. Some services can not be injected or autowired, which mean be called as a Service in the parameters of your function. To allow this to called as a service injected, Go to add the name of the service into the services.yaml file default : bind: "name of your service". In most cases, Symfony may provide you the proper name to add  

 https://symfonycasts.com/screencast/symfony-security/manual-auth

 3. Lors de la génération d'une paire de clé privées/publiques pour l'installation du bundle JWT. La commande fournie par la documentation peut provoquer une erreur. Exécuter les commandes suivantes une par une  
 
https://stackoverflow.com/questions/66252709/error-system-libraryfopenno-such-process
  
4. To allow the user to not be logged in (registration.controller) when they click the confirmation link in their email. With this mode, we need to pass an array as the final argument to include the user id
['id' => $user->getId()]  

https://symfonycasts.com/screencast/symfony-security/verify-email

### INSTALL JWT
Source : https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html#working-with-cors-requests  

1. Installer le bundle JWT > composer require lexik/jwt-authentication-bundle
2. Générer une paire de clé publique/privée > php bin/console lexik:jwt:generate-keypair
3. Déplacer les JWT Variables qui sont dans .env dans .env.local (comportent les clé et passphrase)
4. Configurer security.yaml, routes.yaml
(5. Ajouter les eventlisteners pour l'utilisation des handler, AuthenticationSuccessListener)
