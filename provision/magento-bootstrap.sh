#!/usr/bin/env bash

DB=$1;
domain=$2;

echo "--- Bootstrapping Magento ---"
cp -R /vagrant/provision/stubs/magento-composer.json /vagrant/composer.json
cd /vagrant;
/usr/local/bin/composer install;

cp -R /vagrant/provision/stubs/services.xml /vagrant/magento/app/etc/services.xml;

echo "--- Installing Magento ---"

cd /vagrant/magento;

php -f install.php -- \
--license_agreement_accepted "yes" \
--locale "en_GB" \
--timezone "Europe/London" \
--default_currency "GBP" \
--db_host "localhost" \
--db_name "$DB" \
--db_user "root" \
--db_pass "root" \
--url "$domain" \
--use_rewrites "yes" \
--skip_url_validation "yes" \
--use_secure "no" \
--use_secure_admin "no" \
--secure_base_url "$domain" \
--admin_firstname "RichDynamix" \
--admin_lastname "Magestead" \
--admin_email "admin@admin.com" \
--admin_username "admin" \
--admin_password "password123"

echo "Magento admin username = admin";
echo "Magento admin password = password123";
echo "Magento installed at http://$domain/. Remember and set your hosts file.";
