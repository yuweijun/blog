<?php
$config = array (
	'development' => array (
		'adapter' => 'mysql',
		'encoding' => 'utf8',
		'database' => 'firefly_development',
		'username' => 'firefly',
		'password' => 'phpfirefly',
		'host' => 'localhost:/tmp/mysql.sock'
	),
	'test' => array (
		'adapter' => 'mysql',
		'encoding' => 'utf8',
		'database' => 'firefly_development',
		'username' => 'firefly',
		'password' => 'phpfirefly',
		'host' => 'localhost'
	),
	'production' => array (
		'adapter' => 'mysql',
		'encoding' => 'utf8',
		'database' => 'firefly_development',
		'username' => 'firefly',
		'password' => 'phpfirefly',
		'host' => 'localhost'
	)
);

$slaves = array (
	array (
		'adapter' => 'mysql',
		'encoding' => 'utf8',
		'database' => 'firefly_development',
		'username' => 'firefly',
		'password' => 'phpfirefly',
		'host' => 'localhost:/tmp/mysql.sock'
	),
	array (
		'adapter' => 'mysql',
		'encoding' => 'utf8',
		'database' => 'firefly_development',
		'username' => 'firefly',
		'password' => 'phpfirefly',
		'host' => 'localhost:/tmp/mysql.sock'
	)
);
?>
