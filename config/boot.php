<?php
/**
 * boot.php
 *
 * load application constants from config.php
 * using function __autoload to detect classes.
 */
include_once('config.php');
include_once('theme.php');
include_once('plugins.php');
include_once('environment.php');

@include_once("environments" . DS . ENVIRONMENT . ".php");
@include_once(APP_THEMES_DIR . DS . THEME . DS . 'functions.php');

include_once(dirname(__FILE__) . DS . '..' . DS . 'firefly' . DS . 'dispatcher.php');

function __autoload($class_name) {
	$filename = strtolower($class_name) . '.php';

	if(file_exists(FIREFLY_APP_DIR . DS . 'models' . DS . $filename)) {
		// include app models before session start.
		include_once(FIREFLY_APP_DIR . DS . 'models' . DS . $filename);
	} elseif(file_exists(APP_LIB_DIR . DS . $filename)) {
		// include app libs add by developers.
		include_once(APP_LIB_DIR . DS . $filename);
	} elseif(file_exists(FIREFLY_PLUGINS_DIR . DS . $filename)) {
		// include plugins add by others.
		include_once(FIREFLY_PLUGINS_DIR . DS . $filename);
	} else {
		// processing file_name with underscore.
		$filename = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '\1_\2', $class_name)) . '.php';
		if(file_exists(FIREFLY_APP_DIR . DS . 'controllers' . DS . $filename)) {
			include_once(FIREFLY_APP_DIR . DS . 'controllers' . DS . $filename);
        } elseif(file_exists(FIREFLY_APP_DIR . DS . 'models' . DS . $filename)) {
            // include app models before session start.
            include_once(FIREFLY_APP_DIR . DS . 'models' . DS . $filename);
		} else {
			if(file_exists(APP_LIB_DIR . DS . $filename)) {
				include_once(APP_LIB_DIR . DS . $filename);
			}
		}
	}
}

/**
 * http://php.net/manual/en/function.spl-autoload-register.php
 * PHPUnit now uses an autoloader to load its classes. 
 * If the tested code requires an autoloader, use spl_autoload_register() to register it.
 * The only change required is to add spl_autoload_register('__autoload') in your bootstrap script.
 */
spl_autoload_register('__autoload');

?>
