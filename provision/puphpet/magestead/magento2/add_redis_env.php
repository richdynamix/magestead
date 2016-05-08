<?php

$file = "$argv[1]/magento2/app/etc/env.php";
$env  = include $file;

$env['cache'] =  
	array (
		'frontend' => 
		array (
			'default' => 
			array (
				'backend' => 'Cm_Cache_Backend_Redis',
    		'backend_options' => 
    		array (
					'server' => '127.0.0.1',
					'port' => '6379',
					'persistent' => '',
					'database' => '0',
					'force_standalone' => '0',
					'connect_retries' => '1',
					'read_timeout' => '10',
					'automatic_cleaning_factor' => '0',
					'compress_data' => '1',
					'compress_tags' => '1',
					'compress_threshold' => '20480',
					'compression_lib' => 'gzip',
        )
			),
			'page_cache' => 
			array (
	      'backend' => 'Cm_Cache_Backend_Redis',
	      'backend_options' => 
		    array (
					'server' => '127.0.0.1',
					'port' => '6379',
					'persistent' => '',
					'database' => '1',
					'force_standalone' => '0',
					'connect_retries' => '1',
					'read_timeout' => '10',
					'automatic_cleaning_factor' => '0',
					'compress_data' => '0',
					'compress_tags' => '1',
					'compress_threshold' => '20480',
					'compression_lib' => 'gzip',
				),
			),
		)
	);

file_put_contents($file, "<?php \n \n return ".var_export($env,true).";");
