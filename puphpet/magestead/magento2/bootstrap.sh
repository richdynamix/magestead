#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

VAGRANT_CORE_FOLDER=$(cat '/.puphpet-stuff/vagrant-core-folder.txt')

APP_NAME=${1};
DIR=${2};
LOCALE=${3};
CURRENCY=${4};
DB_NAME=${5};
BASE_URL=${6};
REDIS_INSTALL=${7};

if [ -d "/.puphpet-stuff/${APP_NAME}-ran" ]; then
    rm -rf "/.puphpet-stuff/${APP_NAME}-ran"
fi

if [ ! -f "/.puphpet-stuff/${APP_NAME}-ran" ]; then
   sudo touch "/.puphpet-stuff/${APP_NAME}-ran"
   echo "Created file /.puphpet-stuff/${APP_NAME}-ran"
fi

# if ! grep -x -q "${APP_NAME}" "/.puphpet-stuff/${APP_NAME}-ran"; then
    # sudo /bin/bash -c "echo \"${APP_NAME}\" >> \"/.puphpet-stuff/${APP_NAME}-ran\""

	echo "--- Installing Database for Magento 2 ---"
  /bin/bash /vagrant/puphpet/magestead/install-db.sh $DB_NAME
    
	echo "--- Installing Magento 2 Software ---"
  /bin/bash /vagrant/puphpet/magestead/magento2/install.sh $DIR $LOCALE $CURRENCY $DB_NAME $BASE_URL

  echo "--- Configuring NGINX VHOST for Magento ---"
  /bin/bash /vagrant/puphpet/magestead/magento2/configure-nginx.sh $APP_NAME $DIR $BASE_URL

  echo "--- Finalising Setup ---"
  /bin/bash /vagrant/puphpet/magestead/magento2/finalise.sh $DIR

  # if [ $REDIS_INSTALL = "1" ]; then
    # echo "--- Configuring Magento Sessions with Redis ---"
    # /bin/bash /vagrant/puphpet/magestead/magento2/redis-sessions.sh $DIR
  # fi

# else
#     echo "Skipping magento 2 bootstrap for ${DIR} as contents have not changed"
# fi