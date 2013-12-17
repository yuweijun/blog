<?php
class Request {
	
	public $params = false;
	public $method;
	public $format;
	public $path;

	public function __construct() {
		preg_match('/(.*?)(?:\.)(.*)$/', $this->get_firefly_path(), $matches);
		
		if(empty($matches)) {
			$this->path = $_GET['fireflypath'];
			$this->format = 'html';
		} else {
			$this->path = $matches[1];
			$this->format = strtolower($matches[2]);
		}
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Returns true if the request include header (X-Requested-With => XMLHttpRequest).
	 * The jQuery/Prototype Javascript library sends this header with every Ajax request.
	 */
	public function xml_http_request() {
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strstr(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest'));
	}

	public function xhr() {
		return $this->xml_http_request();
	}

	// Return true if the request came from localhost, 127.0.0.1
	public function local_request() {
		return($_SERVER['REMOTE_ADDR'] == '127.0.0.1' && $this->remote_ip() == '127.0.0.1');
	}

	public function remote_ip() {
		// remote_addr may be proxy address.
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Firstly merge POST and GET parameters in a single hash, then update hash by path parameters.
	 * Because request url may contain parameter key like "controller" and "action".
	 */
	public function parameters() {
		if ($this->params) {
			return $this->params;
		}
		$params = array_merge($_POST, $_GET);
		// Using POST hack HTTP PUT/DELETE methods, for http verb request.
		if(isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), array('PUT', 'DELETE'), true)) {
			$_SERVER['REQUEST_METHOD'] = strtoupper($params['_method']);
			unset($params['_method']);
		}
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		$params = array_merge($params, $this->path_parameters());
		unset($params['fireflypath']);
		if(empty($params['action'])) {
			$params['action'] = 'index';
		}
		$this->params = $params;
		return $params;
	}

	private function path_parameters() {
		return Router::recognize($this->path);
	}

	private function get_firefly_path() {
		if(isset($_GET['fireflypath'])) {
			return $_GET['fireflypath'];
		} else {
			return $_SERVER['SCRIPT_NAME'];
		}
	}
	
	public function __toString() {
		// return $this->path . '.' . $this->format;
		return $this->path;
	}

}
?>