<?php
include_once('interface_router.php');

/**
 * routes configure examples:
 * named routes
 * $map['root'] = array ( 'controller' => 'admin' );
 * $map['login'] = array ( 'controller' => 'admin', 'action' => 'login' );
 * $map['logout'] = array ( 'controller' => 'admin', 'action' => 'logout' );
 *
 * // regular expressions and parameters for requirements routes.
 * $map['/:year/:month/:day'] = array ( 'controller' => 'posts', 'action' => 'find_by_date', 'year' => '/^\d{4}$/', 'month' => '/^\d{0,2}$/', 'day' => '/^\d{0,2}$/' );
 *
 * // you simply append a hash at the end of your mapping to set any default parameters.
 * $map['/users/posts/:action/:id'] = array ( 'controller' => 'posts', 'id' => '/\d{1,}/', 'defaults' => array ( 'page' => 1, 'numbers' => 30 ) );
 *
 * // route for http verb request.
 * $map['/posts/:id'] = array (
 * 	array ( 'controller' => 'posts', 'action' => 'show', 'id' => '/\d{1,}/', 'defaults' => array ( 'page' => 1, 'numbers' => 30 ), 'method' => 'get' ),
 * 	array ( 'controller' => 'posts', 'action' => 'create_comment', 'method' => 'post' ),
 *  array ( 'controller' => 'posts', 'action' => 'update', 'method' => 'put' ),
 * 	array ( 'controller' => 'posts', 'action' => 'destroy', 'method' => 'delete' )
 * );
 *
 * // temporary redirect routes.
 * $map['/test/test/test'] = array ( 'location' => '/500.html' );
 * $map['/test/test/test/test1'] = array ( 'location' => '/404.html' );
 * $map['/login2'] = array ( 'location' => '/admin/login' );
 *
 * $map['/blog/:id'] = array ( 'controller' => 'admin', 'action' => 'test', 'id' => '/\d{1,}/' );
 *
 * // default route.
 * $map['/:controller/:action/:id'] = array ();
 *
 * // globbing route, gracefully handle badly formed requests.
 * $map['/:controller/:action/:id/*others'] = array ();
 *
 * $map['*path'] = array ( 'controller' => 'admin', 'action' => 'test' );
 */
class FireflyRouter implements InterfaceRouter {
	private $configure_file;

	private $map = array();
	private $routes = array();
	private $parsed_path = array();
	private $available_controllers = array();

	public function __construct() {
		$routes_configure_file = FIREFLY_BASE_DIR . DS . 'config' . DS . 'routes.php';
		$this->load($routes_configure_file);
		$this->available_controllers = Router :: available_controllers();
	}

	public function add($path, $route = array()) {
		if(empty($route['controller']) && empty($route['location'])) {
			throw new FireflyException('No controller setting in route config');
		}
		$this->map[$path] = $route;
	}

	public function clear() {
		$this->map = array();
	}

	/**
	 * $this->generate(array('controller' => 'posts','action' => 'threads','year' => '2005','month' => '10'));
	 * Produces: /2005/10/
	 * route options convert to firefly path
	 * $this->generate(array('controller' => 'user','action' => 'list','id' => '12'));
	 * Produces: /user/list/12
	 */
	public function generate($options = array()) {
		$cache_key = md5(serialize($options));
		if(!isset($this->routes[$cache_key])) {
			if(isset($options['use_route'])) {
				$named_route_name = $options['use_route'];
				unset($options['use_route']);
				$options = array_merge($this->map[$named_route_name], $options);
			}
			$options = $this->options_as_params($options);

			if(isset($options['prefix'])) {
				$prefix = '/' . preg_replace('/(^\s*\/?|\/?\s*$)/', '', $options['prefix']);
				unset($options['prefix']);
			} else {
				$prefix = '';
			}
			$routes = $this->routes_by_controller_and_action($options['controller'], $options['action']);
			// use the first matched routes to assemble path.
			$path = $this->assemble_path($options, $routes[0]);
			$this->routes[$cache_key] = $prefix . $this->append_query_string($path, $options);
		}
		return $this->routes[$cache_key];
	}

