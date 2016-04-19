#!/usr/bin/env bash

APP_NAME=${1};
DIR=${2};
BASE_URL=${3};

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
  ErrorLog  \"/var/log/httpd/$BASE_URL_error.log\"
  CustomLog \"/var/log/httpd/$BASE_URL_access.log\" combined

  ## SetEnv/SetEnvIf for environment variables
  SetEnv MAGE_IS_DEVELOPER_MODE true
  SetEnv MAGE_RUN_CODE default
  SetEnv MAGE_RUN_TYPE store

</VirtualHost>
"

sudo echo "$vhost" > "/etc/httpd/conf.d/$APP_NAME.conf"
sudo service httpd restart
sudo service php-fpm restart


