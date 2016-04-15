#!/bin/bash

DIR=${1};
LOCALE=${2};
CURRENCY=${3};
DB_NAME=${4};
BASE_URL=${5};

sudo mkdir $DIR
if [ $? -ne 0 ] ; then
    echo "fatal"
else
    echo "success"
fi

echo "--- Created $DIR ---";

sudo cp -R /vagrant/puphpet/magestead/magento/stubs/composer.json "$DIR/composer.json"
cd $DIR;
/usr/local/bin/composer install;

echo "--- Installing Magento ---"
cd "$DIR/magento";

php -f install.php -- \
--license_agreement_accepted "yes" \
--locale "$LOCALE" \
--timezone "Europe/London" \
--default_currency "$CURRENCY" \
--db_host "localhost" \
--db_name "$DB_NAME" \
--db_user "magestead" \
--db_pass "vagrant" \
--session_save "db" \
--url "http://$BASE_URL/" \
--use_rewrites "yes" \
--skip_url_validation "yes" \
--use_secure "no" \
--use_secure_admin "no" \
--secure_base_url "http://$BASE_URL/" \
--admin_firstname "RichDynamix" \
--admin_lastname "Magestead" \
--admin_email "admin@admin.com" \
--admin_username "admin" \
--admin_password "password123"

echo "Magento admin username = admin";
echo "Magento admin password = password123";
echo "Magento installed at http://$BASE_URL/";

echo "--- Setting Permissions ---"
sudo find . -type f -exec chmod 400 {} \;
sudo find . -type d -exec chmod 500 {} \;
sudo find var/ -type f -exec chmod 600 {} \;
sudo find media/ -type f -exec chmod 600 {} \;
sudo find var/ -type d -exec chmod 700 {} \;
sudo find media/ -type d -exec chmod 700 {} \;
sudo chmod 700 includes;
sudo chmod 600 includes/config.php;