# 🐳 Configuration Docker pour Symfony

Cette configuration Docker permet de faire tourner des projets Symfony avec MySQL, phpMyAdmin et MailHog de manière isolée et sans conflits entre projets.

## 📋 Prérequis

- Docker et Docker Compose installés
- Symfony CLI (optionnel, pour créer de nouveaux projets)

## 🚀 Utilisation pour un nouveau projet Symfony

### 1️⃣ Créer le projet Symfony

```bash
# Créer le nouveau projet
symfony new mon-nouveau-projet --version="7.*" --webapp
cd mon-nouveau-projet
```

### 2️⃣ Copier les fichiers Docker

```bash
# Depuis le répertoire de ton nouveau projet, copier depuis ce template :
cp /chemin/vers/ce-projet/Dockerfile .
cp /chemin/vers/ce-projet/compose.yaml .
cp /chemin/vers/ce-projet/init-docker.sh .
cp -r /chemin/vers/ce-projet/docker/ .
cp /chemin/vers/ce-projet/.dockerignore .
```

### 3️⃣ Initialiser Docker pour le projet

```bash
# Rendre le script exécutable
chmod +x init-docker.sh

# Lancer l'initialisation avec des ports personnalisés
./init-docker.sh mon-nouveau-projet 8001 3307 8081
```

### 4️⃣ Configurer Symfony

```bash
# Copier la configuration Docker vers l'environnement local
cp .env.docker .env.local
```

### 5️⃣ Vérifier l'installation

```bash
# Vérifier que les containers tournent
docker ps

# Tester l'application
curl http://localhost:8001
```

## 🎯 Services disponibles

Après l'initialisation, les services seront accessibles sur :

- **🌐 Application Symfony** : http://localhost:[PORT_WEB]
- **🗄️ phpMyAdmin** : http://localhost:[PORT_PMA]
- **📧 MailHog (emails de test)** : http://localhost:[PORT_PMA+1]
- **🐬 MySQL** : localhost:[PORT_DB]

## ⚡ Version rapide (une seule commande)

```bash
# Créer et configurer un nouveau projet en une fois
symfony new mon-projet --webapp && cd mon-projet && \
cp /chemin/vers/template/{Dockerfile,compose.yaml,init-docker.sh,.dockerignore} . && \
cp -r /chemin/vers/template/docker/ . && \
chmod +x init-docker.sh && \
./init-docker.sh mon-projet 8001 3307 8081 && \
cp .env.docker .env.local
```

## 📝 Gestion des ports (éviter les conflits)

Utilise des ports différents pour chaque projet :

| Projet   | Port Web | Port DB | Port phpMyAdmin | Port MailHog |
| -------- | -------- | ------- | --------------- | ------------ |
| Projet 1 | 8000     | 3306    | 8080            | 8025         |
| Projet 2 | 8001     | 3307    | 8081            | 8026         |
| Projet 3 | 8002     | 3308    | 8082            | 8027         |

## 🛠️ Commandes utiles

### Gestion des containers

```bash
# Démarrer les services
docker-compose up -d

# Arrêter les services
docker-compose down

# Voir les logs
docker-compose logs -f

# Reconstruire les images
docker-compose up -d --build
```

### Accès aux containers

```bash
# Shell dans le container web
docker exec -it [PROJET_NAME]_app bash

# Shell dans le container MySQL
docker exec -it [PROJET_NAME]_mysql bash
```

### Nettoyage

```bash
# Supprimer les containers et volumes du projet
docker-compose down -v

# Supprimer l'image du projet
docker rmi [PROJET_NAME]-web
```

## 🏗️ Structure des fichiers

```
mon-projet/
├── Dockerfile                 # Image PHP 8.3 + Apache + Symfony CLI
├── compose.yaml              # Configuration des services Docker
├── init-docker.sh           # Script d'initialisation automatique
├── .dockerignore            # Fichiers à ignorer lors du build
├── .env.docker             # Variables d'environnement pour Docker
└── docker/                 # Configuration Docker
    ├── apache/
    │   └── vhost.conf      # Configuration Apache
    └── php/
        ├── php.ini         # Configuration PHP
        └── opcache.ini     # Configuration OPcache
```

## 🔧 Personnalisation

### Modifier les ports

Édite le fichier `compose.yaml` ou utilise les variables d'environnement :

```bash
export PROJECT_NAME=mon-projet
export WEB_PORT=8001
export DB_PORT=3307
export PMA_PORT=8081
docker-compose up -d
```

### Modifier la configuration PHP

Édite les fichiers dans `docker/php/` et rebuilde :

```bash
docker-compose up -d --build
```

### Executer les migrations

```bash
docker exec -it mon-projet_app symfony console doctrine:migrations:migrate
```

### Executer les fixtures

```bash
docker exec -it mon-projet_app symfony console d:f:l
```

### Installer Bootstrap

Installer Bootstrap dans le container web :

```bash
docker exec -it mon-projet_app symfony console importmap:require bootstrap/dist/css/bootstrap.min.css
```

Ajouter une feuille de style :

```js
// assets/app.js
// Import Bootstrap CSS
import "bootstrap/dist/css/bootstrap.min.css";
```

Asset Map Compiler : (/public)

```bash
docker exec -it mon-projet_app symfony console asset-map:compile
```

## ❗ Résolution de problèmes

### Conflits de containers

```bash
# Arrêter tous les containers avec le même nom
docker stop $(docker ps -q --filter name=mon-projet)
docker rm $(docker ps -aq --filter name=mon-projet)
```

### Conflits de ports

```bash
# Voir quels ports sont utilisés
netstat -tulpn | grep :8000
# ou
docker ps --format "table {{.Names}}\t{{.Ports}}"
```

### Rebuilder complètement

```bash
docker-compose down -v
docker rmi mon-projet-web
docker-compose up -d --build
```

---

🎉 **C'est tout !** Ton projet Symfony tourne maintenant avec Docker sans conflits !
