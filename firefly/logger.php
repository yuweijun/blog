<?php
defined('LOG_COLORING') ? null : define('LOG_COLORING', 1); // 0. log disable coloring, 1. log enable coloring.
defined('DEBUG_LEVEL') ? null : define('DEBUG_LEVEL', 'debug'); // debug, info, warn, error, null.
defined('ENVIRONMENT') ? null : define('ENVIRONMENT', 'development'); // development, production.

class Logger {
	private static $odd = true;
	private static $logs = array ();
	private function __construct() {
	}

	// private static $instance = null;
	// public static function get_reference() {
		// if (self::$instance == null) {
			// $class_name = __CLASS__;
			// self::$instance = new $class_name;
		// }
		// return self::$instance;
	// }

	/**
	 * 0. production, without debug info.
	 * 1. inspect controller and view object and sql statements.
	 */
	public static function debug($object) {
		// debug method will output function caller position, and file name of caller.
		self::log('debug', $object, "cyan");
	}

	public static function warn($msg) {
		self::log('warn', $msg, "light_red");
	}

	public static function info($info) {
		self::log('info', $info, "green");
	}

	public static function error($err) {
		self::log('error', $err, "red");
	}

	// 0. log file, 1. append to page footer
	public static function output() {
		if (DEBUG_LEVEL) {
			$out = "\n\n";
			foreach (self::$logs as $log) {
				switch (DEBUG_LEVEL) {
					case 'error' :
						$out .= self::output_error($log);
						break;
					case 'warn' :
						$out .= self::output_warn($log);
						break;
					case 'debug' :
						$out .= self::output_debug($log);
						break;
					default :
						$out .= self::output_info($log);
				}
			}
			// write to log/ENVIRONMENT.log file
			$filename = FIREFLY_BASE_DIR . DS . 'log' . DS . ENVIRONMENT . '.log';
			if (is_writable($filename)) {
				file_put_contents($filename, $out, FILE_APPEND | LOCK_EX);
			} else {
				throw new FireflyException("The file $filename is not writable!");
			}
		}
		self::$logs = array();
	}
	
	public static function log($level, $msg, $color = 'normal') {
		if (DEBUG_LEVEL) {
			$out = "[" . strtoupper($level) . " - " . date('Y-m-d H:i:s') . "] ";
			$out .= is_string($msg) ? $msg : var_export($msg, true);
			if(self::$odd) {
				self::$odd = false;
				$out = self::coloring($out, 'bold');
			} else {
				self::$odd = true;
				$out = self::coloring($out, $color);
			}
			$out .= "\n";
			self::$logs[] = array ( $level => $out );
		}
	}

	private static function coloring($text, $color = 'normal') {
		if (!LOG_COLORING) {
			return $text;
		}

		$colors = array ( 'light_red' => '[1;31m', 'light_green' => '[1;32m', 'yellow' => '[1;33m',
			'light_blue' => '[1;34m', 'magenta' => '[1;35m', 'light_cyan' => '[1;36m', 'white' => '[1;37m',
			'normal' => '[0m', 'black' => '[0;30m', 'red' => '[0;31m', 'green' => '[0;32m', 'brown' => '[0;33m',
			'blue' => '[0;34m', 'cyan' => '[0;36m', 'bold' => '[1m', 'underscore' => '[4m', 'reverse' => '[7m' );
		return "\033" . (isset($colors[$color]) ? $colors[$color] : '[0m') . $text . "\033[0m";
	}

	private static function output_error($log) {
		foreach ($log as $k => $v) {
			if ('error' ===  $k) {
				return $log['error'];
			}
		}
	}

	private static function output_warn($log) {
		foreach ($log as $k => $v) {
			if (in_array($k, array ( 'error', 'warn' ))) {
				return $v;
			}
		}
	}

	private static function output_info($log) {
		foreach ($log as $k => $v) {
			if (in_array($k, array ( 'error', 'warn', 'info' ))) {
				return $v;
			}
		}
	}

	private static function output_debug($log) {
		foreach ($log as $k => $v) {
			return $v;
		}
	}
}
?>
