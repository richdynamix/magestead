#!/usr/bin/env bash

echo "--- Bootstrapping Symfony 2 ---"
cd /vagrant;

if [ ! -d /vagrant/symfony ]; then
    echo "Installing Symfony"
    /usr/local/bin/composer create-project symfony/framework-standard-edition symfony;
fi

block="server {
    listen 80;
    server_name $1;
    root $2;
    autoindex on;

	index app_dev.php;

	try_files \$uri \$uri/ @rewrite;

	location @rewrite {
		rewrite ^/(.*)$ /app_dev.php/\$1;
	}

	location ~ \.php {
		fastcgi_pass   127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_index  app_dev.php;

		fastcgi_param PATH_INFO \$fastcgi_path_info;
		fastcgi_param PATH_TRANSLATED \$document_root\$fastcgi_path_info;
		fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;

		include fastcgi_params; ## See /etc/nginx/fastcgi_params

		fastcgi_param REMOTE_ADDR 127.0.0.1;
	}

	location ~ /\.ht {
		deny all;
	}
}
"

echo "$block" > "/etc/nginx/conf.d/$1"
sudo service nginx restart
sudo service php-fpm restart