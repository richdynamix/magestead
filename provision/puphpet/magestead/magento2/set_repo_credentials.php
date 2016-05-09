<?php

$file = "/home/vagrant/.composer/auth.json";

$publicKey  = "$argv[1]";
$privateKey = "$argv[2]";

$contents = [
	"http-basic" => [
		"repo.magento.com" => [
			"username" => $publicKey,
			"password" => $privateKey,
		]
	]
];


file_put_contents($file, json_encode($contents));
