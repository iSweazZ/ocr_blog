# OpenClassrooms

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6c3ee61817084a68917c9fb62b058773)](https://app.codacy.com/gh/iSweazZ/ocr_blog?utm_source=github.com&utm_medium=referral&utm_content=iSweazZ/ocr_blog&utm_campaign=Badge_Grade)

- Développeur d'application
- Parcours PHP/Symfony
- Projet 5

## Créez votre premier blog en PHP

### À propos

Bonjour et bienvenue sur le dépôt de mon travail, qui traite du cinquième projet d'OpenClassrooms, intutulé **Créez votre premier blog en PHP** ! Vous trouverez, ci-après, la procédure d'installation pour prendre en main le code du projet, ainsi que la base de données et sa structure, conçue pour fonctionner avec.

Vous trouverez également, dans le dossier **diagrams**, les diagrammes UML conçus en amont du projet, ainsi que le compte rendu de qualité de code, disponible en cliquant sur le bouton **Codacy**, présent ci-dessus.


**PHP • Twig • JS • CSS • MVC • POO**

---

## Remarque

Pour pouvoir installer ce projet, le gestionnaire de dépendance **Composer** doit être présent sur votre machine, ainsi qu'un serveur local sous **PHP 8.2**. Si vous ne disposez pas de ces outils, vous pourrez les télécharger et les installer, en suivant ces liens :
- Télécharger [Composer](https://getcomposer.org/)
- Télécharger [Wamp](https://www.wampserver.com/) (Windows)
- Télécharger [Mamp](https://www.wampserver.com/) (Mamp)

---

## Installation

1. À l'aide d'un terminal, créez un dossier à l'emplacement souhaité pour l'installation du projet. Lancez la commande suivante :
```shell
git clone https://github.com/iSweazZ/ocr_blog.git
```

2. Lancez cette commande pour vous rendre dans le dossier adequat :
```shell
cd ocr_blog
```

3. À la racine de ce répertoire, lancez la commande suivante pour installer les dépendances Composer :
```shell
composer install
```

4. Vous devez maintenant modifier le fichier `.env.exemple` situé à la racine du projet et le renommer en `.env`. Remplissez `DB_HOST`, `DB_DBNAME` en renseignant vos identifiants de la base de données locale :
```dotenv
DB_HOST=localhost
DB_DBNAME=
DB_USER=
DB_PASSWORD=
DB_PROTOCOL=mysql
DB_CHARSET=utf8
```

5. Ensuite, importez simplement le fichier `badiwissem.sql`, présent à la racine du projet, dans votre base de données SQL locale. Si toutes les informations ont correctement été renseignées, la connexion devrait se faire automatiquement.

6. Créez votre hôte virtuel, pointant vers le dossier **public** du projet afin de pouvoir l'ouvrir avec une URL locale.

7. Pour pouvoir tester les controllers, veuillez utiliser les identifiants par défaut :
- Admin
    - ID : admin@admin.com
    - MDP : 123456
- User
    - ID : user@user.com
    - MDP : 123456

### Merci pour votre attention !