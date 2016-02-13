#!/usr/bin/env bash

DB=$1;

mysql -uroot -pvagrant -e "CREATE DATABASE IF NOT EXISTS \`$DB\` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci";