#!/usr/bin/env bash

DB=$1;

mysql -uroot -proot -e "CREATE DATABASE \`$DB\` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci";
