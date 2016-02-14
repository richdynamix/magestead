#!/bin/bash

DIR=${1};

LOCAL_XML="$DIR/magento/app/etc/local.xml"
MODULE_XML="$DIR/magento/app/etc/modules/Cm_RedisSession.xml"

sudo chmod 755 $MODULE_XML

config=$(<$LOCAL_XML)

redis="<redis_session>\n
		      <host>127.0.0.1</host>\n
		      <port>6379</port>\n
		      <password></password>\n
		      <timeout>2.5</timeout>\n
		      <persistent></persistent>\n
		      <db>0</db>\n
		      <compression_threshold>2048</compression_threshold>\n
		      <compression_lib>gzip</compression_lib>\n
		      <log_level>1</log_level>\n
		      <max_concurrency>6</max_concurrency>\n
		      <break_after_frontend>5</break_after_frontend>\n
		      <break_after_adminhtml>30</break_after_adminhtml>\n
		      <first_lifetime>600</first_lifetime>\n
		      <bot_first_lifetime>60</bot_first_lifetime>\n
		      <bot_lifetime>7200</bot_lifetime>\n
		      <disable_locking>0</disable_locking>\n
		      <min_lifetime>60</min_lifetime>\n
		      <max_lifetime>2592000</max_lifetime>\n
		</redis_session>"

C=$(echo $redis | sed 's/\//\\\//g')
newconfig=$(sed "/<\/global>/ s/.*/${C}\n&/" "$LOCAL_XML")

sudo echo "$newconfig" > "$LOCAL_XML"

value="true"
sudo sed -i "s|\(<active>\)[^<>]*\(</active>\)|\1${value}\2|" $MODULE_XML

sudo chmod 644 $MODULE_XML
