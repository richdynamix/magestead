#!/bin/bash

DIR=${1}

cd "$DIR/bin";

sudo wget https://files.magerun.net/n98-magerun.phar;

sudo chmod +x ./n98-magerun.phar;