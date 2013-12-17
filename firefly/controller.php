<?php
include_once('controller/flash.php');
include_once('controller/layout.php');
include_once('helpers/helpers.php');
include_once('helpers/firefly_helper.php');

class Controller {
	private $rendered = false;

	protected $view;
	protected $request;
	protected $response;
	protected $helpers;

	/**
	 * Methods in this array should be revoke before render.
	 * and those methods visiblity must be 'protected'.
	 * Don't revoke those methods for except actions.
	 * array('except' => array()).
	 */
	protected $before_filter = array();

	/**
	 * Methods in this array should be revoke after render.
	 * and those methods visiblity must be 'protected'.
	 */
	protected $after_filter = array();

	/**
	 * Actions in this array should not use session.
	 */
	protected $session_off = array();

	/**
	 * Actions in $post_actions should not access by http method GET.
	 */
	protected $post_actions = array();
	
	/**
	 * For cache page or cache action configure for current controller.
	 * Page cache only for http GET request and without query string.
	 * Should choise to cache page or action which request very frequence.
	 */
	protected $cache = array('page' => array(), 'action' => array());

	protected $layout;
	
	public $flash;
	public $params;
	public $cookies;
	public $sessions;
	public $page_title;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
		$this->params = $request->params;
		
		if(!in_array($this->params['action'], $this->session_off)) {
			// for improve performance with session off.
			// session default will start for all actions except $this->session_off.
			Session::start();
			Plugin::get_reference()->trigger('session_start');
		}
		
		$this->flash = Flash::get_reference();
		$this->view = new View($request, $response, $this);
		$this->layout = Layout::get_action_layout($this->params, $this->layout);
		Helpers::include_helpers($this->params['controller'], $this->helpers);

