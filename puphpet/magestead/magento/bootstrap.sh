#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

VAGRANT_CORE_FOLDER=$(cat '/.puphpet-stuff/vagrant-core-folder.txt')

APP_NAME=${1};
DIR=${2};
LOCALE=${3};
CURRENCY=${4};
DB_NAME=${5};
SESSION_SAVE=${6};
BASE_URL=${7};

if [ -d "/.puphpet-stuff/${APP_NAME}-ran" ]; then
    rm -rf "/.puphpet-stuff/${APP_NAME}-ran"
fi

if [ ! -f "/.puphpet-stuff/${APP_NAME}-ran" ]; then
   sudo touch "/.puphpet-stuff/${APP_NAME}-ran"
   echo "Created file /.puphpet-stuff/${APP_NAME}-ran"
fi

if ! grep -x -q "${APP_NAME}" "/.puphpet-stuff/${APP_NAME}-ran"; then
    sudo /bin/bash -c "echo \"${APP_NAME}\" >> \"/.puphpet-stuff/${APP_NAME}-ran\""

	echo "--- Installing Database for Magento ---"
    /bin/bash /vagrant/puphpet/magestead/magento/install-db.sh $DB_NAME
    
	echo "--- Installing Magento With Composer ---"
    /bin/bash /vagrant/puphpet/magestead/magento/install.sh $DIR $LOCALE $CURRENCY $DB_NAME $SESSION_SAVE $BASE_URL

    echo "--- Configuring NGINX VHOST for Magento ---"
    /bin/bash /vagrant/puphpet/magestead/magento/configure-nginx.sh $APP_NAME $DIR $BASE_URL

else
    echo "Skipping magento bootstrap for ${DIR} as contents have not changed"
fi