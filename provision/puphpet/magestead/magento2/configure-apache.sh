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

  DocumentRoot \"$DIR/public/pub\"

  <Directory \"$DIR/public/pub\">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
    Order allow,deny
    allow from all

    <FilesMatch \"\.php\$\">
      Require all granted
      SetHandler proxy:fcgi://127.0.0.1:9090
    </FilesMatch>
  </Directory>

  ErrorLog  \"/var/log/$LOG_LOCATION/$BASE_URL-error.log\"
  LogLevel warn
  CustomLog \"/var/log/$LOG_LOCATION/$BASE_URL-access.log\" combined

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


