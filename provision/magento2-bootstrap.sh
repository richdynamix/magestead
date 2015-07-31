#!/usr/bin/env bash

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