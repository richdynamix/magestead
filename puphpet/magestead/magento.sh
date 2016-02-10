#!/bin/bash

TYPE=$1;
DIR=$2;
CURRENCY=$3;
DB_HOST=$4;
DB_NAME=$5;
DB_USER=$6;
DB_PASS=$7;
SESSION_SAVE=$8;
BASE_URL=$9;

if [ -d "/.puphpet-stuff${DIR}-${TYPE}-ran" ]; then
    rm -rf "/.puphpet-stuff${DIR}-${TYPE}-ran"
fi

if [ ! -f "/.puphpet-stuff/${DIR}-${TYPE}-ran" ]; then
   sudo touch "/.puphpet-stuff${DIR}-${TYPE}-ran"
   echo "Created file /.puphpet-stuff${DIR}-${TYPE}-ran"
fi

SHA1=$(sha1sum "${DIR}")

if ! grep -x -q "${SHA1}" "/.puphpet-stuff${DIR}-${TYPE}-ran"; then
    sudo /bin/bash -c "echo \"${SHA1}\" >> \"/.puphpet-stuff${DIR}-${TYPE}-ran\""

    
    echo "--- Bootstrapping Magento ---"
	cp -R /vagrant/puphpet/magestead/stubs/magento-composer.json "$DIR/composer.json"
	cd $DIR;
	/usr/local/bin/composer install;

	# echo "--- Installing Magento ---"

	# cd "$DIR/magento";

	# php -f install.php -- \
	# --license_agreement_accepted "yes" \
	# --locale "en_GB" \
	# --timezone "Europe/London" \
	# --default_currency "GBP" \
	# --db_host "localhost" \
	# --db_name "$DB" \
	# --db_user "root" \
	# --db_pass "root" \
	# --session_save "db" \
	# --url "$domain" \
	# --use_rewrites "yes" \
	# --skip_url_validation "yes" \
	# --use_secure "no" \
	# --use_secure_admin "no" \
	# --secure_base_url "$domain" \
	# --admin_firstname "RichDynamix" \
	# --admin_lastname "Magestead" \
	# --admin_email "admin@admin.com" \
	# --admin_username "admin" \
	# --admin_password "password123"

	# echo "Magento admin username = admin";
	# echo "Magento admin password = password123";
	# echo "Magento installed at http://$domain/. Remember and set your hosts file.";

else
    echo "Skipping magento bootstrap for ${DIR} as contents have not changed"
fi