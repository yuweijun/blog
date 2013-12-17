<?php
include_once('firefly_exception.php');
include_once('i18n.php');
require_once('plugin.php');
include_once('logger.php');
require_once('profiler.php');
include_once('debugger.php');
include_once('headers.php');
include_once('router.php');
include_once('request.php');
include_once('response.php');
include_once('session.php');
include_once('active_record.php');
include_once('view.php');
include_once('controller.php');

class Dispatcher {
	private $request;
	private $response;
	private $params;
	private $controller;

	public function dispatch() {
		$this->request = new Request;
		$this->response = new Response;
		try {
			$this->process();
		} catch(Exception $exception) {
			$this->exception_process($exception);
		}
	}

	private function process() {
		Plugin::get_reference()->trigger('dispatch.start', array($this->request, $this->response));
		$this->params = $this->request->parameters();
		include_once(FIREFLY_APP_DIR . DS . 'controllers' . DS . strtolower($this->params['controller']) . '_controller.php');
		$class_name = str_replace('_', '', $this->params['controller']) . "Controller";
		$class_name = array_pop(explode(DS, $class_name));
		$this->controller = new $class_name($this->request, $this->response);
		$this->render();
		Plugin::get_reference()->register('dispatch.end', array('Logger', 'output'));
		Plugin::get_reference()->trigger('dispatch.end', array($this->request, $this->response));
	}

	/**
	 * if request action exists in controller
	 * 		invoke controller->action
	 * else if request action file exists under views folder (for controller clean)
	 * 		render action file
	 * else
	 * 		render method_missing template
	 */
	private function render() {
		if(in_array($this->params['action'], get_class_methods(get_class($this->controller)))) {
			call_user_func(array($this->controller, $this->params['action']));
		} elseif(file_exists(FIREFLY_APP_DIR . DS . 'views' . DS . $this->params['controller'] . DS . $this->params['action'] . '.php')) {
			$this->controller->render(FIREFLY_APP_DIR . DS . 'views' . DS . $this->params['controller'] . DS . $this->params['action'] . '.php');
		} else {
			$this->controller->action_missing();
		}

		// maybe rendered in $controller->action() explicitly.
		$this->controller->render();
	}

	private function exception_process($exception) {
		FireflyException::exception_report($exception);
	}

}
?>