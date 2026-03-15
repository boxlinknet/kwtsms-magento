#!/bin/bash
set -e

echo "=== Installing Magento 2.4.7 ==="

# Check if Magento is already installed
if [ -f /var/www/html/app/etc/env.php ]; then
    echo "Magento already installed. Skipping."
    exit 0
fi

cd /var/www/html

# Create Magento project via Composer
echo "=== Downloading Magento via Composer ==="
composer create-project --repository-url=https://repo.magento.com/ \
    magento/project-community-edition=2.4.7 . \
    --no-interaction

# Install Magento
echo "=== Running Magento Setup ==="
php bin/magento setup:install \
    --base-url=http://localhost:8085 \
    --db-host=db \
    --db-name=magento \
    --db-user=magento \
    --db-password=magento \
    --admin-firstname=Admin \
    --admin-lastname=User \
    --admin-email=admin@example.com \
    --admin-user=admin \
    --admin-password=Admin123! \
    --language=en_US \
    --currency=KWD \
    --timezone=Asia/Kuwait \
    --use-rewrites=1 \
    --search-engine=opensearch \
    --opensearch-host=opensearch \
    --opensearch-port=9200 \
    --session-save=redis \
    --session-save-redis-host=redis \
    --session-save-redis-port=6379 \
    --session-save-redis-db=0 \
    --cache-backend=redis \
    --cache-backend-redis-server=redis \
    --cache-backend-redis-port=6379 \
    --cache-backend-redis-db=1 \
    --no-interaction

# Set developer mode
php bin/magento deploy:mode:set developer

# Disable two-factor auth for local dev
php bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth
php bin/magento setup:upgrade
php bin/magento cache:flush

# Fix permissions
find var generated pub/static pub/media app/etc -type f -exec chmod g+w {} + 2>/dev/null || true
find var generated pub/static pub/media app/etc -type d -exec chmod g+ws {} + 2>/dev/null || true

echo ""
echo "=== Magento 2.4.7 Installed ==="
echo "Storefront: http://localhost:8085"
echo "Admin: http://localhost:8085/admin"
echo "Admin user: admin"
echo "Admin pass: Admin123!"
echo "phpMyAdmin: http://localhost:8082"
echo ""
