#!/usr/bin/env bash


echo "--- Bootstrapping Magento ---"
cp -R /vagrant/provision/stubs/magento-composer.json /vagrant/composer.json
cd /vagrant;
/usr/local/bin/composer install;


cd /vagrant/public;

/usr/local/bin/modgit init;
/usr/local/bin/modgit add Cm_Cache_Backend_Redis git://github.com/colinmollenhour/Cm_Cache_Backend_Redis.git;