#!/usr/bin/env bash


echo "--- Bootstrapping Symfony 2.0 ---"
cd /vagrant; 
/usr/local/bin/composer create-project symfony/framework-standard-edition public;
