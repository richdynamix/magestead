#!/bin/bash

DIR=${1};
LOCALE=${2};
CURRENCY=${3};
DB_NAME=${4};
BASE_URL=${5};

sudo mkdir $DIR
if [ $? -ne 0 ] ; then
    echo "There was an error creating the root folder..."
else
    echo "Successfully created $DIR"
fi

# Clone the repo
cd $DIR;
if [ ! -d $DIR/magento2 ]; then
    echo "--- Cloning Magento 2 Repo ---"
    git clone https://github.com/magento/magento2.git
fi

echo "--- Setting Permissions ---"
cd magento2;

# redis no longer installed by default
/usr/local/bin/composer require predis/predis;

sudo find . -type d -exec chmod 700 {} \; &&
sudo find . -type f -exec chmod 600 {} \; &&
sudo chmod +x bin/magento
/usr/local/bin/composer install;

echo "--- Exporting PATH ---"
export PATH=$PATH:/$DIR/magento2/bin;

echo "--- Installing Magento 2 ---"
# Run the setup wizard from command line
magento setup:install --base-url=http://$BASE_URL/ \
--db-host=localhost \
--db-name=$DB_NAME \
--db-user=magestead \
--db-password=vagrant \
--admin-firstname=RichDynamix \
--admin-lastname=Magestead \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=password123 \
--language=$LOCALE \
--currency=$CURRENCY \
--timezone=Europe/London \
--use-rewrites=1 \
--session-save=db

echo "Magento admin username = admin";
echo "Magento admin password = password123";
echo "Magento installed at http://$BASE_URL/";
