#!/bin/bash

DIR=${1};

cd $DIR/magento2;

echo "--- Reindexing Tables ---"
bin/magento indexer:reindex;

echo "--- Flushing All Cache ---"
bin/magento cache:flush;