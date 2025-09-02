#!/bin/bash

# Script d'initialisation Docker pour projets Symfony
# Usage: ./init-docker.sh [port_web] [port_db] [port_pma]

PROJECT_NAME=$(basename $(pwd))
WEB_PORT=${1:-8000}
DB_PORT=${2:-3306}
PMA_PORT=${3:-8080}

echo "🐳 Initialisation Docker pour le projet: $PROJECT_NAME"
echo "📱 Ports: Web=$WEB_PORT, DB=$DB_PORT, phpMyAdmin=$PMA_PORT"

# Arrêter les containers existants avec le même nom
echo "🛑 Arrêt des containers existants..."
docker stop ${PROJECT_NAME}_app ${PROJECT_NAME}_mysql ${PROJECT_NAME}_phpmyadmin ${PROJECT_NAME}_mailhog 2>/dev/null || true
docker rm ${PROJECT_NAME}_app ${PROJECT_NAME}_mysql ${PROJECT_NAME}_phpmyadmin ${PROJECT_NAME}_mailhog 2>/dev/null || true

# Créer le fichier .env.local avec les variables Docker (non versionné)
cat > .env.local << EOF
# Variables d'environnement locales pour Docker - Projet: $PROJECT_NAME
# Ce fichier est ignoré par Git et contient les configurations locales

# Variables Docker
PROJECT_NAME=$PROJECT_NAME
WEB_PORT=$WEB_PORT
DB_PORT=$DB_PORT
PMA_PORT=$PMA_PORT
MAILHOG_WEB_PORT=$((PMA_PORT + 1))
MAILHOG_SMTP_PORT=1025
EOF

# Mettre à jour le .env avec les valeurs par défaut (versionné)
cat > .env << EOF
# Variables d'environnement par défaut - modifiez .env.local pour vos configurations locales
APP_ENV=dev
APP_SECRET=

# Messenger
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
EOF

# Exporter les variables sensibles dans .env.local (non versionné)
cat >> .env.local << EOF

# Base de données MySQL dans Docker
DATABASE_URL="mysql://${PROJECT_NAME}_user:${PROJECT_NAME}_pwd@database:3306/${PROJECT_NAME}_db?serverVersion=8.0.32&charset=utf8mb4"

# Mailer avec Mailhog
MAILER_DSN=smtp://mailhog:1025
EOF

# Créer le fichier docker-compose.yml avec les variables
cat > compose.yaml << EOF
services:
  web:
    build: .
    container_name: ${PROJECT_NAME}_app
    ports:
      - "${WEB_PORT:-8000}:80"
    volumes:
      - ./:/var/www/html
    environment:
      COMPOSER_ALLOW_SUPERUSER: 1
    env_file:
      - ./.env.local
    depends_on:
      - database
    extra_hosts:
      - "host.docker.internal:host-gateway"

  database:
    image: mysql:8.3
    container_name: ${PROJECT_NAME}_mysql
    restart: always
    environment:
      MYSQL_DATABASE: ${PROJECT_NAME}_db
      MYSQL_USER: ${PROJECT_NAME}_user
      MYSQL_PASSWORD: ${PROJECT_NAME}_pwd
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "${DB_PORT:-3306}:3306"
    volumes:
      - ${PROJECT_NAME}_db_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: ${PROJECT_NAME}_phpmyadmin
    restart: always
    environment:
      PMA_HOST: database
      PMA_USER: root
      PMA_PASSWORD: root
    ports:
      - "${PMA_PORT:-8080}:80"
    depends_on:
      - database

  mailhog:
    image: mailhog/mailhog
    container_name: ${PROJECT_NAME}_mailhog
    ports:
      - "${MAILHOG_WEB_PORT:-8025}:8025"
      - "${MAILHOG_SMTP_PORT:-1025}:1025"

volumes:
  ${PROJECT_NAME}_db_data:
EOF

# Exporter les variables d'environnement pour docker-compose
export PROJECT_NAME=$(basename $(pwd))
export WEB_PORT=$WEB_PORT
export DB_PORT=$DB_PORT
export PMA_PORT=$PMA_PORT
export MAILHOG_WEB_PORT=$((PMA_PORT + 1))
export MAILHOG_SMTP_PORT=1025

echo "✅ Configuration terminée!"
echo "🚀 Lancement des containers..."

# Construire et lancer
docker-compose up -d --build

# Vérifier et installer les dépendances Composer si nécessaire
if [ ! -d "vendor" ]; then
    echo "📦 Installation des dépendances Composer..."
    docker-compose exec web composer install
fi

echo "🌐 Services disponibles:"
echo "   - Application: http://localhost:$WEB_PORT"
echo "   - phpMyAdmin: http://localhost:$PMA_PORT"
echo "   - MailHog: http://localhost:$((PMA_PORT + 1))"
echo "   - MySQL: localhost:$DB_PORT"