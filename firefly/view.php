<?php
include_once('view/render_options.php');

class View {
	private $first_render = true;
	private $request;
	private $response;
	private $controller;

	public function __construct($request, $response, $controller) {
		$this->controller = $controller;
		$this->request = $request;
		$this->response = $response;
	}

	final public function render($options) {
		$render_options = new RenderOptions($this->request, $this->response, $this->controller);
		$options = $render_options->parse($options);
		if($this->first_render) {
			// render options from controller and send reponse headers only once.
			$this->response->send_headers();
			$this->first_render = false;
		} else {
			if(empty($options['layout'])) {
				// partial render options from view, set default layout to false.
				$options['layout'] = false;
			}
		}

		$vars = array_merge(get_object_vars($this->controller), $options['locals']);
		extract($vars, EXTR_SKIP);
		$controller_name = isset($controller_name) ? $controller_name : $this->controller->params['controller'];
		$action_name = isset($action_name) ? $action_name : $this->controller->params['action'];

		ob_start();
		Plugin::get_reference()->trigger('render.before_template', array($this->controller));
		if(isset($options['content'])) {
			echo $options['content'];
		} else {
			require $options['template'];
		}
		Plugin::get_reference()->trigger('render.after_template', array($this->controller));
		$content_for_layout = ob_get_clean();
		if(isset($options['layout']) && $options['layout']) {
			ob_start();
			require $options['layout'];
			echo ob_get_clean();
		} else {
			echo $content_for_layout;
		}
	}

}
?>