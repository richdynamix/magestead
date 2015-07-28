#!/usr/bin/env bash


echo "--- Bootstrapping Laravel ---"
cp -R /vagrant/provision/stubs/laravel-composer.json /vagrant/composer.json
cd /vagrant;
/usr/local/bin/composer install;


echo "--- Setting Laravel Folder Permissions ---"
chmod -R 777 storage/;
chmod -R 777 bootstrap/cache/;