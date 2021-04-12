# Projet 8-ToDo & Co

Base du projet #8 : Améliorez un projet existant

Ce qui est demandé : 
* Corrections d'anomalies.
* Implémentation de nouvelles fonctionnalités.
* Implémentation de tests automatisées.
* Fournir une documentation technique.
* Faire un audit de performance.

Les livrables sont disponibles dans `./deliverables/`. Le dossier contient :
* Un dossier `test-coverage.zip` : analyse généré par PHPUnit indiquant le taux de couverture de code des tests 
  réalisées.
* Un dossier `UMLDiagrams.zip` : Diagrammes UML demandés.
* Un fichier `CONTRIBUTE.md` : Documentation indiquant comment contribuer au projet.
* Un fichier `Symfony_Authentication.pdf` : Documentation technique concernant l'authentification.
* Un fichier `Audit.pdf` : Rapport audit de qualité de code et de performance.

## Environnement utilisé durant le développement
* [Symfony 4.4.21 LTS](https://symfony.com/doc/4.4/setup.html) 
* [Composer 2.0.11](https://getcomposer.org/doc/00-intro.md)
* MAMP 6 (985)
    * Apache 2.4.46
    * PHP 7.3.27
    * MySQL 5.7.30

## Installation
1- Clonez le repository GitHub dans le dossier voulu :
```
    git clone https://github.com/ProfesseurOrme/ToDoAndCo.git
```

2- Placez vous dans le répertoire de votre projet et installez les dépendances du projet avec la commande de [Composer](https://getcomposer.org/doc/00-intro.md) :
```
    composer install
```

3- Configurez vos variables d'environnement dans le fichier `.env` tel que :

* La connexion à la base de données  :
```
    DATABASE_URL=mysql://db.username:db.password@127.0.0.1:3306/todo_and_co
```

3- Si le fichier `.env` est correctement configuré, créez la base de données avec la commande ci-dessous :
```
    php bin/console doctrine:database:create
```
4- Créez les différentes tables de la base de données :
```
    php bin/console doctrine:migrations:migrate
```
5- Installer des données fictives avec des fixtures pour agrémenter le site :
```
    php bin/console doctrine:fixtures:load
```
6- Votre projet est prêt à l'utilisation ! Pour utiliser l'application dans un environnement local, veuillez vous
 renseigner sur cette
 [documentation](https://symfony.com/doc/4.4/setup.html#running-symfony-applications).

## Tester l'application (optionnel)

Des tests ont été écrits afin de vérifier l'intégrité et le fonctionnement de l'application. Afin de pouvoir les 
lancer :

1- Créez un fichier `.env.test.local`, avec une variable d'environnement qui contient l'adresse de la base de 
données de test voulue:
```
    DATABASE_URL=mysql://db.username:db.password@127.0.0.1:3306/todo_and_co_test
```

2- Créez la base de données de test :
```
    php bin/console doctrine:database:create --env=test
```

3- Lancez le test via la commande : 
```
    /vendor/bin/phpunit
```

4-ous pouvez générer un test de couverture de code avec la commande ci-dessous (Le résultat est disponible à dans `./html/test-coverage/index.html`:
```
    /vendor/bin/phpunit --coverage-html html/test-coverage
```