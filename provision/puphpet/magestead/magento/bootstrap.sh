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
WEBSERVER=${8};
OS=${9};
ADVANCED=${10};

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
  /bin/bash /vagrant/puphpet/magestead/install-db.sh $DB_NAME

  if [ $WEBSERVER = "apache" ]; then
    echo "--- Configuring APACHE VHOST for Magento ---"
    /bin/bash /vagrant/puphpet/magestead/magento/configure-apache.sh $APP_NAME $DIR $BASE_URL $OS
  fi

  if [ $WEBSERVER = "nginx" ]; then
    echo "--- Configuring NGINX VHOST for Magento ---"
    /bin/bash /vagrant/puphpet/magestead/magento/configure-nginx.sh $APP_NAME $DIR $BASE_URL
  fi


  if [ $ADVANCED = "true" ]; then
    echo "--- Installing Magento With Composer ---"
    /bin/bash /vagrant/puphpet/magestead/magento/install.sh $DIR $LOCALE $CURRENCY $DB_NAME $BASE_URL

    if [ $REDIS_INSTALL = "1" ]; then
      echo "--- Configuring Magento Sessions with Redis ---"
      /bin/bash /vagrant/puphpet/magestead/magento/redis-sessions.sh $DIR
    fi

    echo "--- Installing Magerun ---"
    /bin/bash /vagrant/puphpet/magestead/magento/magerun.sh $DIR

    echo "--- Finalising Setup ---"
    /bin/bash /vagrant/puphpet/magestead/magento/finalise.sh $DIR
  fi

else
    echo "Skipping magento bootstrap for ${DIR} as contents have not changed"
fi