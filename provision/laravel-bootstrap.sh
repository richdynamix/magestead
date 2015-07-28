#!/usr/bin/env bash

echo "--- Bootstrapping Laravel ---"

cd /vagrant;
/usr/local/bin/composer create-project laravel/laravel --prefer-dist laravel

rm -fr public;
ln -sfn /vagrant/laravel/public public

echo "--- Setting Laravel Folder Permissions ---"
cd laravel;
chmod -R 777 storage/;
chmod -R 777 bootstrap/cache/;