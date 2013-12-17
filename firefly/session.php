<?php
class Session {

	private static $started = false;

	public static function start() {
		if(!self::$started) {
			self::$started = true;
			defined('SESSION_STORE_STRATEGY') ? null : define('SESSION_STORE_STRATEGY', 'file');
			if(SESSION_STORE_STRATEGY) {
				$type = strtolower(SESSION_STORE_STRATEGY);
				if(file_exists(FIREFLY_LIB_DIR . DS . 'sessions' . DS . $type . '_session.php')) {
					include_once('sessions' . DS . $type . '_session.php');
					$classname = $type . 'session';
					new $classname;
				}
				elseif(file_exists(FIREFLY_PLUGINS_DIR . DS . $type . '_session.php')) {
					include_once(FIREFLY_PLUGINS_DIR . DS . $type . '_session.php');
					$classname = $type . 'session';
					new $classname;
				} else {
					throw new FireflyException('Can not find session store strategy: ' . $type);
				}
			} else {
				// SESSION_STORE_STRATEGY set to null or 'default', using php default session.
				session_start();
			}
		}
	}

}
?>
