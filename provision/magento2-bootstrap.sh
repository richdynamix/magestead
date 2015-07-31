#!/usr/bin/env bash

DB=$1;
domain=$2;

# Clone the repo
cd /vagrant;
git clone git@github.com:magento/magento2.git magento2;

# Set permissions
cd magento2;
sudo find . -type d -exec chmod 700 {} \; && sudo find . -type f -exec chmod 600 {} \; && sudo chmod +x bin/magento
/usr/local/bin/composer install;

# Export the path to use global
export PATH=$PATH:/vagrant/magento2/bin;

# Create the NGINX server block
block="# Magento Vars

set $MAGE_ROOT /vagrant/magento2;
set $MAGE_MODE default; # or production or developer

# Example configuration:
 upstream fastcgi_backend {
    # use tcp connection
    server  127.0.0.1:9000;
    # or socket
    # server   unix:/var/run/php5-fpm.sock;
 }
 server {
    listen 80;
    server_name mage.dev;
    set $MAGE_ROOT /var/www/magento2;
    set $MAGE_MODE developer;
    include /vagrant/magento2/nginx.conf.sample;
 }
"

# Add the block and restart PHP-FPM and NGINX
echo "$block" > "/etc/nginx/conf.d/$1"
sudo service nginx restart
sudo service php-fpm restart

# Run the setup wizard from command line

magento setup:install --base-url=http://$domain/ \
--db-host=localhost \
--db-name=$DB \
--db-user=root \
--db-password=root \
--admin-firstname=Magento \
--admin-lastname=Admin \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=password123 \
--language=en_GB \
--currency=GBP \
--timezone=Europe/London \
--use-rewrites=1 \
--session-save=db

echo "Magento admin username = admin";
echo "Magento admin password = password123";
echo "Magento installed at http://$domain/. Remember and set your hosts file.";