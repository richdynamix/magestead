#!/bin/bash

DIR=${1};

cd $DIR/magento;

echo "--- Reindexing Tables ---"
../bin/n98-magerun.phar index:reindex:all;

echo "--- Enable All Cache ---"
../bin/n98-magerun.phar cache:enable;

echo "--- Flushing All Cache ---"
../bin/n98-magerun.phar cache:flush;