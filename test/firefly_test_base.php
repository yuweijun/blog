<?php
define('ENVIRONMENT', 'test');

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'environment.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly' . DIRECTORY_SEPARATOR . 'dispatcher.php');

class FireflyTestBase extends PHPUnit_Framework_TestCase {

	/**
	 * test action render.
	 * return response body of action render.
	 */
	protected function getHttpResponseBody($path) {
		ob_start();
		$_GET['fireflypath'] = $path;
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$dispatcher = new Dispatcher;
		$dispatcher->dispatch();
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * use TestController to test render method.
	 */
	protected function getRenderResults($options) {
		include_once(FIREFLY_APP_DIR . DS . 'controllers' . DS . 'admin_controller.php');
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['fireflypath'] = '/admin/test';
		$class_name = "AdminController";
		$request = new Request;
		$response = new Response;
		$params = $request->parameters();
		$controller = new $class_name($request, $response, $params);
		ob_start();
		$controller->render($options);
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * output test logs.
	 */
	public function testLoggerOutput() {
		Logger::output();
	}

}
?>