## example de programme en symfony 6
Utilisation de tutoriel

## Lancer le serveur en dev symfony
cd symfony/example/ # correspond au répertoire contenant le projet à lancer
symfony server:start

## Utilisation de l'IDE Visual Studio Code
### Extensions rajoutées
 ESLint; Git History; Lorem ipsum; MYSQL; PHP Intelephense; Symfony code ...; Twig;Class autocomplete for HTML;

## installation Symfony sous ubuntu
https://www.osradar.com/install-symfony-ubuntu-20-04/

 ## Commandes Symfony
 $ php bin/console make:controller
 > BlogController

## boostrap (https://www.npmjs.com/package/bootstrap)
### https://getbootstrap.com/docs/5.1/components/
> mkdir public/assets
$../public> npm init
$> npm i bootstrap

## config gmail
https://www.google.com/settings/security/lesssecureapps


## commandes utilisées
sudo apt install symfony
sudo apt symfony
mkdir symfony
cd symfony/
wget https://get.symfony.com/cli/installer -O - | bash
export PATH="$HOME/.symfony/bin:$PATH".

symfony new example --full
cd symfony/example/
symfony server:start
export PATH="$HOME/.symfony/bin:$PATH".
symfony server:start
symfony server:ca:install
symfony server:start
symfony -v
export PATH="$HOME/.symfony/bin:$PATH".
symfony -v

cd symfony/example/
symfony console make:controller
composer require symfony/webpack-encore-bundle
symfony console make:controller
symfony console make:entity
symfony console make:fixture
composer require fakerino/symfony-fakerino
symfony console make:fixture
composer require fakerino/symfony-fakerino
symfony console make:fixture
symfony console doctrine:fixtures:load
symfony console make:entity
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console make:entity
symfony console doctrine:migrations:migrate
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console make:migration
symfony console make:form

composer require symfony/webpack-encore-bundle
composer require orm-fixtures
composer require fzaninotto/faker
composer require doctrine/doctrine-fixtures-bundle
composer require orm-fixtures
composer require fakerino/symfony-fakerino
composer require --dev fzaninotto/faker
composer require theofidry/alice-data-fixtures --dev
composer require theofidry/alice-data-fixtures --dev
composer require fzaninotto/faker
composer require fakerphp/faker
composer require dompdf/dompdf


## securite
composer require security
symfony console make:user
symfony console make:auth
symfony console doctrine:fixtures:load --group=user --append
symfony console debug:config security

## securité : enregistrer un user
symfony console make:registration-form



## Installation du projet dans un nouvel espace
mkdir example3
git clone https://github.com/jms212004/example.git .
cp .env .env.local
   placer les lignes pour la nouvelle bdd (pense à la créer en amont) 
   et la config pour le mail
   
   DATABASE_URL="mysql://<login>:<pwd>@127.0.0.1:3306/example3?serverVersion=mariadb-10.5.15"
   MAILER_DSN=gmail://<adressemail>:<pwd>@default

composer update
symfony console make:migration
php bin/console doctrine:migrations:migrate

### charger des données de test en bdd
symfony console doctrine:fixtures:load

## creation d un formulaire pour les user (voir vidéo 40)
symfony console make:form
Réponses :
   UserType
   User

Création dans la classe TabControler de la méthode edit#[Route('/edit/{id?0}', name: 'tab.edit')]
    +use App\Entity\User;
    +use App\Form\UserType;

    #[Route('/edit/{id?0}', name: 'tab.edit')]
    public function addUsers(
        ManagerRegistry $doctrine
    ): Response
    {
        $manager = $doctrine->getManager();

        // affichage des informations dans la page detail
        return $this->render('tab/add-utilisateurs.html.twig', [
            
        ]);
    }
Creation du template add-utilisateurs.html.twig


Rajouter la methode dans User.php 
   public function __toString(): string
    {
        return $this->designation;
    }

## creation page erreur (fonctionne uniquement en prod) (pense à vider le cache en prod afin de faire des essais)
### voir https://symfony.com/doc/current/translation.html
- composer require symfony/twig-pack
- placer dans le fichier .env.local APP_ENV=prod
- creation des réperoires templates/bundles/TwigBundle/Exception
- mettre la page avec le  code erreur

> Possible d'accéder à cette page en dev alors mettre dans url https://localhost:8000/index.php/_error/403

## traduction 
- verifier que le module translation est bien installé
- traduire dans les templates
- traduire dans les methodes
-> si on veut afficher avant de réaliser l action (en partant du principe que 'EN' par défaut voir fichier translation.yaml)
> php bin/console translation:extract --dump-messages fr
-> creation des fichiers dans le répertoire translations
> php bin/console translation:extract --force fr
-> mettre à jour 
> php bin/console translation:update --force fr
(provoque la création des fichiers qui peuvent maintenant être modifiés avec les bons textes)
