#!/usr/bin/env bash

# Installer PHP et Composer si non présents
if ! command -v php &> /dev/null; then
    echo "PHP n'est pas installé. Installation en cours..."
    curl -s https://php.8.3/install.sh | bash
fi

if ! command -v composer &> /dev/null; then
    echo "Composer n'est pas installé. Installation en cours..."
    curl -s https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Installer les dépendances
composer install --optimize-autoloader --no-dev

# Exécuter les commandes Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