	/**
	 * Router :: factory()->routes_by_controller("posts")
	 */
	public function routes_by_controller($controller) {
		$matched_routes = array();
		foreach($this->map as $route_key => $options) {
			if(strpos($route_key, ':controller') !== false || (isset($options['controller']) && $options['controller'] == $controller)) {
				$matched_routes[] = $route_key;
			}
		}
		return $matched_routes;
	}

	/**
	 * Router :: factory()->routes_by_controller_and_action("posts", "index")
	 */
	public function routes_by_controller_and_action($controller, $action) {
		$matched_routes = array();
		foreach($this->routes_by_controller($controller) as $route_key) {
			if(strpos($route_key, ':action') !== false || in_array($action, $this->map[$route_key], true)) {
				$matched_routes[] = $route_key;
			}
		}
		return $matched_routes;
	}

	/**
	 * Router :: factory()->routes_for(array ( "controller" => "posts", "action" => "find_by_date", "year" => "2009", "month" => "01", "day" => "18", "page" => 1 ))
	 */
	public function routes_for($options = array()) {
		$options = $this->options_as_params($options);
		return $this->routes_by_controller_and_action($options['controller'], $options['action']);
	}

	public function get_map() {
		return $this->map;
	}

	public function get_routes() {
		return $this->routes;
	}

	public function get_named_routes() {
		$named_routes = array();
		foreach($this->map as $key => $options) {
			if(preg_match('/^\w+$/', $key)) {
				$named_routes[$key] = $options;
			}
		}
		return $named_routes;
	}

	public function recognize_controller($path) {
		$params = $this->recognize_path($path);
		if($params) {
			return $params['controller'];
		} else {
			return false;
		}
	}

	/**
	 * Router :: factory()->recognize_path("/2009/01/18")
	 * => array("controller" => "posts", "action" => "find_by_date", "year" => "2009", "month" => "01", "day" => "18")
	 */
	public function recognize_path($path, $map = array()) {
		$path = Router :: normalize_path($path);
		$map = empty($map) ? $this->map : $map;
		foreach($map as $key => $options) {
			if(preg_match('/^\w+$/', $key)) {
				// named route
				$params = $this->named_route($path, $key, $options);
			} else {
				// normal route, glob route and http verb request route.
				$params = $this->routing($path, $key, $options);
			}

			if($params) {
				// append defaults array parameters to params, and remove 'defaults' from $params.
				$params = array_merge($params, $this->default_params($params));
				unset($params['defaults']);
				return $this->check_params($params);
			}
		}

		return false;
	}

	/**
	 * routes map config.
	 * defaults:	default params append to request params array.
	 * get/post/put/delete:  for http verb request methods(get/post/put/delete).
	 * location:	url for temporary route redirect.
	 * status:		header status for this route.
	 * symbol:		symbols in key can be used in array as key name(such as :controller, :action, :id).
	 */
	private function load($routes_configure_file) {
		$this->configure_file = $routes_configure_file;
		$map = array();
		if(file_exists($this->configure_file)) {
			require($this->configure_file);
			if(empty($map['/:controller/:action/:id'])) {
				// default route, can be overrided in config/routes.php
				// Notice: This route will make all actions in every controller accessible via GET requests.
				$map['/:controller/:action/:id'] = array();
			}
			$this->map = $map;
		} else {
			throw new FireflyException($this->configure_file . ' is not exists!');
		}
	}

	/**
	 * Assemble path using selected route and $options.
	 */
	private function assemble_path(&$options, $route) {
		$path = '';
		$options = $this->options_as_params($options);
		$key_segments = explode('/', $route);
		foreach($key_segments as $key_segment) {
			if(strpos($key_segment, ':') !== false) {
				$symbol = substr($key_segment, 1);
				if(isset($options[$symbol])) {
					$path .= '/' . $options[$symbol];
					unset($options[$symbol]);
				}
			}
			elseif($key_segment) {
				$path .= '/' . $key_segment;
			}
		}
		return $path;
	}

	/**
	 * Generate the query string with any extra keys in the $options and append it to the given path, returning the new path.
	 */
	private function append_query_string($path, $options) {
		foreach(array('controller', 'action', 'id', 'use_route', 'prefix') as $k) {
			if(isset($options[$k])) {
				unset($options[$k]);
			}
		}
		return $path . $this->build_query_string($options);
	}

