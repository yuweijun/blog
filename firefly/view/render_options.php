<?php
class RenderOptions {
	private $request;
	private $response;
	private $controller;
	private $default_template;
	private $template_root;
	private $layout;
	private $extension = '.php';
	private $options = array();

	public function __construct($request, $response, $controller) {
		$this->request = $request;
		$this->response = $response;
		$this->controller = $controller;
		$this->layout = $this->controller->get_layout();
		$this->template_root = FIREFLY_APP_DIR . DS . 'views' . DS;
		$this->default_template = FIREFLY_APP_DIR . DS . 'views' . DS . $this->controller->params['controller'] . DS . $this->controller->params['action'];
	}

	public function parse($options) {
		$this->check_render_format($options);
		$this->options = $this->parse_shortcut_options($options);
		if(isset($this->options['text'])) {
			$this->render_for_text($this->options['text']);
		}
		elseif(isset($this->options['file'])) {
			$this->render_for_file($this->options['file']);
		}
		elseif(isset($this->options['template'])) {
			$this->render_for_file($this->find_template($this->options['template']));
		}
		elseif(isset($this->options['action'])) {
			$this->render_for_file($this->find_template($this->options['action']));
		}
		elseif(isset($this->options['xml'])) {
			$this->response->set_content_type_by_extension('xml');
			$this->render_for_text($this->options['xml']);
		}
		elseif(isset($this->options['js'])) {
			$this->response->set_content_type_by_extension('js');
			$this->render_for_text($this->options['js']);
		}
		elseif(isset($this->options['json'])) {
			if(isset($this->options['callback'])) {
				$this->response->set_content_type_by_extension('js');
				$this->options['json'] = $this->options['callback'] . "({$this->options['json']});";
			} else {
				$this->response->set_content_type_by_extension('json');
			}
			$this->render_for_text($this->options['json']);
		}
		elseif(isset($this->options['partial'])) {
			$this->render_for_file($this->find_template($this->options['partial'], true));
		}
		elseif(isset($this->options['nothing'])) {
			if($this->options['nothing']) {
				$this->render_for_text('');
			} else {
				$this->render_for_file($this->get_filename_by_extension($this->default_template));
			}
		} else {
			$this->render_for_file($this->get_filename_by_extension($this->default_template));
		}

		$this->options['layout'] = Layout :: get_layout($this->layout, $this->options);
		return $this->options;
	}

	private function render_for_file($file) {
		if(file_exists($file) && preg_match('/^' . preg_quote(FIREFLY_BASE_DIR, '/') . '/', $file)) {
			$this->options['template'] = $file;
		} else {
			throw new FireflyException('File: ' . $file . ' is not exists or not under fold ' . FIREFLY_BASE_DIR);
		}
	}

	private function render_for_text($text) {
		$this->options['content'] = $text;
	}

	/**
	 * chooses between file, template, action and text depending on
	 * whether there is a leading slash (file and file must under FIREFLY_APP_DIR),
	 * or an embedded slash (template),
	 * or no slash and no white space at all in what�s to be rendered (action),
	 * or render as string (text).
	 */
	private function parse_shortcut_options($options) {
		$this->extension = $this->request->format;
		if($this->extension == 'js') {
			// php will auto add header, don't worry about browser's javascript file cache.
			// Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
			$this->extension = '.js.php';
		}
		elseif($this->extension == 'xml') {
			$this->extension = '.xml.php';
		}
		elseif($this->extension == 'json') {
			$this->extension = '.json.php';
		} else {
			// TODO: register new format template.
		}
		if(!is_array($options)) {
			if(is_string($options)) {
				if(preg_match('/\s/', $options)) {
					$options = array('text' => $options);
				} else {
					$x = $this->template_root . $this->controller->params['controller'] . DS . $options . $this->extension;
					if(file_exists($options)) {
						$options = array('file' => $options);
					}
					elseif(strpos($options, '/') !== false && file_exists($this->template_root . str_replace('/', DS, preg_replace('/^[\/]?(.)/', '\1', $options)) . $this->extension)) {
						$options = array('layout' => false, 'file' => $this->template_root . str_replace('/', DS, preg_replace('/^[\/]?(.)/', '\1', $options)) . $this->extension);
					}
					elseif(strpos($options, '/') === false && file_exists($this->template_root . $this->controller->params['controller'] . DS . $options . $this->extension)) {
						$options = array('layout' => false, 'file' => $this->template_root . $this->controller->params['controller'] . DS . $options . $this->extension);
					}
					elseif(strpos($options, '/') !== false && file_exists($this->template_root . str_replace('/', DS, preg_replace('/^[\/]?(.)/', '\1', $options)) . '.php')) {
						$options = array('file' => $this->template_root . str_replace('/', DS, preg_replace('/^[\/]?(.)/', '\1', $options)) . '.php');
					}
					elseif(strpos($options, '/') === false && file_exists($this->template_root . $this->controller->params['controller'] . DS . $options . '.php')) {
						$options = array('file' => $this->template_root . $this->controller->params['controller'] . DS . $options . '.php');
					} else {
						$options = array('text' => $options);
					}
				}
			} else {
				$options = array();
			}
		}
		if(isset($options['content_type'])) {
			$this->response->set_content_type($options['content_type']);
		}
		elseif(isset($options['format'])) {
			$this->response->set_content_type_by_extension($options['format']);
		} else {
			$this->response->set_content_type_by_extension($this->request->format);
		}
		if(isset($options['status'])) {
			$this->response->set_header_status($options['status']);
		}
		if(isset($options['location'])) {
			$this->response->redirect_to($options['location']);
		}
		if(empty($options['locals']) || !is_array($options['locals'])) {
			$options['locals'] = array();
		}

		return $options;
	}

	/**
	 * when $partial is true, extract controller and partial action:
	 * action => _action
	 * controller/action => controller/_action
	 * other_prefix_path/controller/action => other_prefix_path/controller/_action
	 */
	private function find_template($action_name, $partial = false) {
		if($action_name === true) {
			return $this->get_filename_by_extension($this->default_template);
		}
		elseif(file_exists($this->get_filename_by_extension($action_name))) {
			return $this->get_filename_by_extension($action_name);
		} else {
			if(strpos($action_name, '/') !== false) {
				if($partial) {
					// TODO: prevent recursive partial, create an array to record partial's parent level
					// $parts = explode('/', $action_name);
					// $max_index = count($parts) - 1;
					// $parts[$max_index] = '_' . $parts[$max_index];
					// $action_name = join('/', $parts);
					$action_name = preg_replace('/^(.*)(\/)(\w+)$/', '\1\2_\3', $action_name);
				}
				return $this->get_filename_by_extension($this->template_root . preg_replace('/^[\/]?(.)/', '\1', $action_name));
			} else {
				if($partial) {
					$action_name = '_' . $action_name;
				}
				return $this->get_filename_by_extension($this->template_root . $this->controller->params['controller'] . DS . $action_name);
			}
		}
	}

	private function get_filename_by_extension($filename) {
		if(file_exists($filename . $this->extension)) {
			// don't use layout for js/xml/json
			$this->layout = false;
			return $filename . $this->extension;
		} else {
			return $filename . '.php';
		}
	}

	private function check_render_format($options) {
		if(isset($options['format'])) {
			$format = $options['format'];
		} else {
			$format = $this->request->format;
		}
		if(substr_count($format, '.') > 0) {
			throw new FireflyException("Bad request format: " . $this->request->format);
		}
	}

}
?>
