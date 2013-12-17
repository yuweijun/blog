<?php
class Profiler {

	private static $profiles = array ();

	private function __construct() {
	}
	
	private static function get_unique_key($key) {
		return md5($key);
	}

	public static function start($key = 'default') {
		$unique_key = self::get_unique_key($key);
		self::$profiles[$unique_key] = array ( 'start' => microtime(true) );
		return self::$profiles[$unique_key];
	}

	public static function end($key = 'default') {
		$unique_key = self::get_unique_key($key);
		if (!isset (self::$profiles[$unique_key]['start'])) {
			self::start($unique_key);
		}
		$start = self::$profiles[$unique_key]['start'];
		$end = microtime(true);
		// multiple 1000 to convert micro seconds to milli seconds.
		$costs = round(($end - $start) * 1000, 2);
		$msg = "($costs ms) $key";
		Logger::info($msg);
		
		self::$profiles[$unique_key]['end'] = $end;
		return self::$profiles[$unique_key];
	}

	public static function get($key = 'default') {
		$unique_key = self::get_unique_key($key);
		if (!isset (self::$profiles[$unique_key])) {
			$time = microtime(true);
			self::$profiles[$unique_key] = array('start' => $time, 'end' => $time);
		}
		return self::$profiles[$unique_key];
	}

}
?>