	// Build a query string from the keys of the given $options.
	private function build_query_string($options) {
		$elem = array();
		foreach($options as $key => $value) {
			$elem[] = $this->to_query($key, $value);
		}
		if(empty($elem)) {
			return '';
		} else {
			return '?' . implode('&', $elem);
		}
	}

	private function to_query($key, $value) {
		return urlencode($key) . '=' . urlencode($value);
	}

	/**
	 * If matched any rule, return $params array()
	 * else return false
	 */
	private function routing($path, $key, $options) {
		$key = Router :: normalize_path($key);
		$pos = strpos($key, ':controller');
		if($pos === false) {
			return $this->normal_routing($path, $key, $options);
		} else {
			return $this->controller_routing($path, $key, $pos, $options);
		}
	}

	/**
	 * Route map key with symbol ":controller" will be parsed by this method, more shorter controller, more higher priovity.
	 * This method also process modular controller, such as "module_name/controller_name".
	 * examples:
	 * app/moudle_controller.php
	 * app/module/test_controller.php
	 * app/module/test/index_controller.php
	 *
	 * Router :: factory()->recognize_path("module/test")
	 * => Array ( [controller] => module [action] => test [id] => "")
	 * Router :: factory()->recognize_path("module/test/index")
	 * => Array ( [controller] => module [action] => test [id] => index )
	 * Router :: factory()->recognize_path("module/test/index/index")
	 * => Array ( [controller] => module/test [action] => index [id] => index )
	 * Router :: factory()->recognize_path("module/test/index/index/1")
	 * => Array ( [controller] => module/test/index [action] => index [id] => 1 )
	 * Router :: factory()->recognize_path("module/test/index/index/index/1")
	 * => Array ( [controller] => module [action] => test [id] => index [others] => index/index/1 )
	 */
	private function controller_routing($path, $key, $pos, $options) {
		$prefix = substr($key, 0, $pos);
		$params = $this->normal_routing($path, $key, $options);
		if($params && in_array($params['controller'], $this->available_controllers, true)) {
			return $params;
		} else {
			// processing modular controller, such as app/controllers/module/test_controller.php
			foreach($this->available_controllers as $controller) {
				if(strpos($controller, DS) !== false && preg_match('/^' . preg_quote($prefix . $controller, '/') . '/i', $path)) {
					// strlen(':controller/') = 12
					$sub_key = substr($key, $pos +12);
					$sub_path = substr($path, strlen($prefix . $controller . '/'));
					$options['controller'] = $controller;
					$params = $this->normal_routing($sub_path, $sub_key, $options);
					if($params === false) {
						continue;
					} else {
						return $params;
					}
				}
			}
			return false;
		}
	}

	private function normal_routing($path, $key, $options) {
		$params = array();
		$key_segments = explode('/', $key);
		$path_segments = explode('/', $path);

		if(count($key_segments) < count($path_segments) && strpos($key, '*') === false) {
			return false;
		}
		foreach($key_segments as $k => $key_segment) {
			$path_segment = isset($path_segments[$k]) ? $path_segments[$k] : null;
			if($path_segment == $key_segment) {
				continue;
			}
			elseif(strpos($key_segment, ':') === 0) {
				$symbol = substr($key_segment, 1);
				$symbol_param = $this->parse_symbol_options($symbol, $options, $path_segment);
				if($symbol_param === false) {
					return false;
				}
				elseif(is_array($symbol_param)) {
					// path matched get/post/put/delete http verb route.
					return $params = array_merge($params, $symbol_param);
				} else {
					// controller and action must start with [a-z_A-Z] start.
					if(preg_match('/^(controller|action)$/i', $symbol) && preg_match('/^[^a-z_A-Z]/', $symbol_param)) {
						return false;
					}
					$params[$symbol] = $symbol_param;
				}
			}
			elseif(strpos($key_segment, '*') === 0) {
				// glob route parsing
				$symbol = substr($key_segment, 1);
				$params[$symbol] = implode('/', array_slice($path_segments, $k));
			} else {
				return false;
			}
		}
		return array_merge($options, $params);
	}

