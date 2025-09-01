# Configuration Docker pour Symfony 7.3

## Services inclus

### 🚀 Application Symfony

-   **Container**: `symfony_app`
-   **Port**: `8000`
-   **PHP**: 8.3 avec Apache
-   **Inclus**: Composer, Symfony CLI

### 🗄️ Base de données MySQL

-   **Container**: `mysql_db`
-   **Port**: `3306`
-   **Version**: MySQL 8.3
-   **Base**: `symfony_db`
-   **Utilisateur**: `symfony` / `symfony`

### 📧 Mailhog (Test emails)

-   **Container**: `mailhog`
-   **SMTP Port**: `1025`
-   **Web UI Port**: `8025`
-   **URL**: http://localhost:8025

### 🗃️ phpMyAdmin

-   **Container**: `phpmyadmin`
-   **Port**: `8080`
-   **Interface web pour MySQL**

## Fichiers de configuration

-   **compose.yaml** - Configuration complète des services
-   **Dockerfile** - Image Symfony optimisée
-   **docker/** - Configurations Apache, PHP, MySQL
-   **.env.docker** - Variables d'environnement Docker
-   **.dockerignore** - Exclusions pour le build

## Commandes Docker

### Démarrer l'environnement

```bash
docker compose up -d
```

### Voir les logs

```bash
docker compose logs -f app
```

### Accéder au container Symfony

```bash
docker compose exec app bash
```

### Exécuter Composer

```bash
docker compose exec app composer install
```

### Exécuter les migrations

```bash
docker compose exec app php bin/console doctrine:migrations:migrate
```

### Arrêter l'environnement

```bash
docker compose down
```

## Démarrage rapide

```bash
# Démarrer les services
docker compose up -d

# Installer les dépendances
docker compose exec app composer install

# Configurer la base de données
docker compose exec app php bin/console doctrine:database:create
docker compose exec app php bin/console doctrine:migrations:migrate
```

## Memento des commandes utiles

```bash
# Voir les logs
docker compose logs web

# Accéder au conteneur
docker compose exec web bash

# Exécuter des commandes Symfony
docker compose exec web php bin/console cache:clear
docker compose exec web php bin/console make:migration
docker compose exec web php bin/console doctrine:migrations:migrate
docker compose exec web php bin/console doctrine:fixtures:load

# Reconstruire les assets Tailwind
docker compose exec web php bin/console tailwind:build
```

## URLs d'accès

-   **Application**: http://localhost:8000
-   **phpMyAdmin**: http://localhost:8080
-   **Mailhog**: http://localhost:8025

## Configuration optimisée

Cette configuration Docker a été optimisée pour Symfony 7.3 avec :

-   ✅ Analyse des fichiers Docker existants
-   ✅ Examen des dépendances du projet
-   ✅ Optimisation du compose.yaml avec les services requis
-   ✅ Configuration des variables d'environnement appropriées
