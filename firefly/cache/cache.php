<?php
/**
 * cache class just a plugin of framework, and it is only availabe for http GET method.
 * If it is an action cache of controller, it use hook 'render.before_filter', before filters of controller will always be executed.
 * else it is a page cache, will return cached page directly using hook 'dispatch.start', without execute before filters of controllers.
 */
class Cache {

	public function __construct() {
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			Plugin::get_reference()->add_action('dispatch.start', array($this, 'page_cache_exists'));
			Plugin::get_reference()->add_action('render.before_filter', array($this, 'action_cache_exists'));
			Plugin::get_reference()->add_action('render.after_filter', array($this, 'cache_create'));
		}
	}

	/**
	 * check cache of page if exists.
	 */
	public function page_cache_exists($request) {
		$file = $this->get_cache_file($request->parameters(), 'page');
		if(file_exists($file)) {
			require $file;
			Logger::debug('use cached page: ' . $file);
			Logger::output();
			exit ;
		}
	}

	/**
	 * Check if exists cache for current action with http GET request.
	 */
	public function action_cache_exists($controller) {
		$file = $this->get_cache_file($controller->params, 'action');
		if(file_exists($file)) {
			require $file;
			Logger::debug('use cached action: ' . $file);
			Logger::output();
			exit ;
		}
	}

	public function cache_create($controller, $response_body, $cache) {
		$params = $controller->params;
		$do_page_cache = $this->cache_validate($params, $cache, 'page');
		// do page cache firstly.
		if($do_page_cache) {
			$this->create_page_cache($params, $response_body);
		} else {
			// do page cache or action cache, select one from in these stragtegies.
			$do_action_cache = $this->cache_validate($params, $cache, 'action');
			if($do_action_cache) {
				$this->create_action_cache($params, $response_body);
			}
		}
	}

	/**
	 * This is static method, should trigger by other hooks, such as active record hook, after_save.
	 * Which should create observer of active record manually.
	 * if not set $controller, will remove all caches.
	 */
	public static function remove($controllers = '') {
		$cache_dir = FIREFLY_CACHE_DIR;
		if (empty($controllers)) {
			self::clear_directory($cache_dir);
		} else {
			if (is_string($controllers)) {
				$list = preg_split('/\s*,\s*/', $controllers);
			}  else {
				$list = $controllers;
			}
			
			foreach ($list as $controller) {
				$cache_dir .= DS . $controller;
				self::clear_directory($cache_dir);
				Logger::debug("Remove caches from: $cache_dir");
			}
		}
	}

	public function get_http_response_body($path) {
		ob_start();
		$_GET['fireflypath'] = $path;
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$dispatcher = new Dispatcher;
		$dispatcher->dispatch();
		$html = ob_get_clean();
		return $html;
	}

	private function cache_validate($params, $cache, $type) {
		$cache_settings = $this->get_cache_settings($cache, $type);
		if(in_array($params['action'], $cache_settings)) {
			return true;
		} else {
			return false;
		}
	}

	private function get_cache_settings($cache, $type) {
		if(isset($cache[$type])) {
			if(is_string($cache[$type])) {
				return preg_split('/\s*,\s*/', $cache[$type]);
			} elseif(is_array($cache[$type])) {
				return $cache[$type];
			}
		}
		return array();
	}

	private function create_page_cache($params, $response_body) {
		$file = $this->get_cache_file($params, 'page');
		file_put_contents($file, $response_body, LOCK_EX);
		
		Logger::debug('create cache page: ' . $file);
	}

	private function create_action_cache($params, $response_body) {
		$file = $this->get_cache_file($params, 'action');
		file_put_contents($file, $response_body, LOCK_EX);
		
		Logger::debug('create cache action: ' . $file);
	}
	
	private function get_cache_file($params, $type) {
		$cache_dir = $this->check_cache_dir($params);
		$filename = $this->get_cache_key($type);
		return $cache_dir . DS . $filename . '.html';
	}

	private function check_cache_dir($params) {
		$dirname = $params['controller'] . DS . $params['action'];
		$cache_dir = FIREFLY_CACHE_DIR . DS . $dirname;
		if (is_writable(FIREFLY_CACHE_DIR)) {
			if (!file_exists($cache_dir)) {
				umask(0000);
				mkdir($cache_dir, 0777, true);
			}
		} else {
			echo 'The directory "' . FIREFLY_CACHE_DIR . '" is not writable!';
			exit;
		}
		return $cache_dir;
	}
	
	/**
	 * not cache post request, so only get cache according by URL and I18n.locale
	 */
	private function get_cache_key($type) {
		$key = I18n::get_locale();
		$key .= $type . $_SERVER['REQUEST_URI'];
		return md5($key);
	}
	
	/**
	 * don't rmdir directory, just only delete files under directory.
	 * so not need to create directory for next cache request.
	 */
	private static function clear_directory($dir) {
		if(file_exists($dir) && $handle = opendir($dir)) {
			while(false !== ($item = readdir($handle))) {
				if($item != "." && $item != "..") {
					$filename = $dir . DS . $item;
					if(is_dir($filename)) {
						self::clear_directory($filename);
					} else {
						if (is_writable($filename)) {
							unlink($filename);
						}
					}
				}
			}
			closedir($handle);
			// rmdir($dir);
		}
	}

	private function create_action_php_file($path) {
		$params = Router::recognize($path);
		$php_file_dir = APP_ROOT . DS . $params['controller'];
		if(!file_exists($php_file_dir)) {
			umask(0000);
			mkdir($php_file_dir, 0777, true);
		}
		
		if(empty($params['action'])) {
			$params['action'] = 'index';
		}
		$file_name = $php_file_dir . DS . $params['action'] . '.php';
		if(!file_exists($file_name)) {
			$content = $this->get_php_file_content();
			file_put_contents($file_name, $content);
		}
	}

	private function get_php_file_content() {
		$content = "<" . "?" . "php\n";
		$content .= "\$dir = str_repeat('..' . DIRECTORY_SEPARATOR, substr_count(\$_SERVER['SCRIPT_NAME'], '/') - 1);\n";
		$content .= "\$dispatch_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . \$dir . 'dispatch.php';\n";
		$content .= "include_once(\$dispatch_file);\n";
		$content .= "?" . ">\n";
		return $content;
	}

}
?>
