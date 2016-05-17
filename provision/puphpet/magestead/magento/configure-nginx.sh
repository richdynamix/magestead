#!/usr/bin/env bash

APP_NAME=${1};
DIR=${2};
BASE_URL=${3};

block="server {
    listen 80;
    server_name $BASE_URL;
    root \"$DIR/public\";
    autoindex on;

    index index.html index.htm index.php;

    charset utf-8;

    ##
    # dont log robots.txt requests
    ##
    location /robots.txt {
        allow all;
        log_not_found off;
        access_log off;
    }

    access_log /var/log/nginx/$BASE_URL-access.log;
    error_log /var/log/nginx/$BASE_URL-error.log error;

    ## These locations would be hidden by .htaccess normally
    location ^~ /app/                { deny all; }
    location ^~ /includes/           { deny all; }
    location ^~ /lib/                { deny all; }
    location ^~ /media/downloadable/ { deny all; }
    location ^~ /pkginfo/            { deny all; }
    location ^~ /report/config.xml   { deny all; }
    location ^~ /var/                { deny all; }
    location /var/export/            { deny all; }
    # deny htaccess files
    location ~ /\. {
        deny  all;
        access_log off;
        log_not_found off;
    }

    ##
    # Rewrite for versioned CSS+JS via filemtime
    ##
    location ~* ^.+\.(css|js)\$ {
        rewrite ^(.+)\.(\d+)\.(css|js)\$ \$1.\$3 last;
        expires 31536000s;
        access_log off;
        log_not_found off;
        add_header Pragma public;
        add_header Cache-Control \"max-age=31536000, public\";
    }
    ##
    # Aggressive caching for static files
    # If you alter static files often, please use 
    # add_header Cache-Control \"max-age=31536000, public, must-revalidate, proxy-revalidate\";
    ##
    location ~* \.(asf|asx|wax|wmv|wmx|avi|bmp|class|divx|doc|docx|eot|exe|gif|gz|gzip|ico|jpg|jpeg|jpe|mdb|mid|midi|mov|qt|mp3|m4a|mp4|m4v|mpeg|mpg|mpe|mpp|odb|odc|odf|odg|odp|ods|odt|ogg|ogv|otf|pdf|png|pot|pps|ppt|pptx|ra|ram|svg|svgz|swf|tar|t?gz|tif|tiff|ttf|wav|webm|wma|woff|wri|xla|xls|xlsx|xlt|xlw|zip)\$ {
        expires 31536000s;
        access_log off;
        log_not_found off;
        add_header Pragma public;
        #add_header Cache-Control \"max-age=31536000, public\";
        add_header Cache-Control \"max-age=31536000, public, must-revalidate, proxy-revalidate\";
    }

    # error pages
    error_page  404              /404.html;
    location = /404.html {
        root   /usr/share/nginx/html;
    }
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }
    
    location / {
        try_files /maintenance.html \$uri \$uri/ @handler; ## If missing pass the URI to Magento's front handler
        expires 30d; ## Assume all files are cachable
    }
    location @handler { ## Magento uses a common front handler
        rewrite / /index.php;
    }
    location ~ .php/ { ## Forward paths like /js/index.php/x.js to relevant handler
        rewrite ^(.*.php)/ \$1 last;
    }
    
    location ~ \.php\$ {
        #try_files \$uri =404;

        if (!-e \$request_filename) { rewrite / /index.php last; }

        expires        off;
        fastcgi_pass   127.0.0.1:9090;
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_index  index.php;

        fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;

        ## Store code is defined in administration > Configuration > Manage Stores
        fastcgi_param  MAGE_RUN_CODE default;
        fastcgi_param  MAGE_RUN_TYPE store;
        fastcgi_param  MAGE_IS_DEVELOPER_MODE true;
        
        include        fastcgi_params; ## See /etc/nginx/fastcgi_params

    }

	rewrite ^/minify/([0-9]+)(/.*.(js|css))\$ /lib/minify/m.php?f=\$2&d=\$1 last;
    rewrite ^/skin/m/([0-9]+)(/.*.(js|css))\$ /lib/minify/m.php?f=\$2&d=\$1 last;
}
"

sudo echo "$block" > "/etc/nginx/sites-available/$APP_NAME.conf"
cd /etc/nginx/sites-enabled;
sudo ln -s "/etc/nginx/sites-available/$APP_NAME.conf"
sudo service nginx restart