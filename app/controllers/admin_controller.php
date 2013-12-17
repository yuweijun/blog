<?php
class AdminController extends ApplicationController {
	
	protected $cache = array('action' => 'index');
	
	protected $before_filter = array('is_login', 'except' => array('login', 'test'));
	
	protected $after_filter = array('afterfilter', 'except' => 'login');
	
	protected $session_off = array('test');

	public function test() {
		$this->render(array('status' => 404, 'text' => '404 Found!'));
	}

	public function index() {
		$this->per_page = array_key_exists('per_page', $this->params) ? $this->params['per_page'] : 3;
		$this->current_page = array_key_exists('page', $this->params) ? $this->params['page'] : 1;

		$this->page_title = "administration of david.yu's blog.";

		$conditions = array('user_id' => $_SESSION['user_id'], 'status' => 'publish');
		$sql_components = array('conditions' => $conditions, 'order' => 'created_on DESC', 'offset' => $this->per_page * ($this->current_page - 1), 'limit' => $this->per_page);
		$this->posts = Posts::find('all', $sql_components);
		$this->total = Posts::count(array('conditions' => $conditions));

		$this->pager = paginate($this->total, $this->current_page, $this->per_page, '/admin/index');
		// debug($this);
	}
	
	// POST or GET
	public function search() {
		if (isset($this->params['q'])) {
			$this->q = $this->params['q'];
			$q = '%' . $this->q . '%';
			$this->per_page = array_key_exists('per_page', $this->params) ? $this->params['per_page'] : 3;
			$this->current_page = array_key_exists('page', $this->params) ? $this->params['page'] : 1;
			
			$this->page_title = "search results of david.yu's blog.";
			
			$conditions = array("user_id = ? AND status = ? AND (title LIKE ? OR content LIKE ?)", $_SESSION['user_id'], 'publish', $q, $q);
			$sql_components = array('conditions' => $conditions, 'order' => 'created_on DESC', 'offset' => $this->per_page * ($this->current_page - 1), 'limit' => $this->per_page);
			$this->posts = Posts::find('all', $sql_components);
			$this->total = Posts::count(array('conditions' => $conditions));
	
			$this->pager = paginate($this->total, $this->current_page, $this->per_page, '/admin/search/' . $this->q);
			$this->render(array ( 'layout' => 'search' ));
		} else {
			$this->flash('notice', 'please input search keywords');
			$this->redirect_to('/admin/index');
		}
	}

	public function login() {
		$this->page_title = "user login";
		if($this->request->method == 'post') {
			if($user = Users::authenticate($this->params['username'], $this->params['password'])) {
				$user->is_session_object(true);
				$_SESSION['user_id'] = $user->id;
				$_SESSION['user_name'] = $user->name;
				$_SESSION[LOCALE] = 'en_US';
				// $_SESSION[LOCALE] = 'zh_CN';
				
				$this->flash->set('notice', 'user login successfully!');
				// $this->redirect_to('/admin/index', 302);
				$this->flash_page('user login successfully!', '/admin/index', 1);
			} else {
				$this->flash->now('error', 'username or password error!');
			}
		}
		// $this->render(array('layout' => false));
	}

	/**
	 * If $_SESSION (or $HTTP_SESSION_VARS for PHP 4.0.6 or less) is used, 
	 * use unset() to unregister a session variable, i.e. unset ($_SESSION['varname']);. 
	 */
	public function logout() {
		unset($_SESSION['user_id']);
		unset($_SESSION['user_name']);
		unset($_SESSION[LOCALE]);
		// session_unset();
		// session_destroy();
		// $_SESSION = array();
		$this->flash_page('user logout successfully!', '/admin/login', 1);
	}

	protected function before_filter() {
		// logger("before filter in admin controller.");
	}

	protected function after_filter() {
		// logger("after filter in admin controller.");
	}

	protected function afterfilter() {
		// logger("afterfilter in admin controller.");
	}

}
?>
