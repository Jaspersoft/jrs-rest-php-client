<?php
define("JASPERCLIENT_ROOT", __DIR__);

spl_autoload_register(function($class) {
	$location = JASPERCLIENT_ROOT . '/' . $class . '.class.php';
	
	if(!is_readable($location)) return;
	
	require_once $location;
});

?>