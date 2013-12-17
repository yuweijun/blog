<?php
class Layout {
	private static $instance = null;
	private $layout;

	private function __construct() {
	}

	public static function get_action_layout($params, $layout) {
		$layout = is_string($layout) ? $layout : $params['controller'];
		if(file_exists(self::layout_location($layout))) {
			return $layout;
		} else {
			$file = self::layout_location('application');
			if(file_exists($file)) {
				return 'application';
			}
		}
		return null;
	}

	public static function get_layout($layout, $options) {
		if(!self::$instance) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance->pick_layout($layout, $options);
	}

	private static function layout_location($layout) {
		// $layout may be file name.
		if(file_exists($layout) && is_file($layout)) {
			return $layout;
		}
		return FIREFLY_APP_DIR . DS . 'views' . DS . 'layouts' . DS . $layout . '.php';
	}

	private function pick_layout($layout, $options) {
		$this->layout = $layout;
		if(isset($options['layout'])) {
			return $this->active_layout($options['layout']);
		}
		elseif(isset($options['text']) || isset($options['partial']) || isset($options['update']) || isset($options['json']) || isset($options['nothing']) || isset($options['xml']) || isset($options['js'])) {
			return $this->active_layout(false);
		} else {
			return $this->active_layout($this->layout, true);
		}
	}

	/**
	 * layout => false, no layout.
	 * layout => $options['layout'].
	 * layout => $controller->layout.
	 * layout => $controller_name
	 * layout => application.php
	 *
	 * special: render text, using default layout => false.
	 */
	private function active_layout($layout, $using_default_layout = false) {
		if($layout === true) {
			return $this->find_layout($this->layout, true);
		}
		elseif($layout) {
			return $this->find_layout($layout, $using_default_layout);
		} else {
			return null;
		}
	}

	/**
	 * If can not find specific layout, it will trigger a layout missing exception.
	 */
	private function find_layout($layout, $using_default_layout) {
		$file = self::layout_location($layout);
		if(!file_exists($file)) {
			if($using_default_layout) {
				$file = self::layout_location($this->layout);
				if(!file_exists($file)) {
					$file = self::layout_location('application');
					if(!file_exists($file)) {
						$file = null;
					}
				}
			} else {
				throw new FireflyException('Specific layout ' . $layout . ' is not exists!');
			}
		}
		return $file;
	}
}
?>