		$this->do_before_filter();
	}

	/**
	 * render type:
	 * text (layout default is false)
	 * file	(absolute path)
	 * template (template root app/views/)
	 * action
	 * update (ajax, layout default is false)
	 * xml (layout default is false)
	 * js (layout default is false)
	 * json (callback, layout default is false)
	 * patial (layout default is false)
	 * nothing (layout default is false)
	 *
	 * parameters in render options array:
	 * content_type (render content type)
	 * format (render content type by extension format)
	 * status (404/301/200 etc.)
	 * location (redirect_to)
	 * locals (must be array, act as view variable)
	 * layout
	 *
	 * examples:
	 * $this->redirect_to("/");
	 * $this->send_file(__FILE__);
	 * $this->render("test string render");
	 * $this->render(array('text' => "test string render"));
	 * $this->render(array('text' => "test string render", 'layout' => true));
	 * $this->render(array('layout' => false));
	 * $this->render(array('layout' => 'posts'));
	 * $this->render(array('layout' => 'not_exists_layout')); // trigger warning
	 * $this->render(array('js' => "alert('__METHOD__')"));
	 * $this->render(array('json' => "{name:'$this->action'}"));
	 * $this->render(array('json' => "{name:'$this->action'}", 'callback' => 'show'));
	 * $this->render(array('nothing' => true));
	 * $this->render(array('nothing' => false));
	 * $this->render(array('status' => 202));
	 * $this->render(array('status' => 202, 'layout' => false));
	 * $this->render(array('text' => "alert('" . __METHOD__ . "');\n", 'format' => 'js'));
	 * $this->render(array('location' => '/', 'status' => 301)); // move permanently redirection 301
	 * $this->render(array('locals' => array('var1' => 'locals_var1', 'var2' => 'locals_var2')));
	 * $this->render(array('file' => '/Users/yu/Sites/phpfirefly/app/views/test/test.php'));
	 * $this->render(array('update' => array('alert' => 'xxxx', 'hide' => 'test', 'show' => 'test2')));
	 * $this->render(array('template' => 'posts/index'));
	 * $this->render(array('template' => 'posts/index2')); // template not exists.
	 * $this->render(array('action' => 'posts/index'));
	 * $this->render(array('action' => 'posts/index', 'layout' => false));
	 * $this->render(array('action' => 'test'));
	 * $this->render('/Users/yu/Sites/phpfirefly/app/views/test/test.php');
	 * $this->render('posts/index');
	 * $this->render('test');
	 * $this->render(array('partial' => 'form'));
	 * $this->render(array('partial' => 'form', 'layout' => false));
	 * $this->render(array('partial' => 'posts/form'));
	 * $this->render(array('partial' => 'posts/form', 'layout' => 'posts'));
	 * action can render only once, partial is rendered by view can more one time.
	 */
	final public function render($options = array()) {
		if(!$this->rendered) {
			$this->rendered = true;
			$this->flash = $this->flash->to_array();
			ob_start();
			$this->before_render();
			Plugin::get_reference()->trigger('render.before_render', array($this));
			$this->view->render($options);
			$this->after_render();
			Plugin::get_reference()->trigger('render.after_render', array($this));
			$response_body = ob_get_clean();
			echo $response_body;
			$this->do_after_filter($response_body);			
		}
	}

	final public function get_layout() {
		return $this->layout;
	}

	public function action_missing() {
		$this->render(array('layout' => false, 'file' => FIREFLY_LIB_DIR . DS . 'view' . DS . 'action_missing.php'));
	}

	public function __toString() {
		return get_class($this);
	}

	public function __call($method, $args) {
		throw new FireflyException("$method not in this controller: " . get_class($this));
	}

	protected function before_filter() {}
	protected function after_filter() {}
	protected function before_render() {}
	protected function after_render() {}

	/**
	 * Shows a message to user $pause seconds, then redirects to $url.
	 * Uses flash_page.php as a layout for the messages.
	 */
	protected function flash_page($message, $url, $pause = 3) {
		defined('FLASH_PAGE') ? null : define('FLASH_PAGE', 0);
		// $flash['messages'] should access in current page.
		$this->flash->now('message', $message);
		if(FLASH_PAGE) {
			$file = FIREFLY_APP_DIR . DS . 'views' . DS . $this->params['controller'] . DS . 'flash_page.php';
			if(!file_exists($file)) {
				$file = FIREFLY_APP_DIR . DS . 'views' . DS . 'shares' . DS . 'flash_page.php';
				if(!file_exists($file)) {
					$file = FIREFLY_LIB_DIR . DS . 'view' . DS . 'flash_page.php';
				}
			}
			$this->render(array('file' => $file, 'layout' => false, 'locals' => array('message' => $message, 'redirect_url' => $url, 'pause' => $pause * 1000)));
		} else {
			$this->redirect_to($url);
		}
	}

	/**
	 * Alias method of $this->flash->set($key, $value)
	 */
	final protected function flash($key, $value) {
		$this->flash->set($key, $value);
	}

	final protected function redirect_to($url, $status = 302) {
		$this->response->redirect_to($url, $status);
	}

	final protected function send_file($file) {
		$this->response->send_file($file);
	}
	
	private function do_before_filter() {
		$this->check_post_actions();
		
		$except = $this->get_except_from_array($this->before_filter);
		if(!in_array($this->params['action'], $except)) {
			$this->before_filter();
			foreach($this->before_filter as $key => $filter) {
				// before filters in $before_filter array
				// notice in php: 0 == 'except', 0 == 'a' => true
				if ($key !== 'except') {
					call_user_func(array($this, $filter));
				}
			}
		}
		// do cache check in 'render.before_filter' hook.
		Plugin::get_reference()->trigger('render.before_filter', array($this));	
	}
	
	private function do_after_filter($response_body) {
		$except = $this->get_except_from_array($this->after_filter);
		if(!in_array($this->params['action'], $except)) {
			foreach($this->after_filter as $key => $filter) {
				// after filters in $after_filter array
				if ($key !== 'except') {
					call_user_func(array($this, $filter));
				}
			}
			$this->after_filter();
		}
		// do cache create in 'render.after_filter' hook.
		Plugin::get_reference()->trigger('render.after_filter', array($this, $response_body, $this->cache));		
	}
	
	private function get_except_from_array($filters) {
		$except = array();
		if(isset($filters['except'])) {
			$except = $filters['except'];
			if(is_string($except)) {
				$except = array($except);
			}
		}
		return $except;
	}
	
	/**
	 * some action only support http POST request.
	 * exit if request a post action with http method GET.
	 */
	private function check_post_actions() {
		$action = $this->params['action'];
		$post_actions = $this->post_actions;
		if (is_string($post_actions)) {
			$post_actions = preg_split('/\s*,\s*/', $post_actions);
		}
		if ($this->request->method == 'GET' && in_array($action, $post_actions)) {
			echo 'No route matches [GET]: ' . $this->request->path;
			exit;
		}
	}

}
?>
