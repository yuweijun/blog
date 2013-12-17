<?php
defined('DEBUG_LEVEL') ? null : define('DEBUG_LEVEL', 'debug'); // debug, info, warn, error, null.
defined('FLASH_PAGE') ? null : define('FLASH_PAGE', 1); // redirect flash message.

ini_get('display_errors') ? null : ini_set('display_errors', 1);

/**
 * Turn register globals off.
 */
function unregister_GLOBALS() {
	if ( !ini_get('register_globals') )
		return;

	if ( isset($_REQUEST['GLOBALS']) )
		die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ( $input as $k => $v )
		if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
			$GLOBALS[$k] = NULL;
			unset($GLOBALS[$k]);
		}
}

unregister_GLOBALS();
set_magic_quotes_runtime(0);
ini_set('magic_quotes_sybase', 0);
?>
