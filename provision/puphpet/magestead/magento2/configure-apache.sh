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

  ErrorLog \"/var/log/httpd/$BASE_URL_error.log\"
  LogLevel warn
  CustomLog \"/var/log/httpd/$BASE_URL_access.log\" combined

</VirtualHost>
"

sudo echo "$vhost" > "/etc/httpd/conf.d/$APP_NAME.conf"
sudo service httpd restart
sudo service php-fpm restart


