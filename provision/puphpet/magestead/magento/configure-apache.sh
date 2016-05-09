#!/usr/bin/env bash

APP_NAME=${1};
DIR=${2};
BASE_URL=${3};
OS=${4};

if [ $OS = "ubuntu14" ]; then
    LOG_LOCATION="apache2"
else
    LOG_LOCATION="httpd"
fi

vhost="
# ************************************
# Vhost template applied during provision
# Managed by Magestead
# ************************************

<VirtualHost *:80>
  ServerName $BASE_URL

  ## VHOST docroot
  DocumentRoot \"$DIR/public\"

  <Directory \"$DIR/public\">
    Options Indexes FollowSymlinks MultiViews
    AllowOverride All
    Require all granted

    <FilesMatch \"\.php\$\">
      Require all granted
      SetHandler proxy:fcgi://127.0.0.1:9090
    </FilesMatch>

  </Directory>

  ## Logging
  ServerSignature Off
  ErrorLog  \"/var/log/$LOG_LOCATION/$BASE_URL-error.log\"
  CustomLog \"/var/log/$LOG_LOCATION/$BASE_URL-access.log\" combined

  ## SetEnv/SetEnvIf for environment variables
  SetEnv MAGE_IS_DEVELOPER_MODE true
  SetEnv MAGE_RUN_CODE default
  SetEnv MAGE_RUN_TYPE store

</VirtualHost>
"

if [ $OS = "ubuntu14" ]; then
    sudo echo "$vhost" > "/etc/apache2/sites-available/$APP_NAME.conf"
    cd /etc/apache2/sites-enabled;
    sudo ln -s "/etc/apache2/sites-available/$APP_NAME.conf"
    sudo service apache2 restart
fi

if [ $OS = "centos65" ]; then
    sudo echo "$vhost" > "/etc/httpd/conf.d/$APP_NAME.conf"
    sudo service httpd restart
fi




