<?php
define('DS', DIRECTORY_SEPARATOR);
define('FIREFLY_BASE_DIR', str_replace(DS . 'config' . DS . 'config.php', '', __FILE__));
define('FIREFLY_LIB_DIR', FIREFLY_BASE_DIR . DS . 'firefly');
define('FIREFLY_APP_DIR', FIREFLY_BASE_DIR . DS . 'app');
define('FIREFLY_TMP_DIR', FIREFLY_BASE_DIR . DS . 'tmp');
define('FIREFLY_CACHE_DIR', FIREFLY_TMP_DIR . DS . 'cache');
define('FIREFLY_PLUGINS_DIR', FIREFLY_BASE_DIR . DS . 'plugins');
define('APP_ROOT', FIREFLY_BASE_DIR . DS . 'public');
define('APP_LIB_DIR', FIREFLY_BASE_DIR . DS . 'lib');
define('APP_THEMES_DIR', APP_ROOT . DS . 'themes');
?>