	private function check_params($params) {
		if(empty($params['controller'])) {
			if(isset($params['location'])) {
				// temporary redirect route for test
				header("Location: " . $params['location']);
			} else {
				throw new FireflyException('No controller setting in route config');
			}
		}
		elseif(in_array($params['controller'], $this->available_controllers, true)) {
			return $params;
		} else {
			// non exists controller, skip current route rule.
			return false;
		}
	}

	private function parse_symbol_options($symbol, $options, $path_segment) {
		if(isset($options[$symbol])) {
			return $this->parse_symbol($symbol, $options, $path_segment);
		}
		elseif(isset($options[0])) {
			// get/post/put/delete route for http verb request.
			return $this->http_verb_route($symbol, $options, $path_segment);
		} else {
			// no $options[$symbol] exists, for :controller/:action/:id and other ':' prefix params.
			return $path_segment;
		}
	}

	private function parse_symbol($symbol, $options, $path_segment) {
		$route_value = $options[$symbol];
		if(preg_match('/^\w+$/', $route_value)) {
			// :controller, :action, :id and other non requirements params
			return $route_value;
		}
		elseif($this->match_requirements_regexp($route_value)) {
			// requirements route check
			if(preg_match($route_value, $path_segment)) {
				return $path_segment;
			} else {
				return false;
			}
		} else {
			throw new FireflyException("Routes rule has error nearby '$route_value'");
		}
	}

	private function http_verb_route($symbol, $options, $path_segment) {
		foreach($options as $option) {
			if(isset($option[$symbol])) {
				$option[$symbol] = $this->parse_symbol($symbol, $option, $path_segment);
				if($option[$symbol] === false) {
					return false;
				}
			}
			if(!isset($option['method'])) {
				// GET act as default http verb request
				$option['method'] = 'GET';
			}
			if(strtoupper($_SERVER['REQUEST_METHOD']) == strtoupper($option['method'])) {
				if(empty($option[$symbol])) {
					$option[$symbol] = $path_segment;
				}
				return $option;
			}
		}
	}

	/**
	 * $map['logout'] = array('controller' => 'admin', 'action' => 'logout');
	 */
	private function named_route($path, $key, $options) {
		$key = Router :: normalize_path($key);
		if($path == '/') {
			// if(file_exists(APP_ROOT . DS . 'index.html')) { require(APP_ROOT . DS . 'index.html'); exit(); }
			// elseif(file_exists(APP_ROOT . DS . 'index.php')) { require(APP_ROOT . DS . 'index.php'); exit(); }
			if($key == 'root') {
				// for root path.
				// RewriteCond %{REQUEST_FILENAME}index.html !-f
				// RewriteCond %{REQUEST_FILENAME}index.php !-f
				// RewriteRule ^$ dispatch.php?fireflypath=/ [QSA,L]
				return $this->check_params($options);
			}
		}
		if($path == $key) {
			return $this->check_params($options);
		} else {
			return false;
		}
	}

	private function default_params($options) {
		if(empty($options['defaults'])) {
			return array();
		}
		elseif(is_array($options['defaults'])) {
			$defaults = $options['defaults'];
			$request = array_merge($_POST, $_GET);
			// router defaults params should be overrided by $_REQUEST params.
			foreach($defaults as $key => $value) {
				if(isset($request[$key])) {
					$defaults[$key] = $request[$key];
				}
			}
			return $defaults;
		} else {
			throw new FireflyException('key "defaults" must be Array in routes map.');
		}
	}

	/**
	 * Check route value whether match requirements regualr expression.
	 */
	private function match_requirements_regexp($route_value) {
		$regexp = '/^\/.+\/[imsxe]*$/';
		return preg_match($regexp, $route_value);
	}

	/**
	 * Check controller name and action name in $options.
	 * If no controller supplied, throw exception.
	 * If no action supplied, set default action name "index" to $options.
	 */
	private function options_as_params($options, $only_current_app = false) {
		if(isset($options['controller'])) {
			if($only_current_app && !in_array($options['controller'], $this->available_controllers, true)) {
				throw new FireflyException('Controller name in options is not avaliable!');
			}
			if(empty($options['action'])) {
				$options['action'] = 'index';
			}
		} else {
			throw new FireflyException('Need controller and action in options!');
		}
		return $options;
	}
}
?>
