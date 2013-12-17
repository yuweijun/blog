<?php
include_once('cache/cache.php');

/**
 * The plugin API is located in this file, which allows for creating actions
 * and filters and hooking functions, and methods. The functions or methods will
 * then be run when the action or filter is called.
 *
 * The API callback examples reference functions, but can be methods of classes.
 * To hook methods, you'll need to pass an array one of two ways.
 *
 * Any of the syntaxes explained in the PHP documentation for the
 * {@link http://us2.php.net/manual/en/language.pseudo-types.php#language.types.callback 'callback'}
 * type are valid.
 */
class Plugin {

	private static $instance = null;

	private $filter_id_count = 0;

	private $listeners = array();
	
	private function __construct() {
	}

	public static function get_reference() {
		if (self::$instance == null) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}
	
	/**
	 * Example of plugin configure:
	 * $plugin = array ( 'name' => 'comments_observer', 'class_name' => 'CommentsObserver', 'directory' => '' )
	 * @parameters is mixed type and will pass to plugin constructor, which can be array if plugin has many constructor parameters.
	 */
	public function add_plugin($plugin, $parameters = '') {
		if (is_array($plugin)) {
			$name = $plugin['name'];
			$class_name = isset ($plugin['class_name']) ? $plugin['class_name'] : $name;
			$directory = isset ($plugin['directory']) ? $plugin['directory'] . DS : '';
			$file_name = FIREFLY_PLUGINS_DIR . DS . $directory . $name . '.php';
			$this->new_plugin_object($class_name, $file_name, $parameters);
		} elseif (is_string($plugin)) {
			if (class_exists($plugin)) {
				new $plugin($parameters);
			} else {
				$file_name = FIREFLY_PLUGINS_DIR . DS . $plugin . '.php';
				$class_name = preg_replace("/_/", "", $plugin);
				$this->new_plugin_object($class_name, $file_name, $parameters);
			}
		}
	}
	
	/**
	 * Hooks a function on to a specific action.
	 *
	 * Based on Wordpress's function 'add_action'.
	 */
	public function add_action($hook, $function) {
		$this->register($hook, $function);
	}
	
	/**
	 * Execute functions hooked on a specific action hook.
	 */
	public function do_action($hook, $args = array()) {
		$this->trigger($hook, $args);
	}
		
	/**
	 * Call the functions added to filter value and return filtered value.
	 */
	public function do_filter($hook, $value, $parameters = '') {
		if (isset ($this->listeners[$hook]) && is_array($this->listeners[$hook])) {
			foreach ($this->listeners[$hook] as $functions) {
				$value = $this->call_filter_functions($functions, $value, $parameters);
			}
		}
		return $value;
	}

	/**
	 * This function similar with Event.addEventListener() of javascript,
	 * and $hook similar with event type such as 'click' or 'submit'.
	 */
	public function register($hook, $function) {
		if (!array_key_exists($hook, $this->listeners)) {
			$this->listeners[$hook] = array ();
		}
		$key = $this->get_unique_filter_id($hook, $function);
		if (array_key_exists($key, $this->listeners[$hook])) {
			array_push($this->listeners[$hook][$key], $function);
		} else {
			$this->listeners[$hook][$key] = array ( $function );
		}
	}

	/**
	 * This function will not remove functions which are triggered.
	 * We can trigger it again later like javascript event.
	 */
	public function trigger($hook, $parameters = array ()) {
		if (!is_array($parameters)) {
			$parameters = array($parameters);
		}
		if (isset ($this->listeners[$hook]) && is_array($this->listeners[$hook])) {
			foreach ($this->listeners[$hook] as $functions) {
				$this->call_hook_functions($functions, $parameters);
			}
		}
	}

	/**
	 * If support zero parameter, will reset $listeners.
	 * else if only support one parameter $hook, will remove all of the hooks from an action.
	 */
	public function remove_action($hook = '', $function = '') {
		$num = func_num_args();
		if ($num === 0) {
			$this->listeners = array();
		} elseif ($num === 1) {
			$this->listeners[$hook] = array();
		} else {
			$key = $this->get_unique_filter_id($hook, $function);
			$this->listeners[$hook][$key] = array();
		}
	}

	/**
	 * Check if any action has been registered for a hook.
	 */
	public function has_action($hook, $function = '') {
		if (!array_key_exists($hook, $this->listeners)) {
			return false;
		}
		$key = $this->get_unique_filter_id($hook, $function);
		if (array_key_exists($key, $this->listeners[$hook])) {
			return true;
		} else {
			return false;
		}
	}
	
	private function new_plugin_object($class_name, $file_name, $parameters) {
		if (file_exists($file_name)) {
			include_once ($file_name);
			if (class_exists($class_name)) {
				new $class_name($parameters);
			}
		} else {
			new $class_name;
		}
	}
	
	private function call_filter_functions($functions, $value, $parameters) {
		foreach ($functions as $function) {
			if (is_string($function) && function_exists($function)) {
				$value = call_user_func_array($function, array($value, $parameters));
			}
			elseif (is_array($function) && is_object($function[0]) && method_exists($function[0], $function[1])) {
				$value = call_user_func_array($function, array($value, $parameters));
			} elseif (is_string($function[0])) {
				$value = call_user_func_array($function, array($value, $parameters));
			}
		}
		return $value;
	}

	private function call_hook_functions($functions, $parameters) {
		foreach ($functions as $function) {
			if (is_string($function) && function_exists($function)) {
				call_user_func_array($function, $parameters);
			} elseif (is_array($function) && is_object($function[0]) && method_exists($function[0], $function[1])) {
				call_user_func_array($function, $parameters);
			} elseif (is_string($function[0])) {
				call_user_func_array($function, $parameters);
			}
		}
	}

	/**
	 * Build Unique ID for storage and retrieval.
	 * This method is based on wordpress function '_wp_filter_build_unique_id'.
	 */
	private function get_unique_filter_id($hook, $function) {
		// If function then just skip all of the tests and not overwrite the following.
		if (is_string($function)) {
			return $function;
		} else {
			// Object Class Calling
			if (is_object($function[0])) {
				$obj_idx = strtolower(get_class($function[0]) . '.' . $function[1]);
				if (!isset ($function[0]->unique_filter_id)) {
					$function[0]->unique_filter_id = $this->filter_id_count++;
				} else {
					$obj_idx .= $function[0]->unique_filter_id;
				}
				return $obj_idx;
			} elseif (is_string($function[0])) {
				// Static Calling
				return strtolower($function[0] . '.' . $function[1]);
			}
		}
	}

}

// facility plugin functions
function add_plugin($plugin_name, $parameters = '') {
	Plugin::get_reference()->add_plugin($plugin_name, $parameters);
}

function add_action($hook, $function) {
	Plugin::get_reference()->add_action($hook, $function);
}

function remove_action($hook = '', $function = '') {
	Plugin::get_reference()->remove_action($hook, $function);
}

function do_action($hook, $args = '') {
	Plugin::get_reference()->do_action($hook, $args);
}

function do_filter($hook, $value, $parameters = '') {
	return Plugin::get_reference()->do_filter($hook, $value, $parameters);
}

?>
