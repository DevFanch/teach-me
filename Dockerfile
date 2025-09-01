# Utilise PHP 8.3 avec Apache
FROM php:8.3-apache

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        intl \
        pdo \
        pdo_mysql \
        opcache \
        xml \
        simplexml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Installer Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install -y symfony-cli

# Configurer Apache
RUN a2enmod rewrite
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Configuration PHP optimisée pour Symfony
COPY docker/php/php.ini /usr/local/etc/php/conf.d/symfony.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copier le code de l'application
COPY . /var/www/html/

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Installer les dépendances PHP si composer.lock existe
RUN if [ -f composer.lock ]; then composer install --no-dev --optimize-autoloader --no-scripts; fi

# Exposer le port Apache
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]