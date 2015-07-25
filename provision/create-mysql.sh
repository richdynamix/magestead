#!/usr/bin/env bash

DB=$1;

mysql -uroot -pvagrant -e "DROP DATABASE IF EXISTS \`$DB\`";
mysql -uroot -pvagrant -e "CREATE DATABASE \`$DB\` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci";
