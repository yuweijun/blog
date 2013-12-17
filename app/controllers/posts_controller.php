<?php
class PostsController extends ApplicationController {
	
	// for cache page and cache action
	protected $cache = array('page' => 'index, show', 'action' => 'search');
	
	// protected $post_actions = 'create, update, delete';
	protected $post_actions = array('create', 'update', 'delete');
	
	protected $layout = 'admin';

	protected $before_filter = array( 'is_login', 'except' => array ( 'index', 'show', 'search' ) );

	// GET last post
	public function index() {
		$this->last_post = Posts::last();
		$this->params['id'] = $this->last_post->id;
		$this->show();
	}

	// GET /posts/show/1
	public function show() {
		$this->post = Posts::find($this->params['id']);
		$this->page_title = $this->post->title;
		
		$this->comments = $this->post->comments;
		$this->comment_count = sizeof($this->comments);
		
		$this->previous_post = $this->post->get_previous_post();
		$this->next_post = $this->post->get_next_post();
		
		$this->latest_posts = Posts::get_latest_posts();
		$this->latest_comments = Comments::get_latest_comments();
		$this->latest_links = Links::get_latest_links();

		$this->render(array(
			'file' => APP_THEMES_DIR . DS . THEME . DS . 'show.php',
			'layout' => APP_THEMES_DIR . DS . THEME . DS . 'layout.php'
		));
	}

	//	GET /posts/add
	public function add() {
		$this->page_title = "Add new post";
		$this->post = new Posts;
	}

	//	POST /posts/create
	public function create() {
		$post = new Posts;
		$post->title = $this->params['title'];
		$post->content = $this->params['content'];
		$post->user_id = $_SESSION['user_id'];
		if ($post->save()) {
			$this->flash->set('notice', 'create new post successfully!');
			$this->redirect_to('/posts/index');
		}
	}

	//	GET /posts/edit/1
	public function edit() {
		$this->post = Posts::find($this->params['id']);
	}

	// POST /posts/update/1
	public function update() {
		$post = Posts::find($this->params['id']);
		$post->title = $this->params['title'];
		$post->content = $this->params['content'];
		if ($post->save()) {
			$this->flash->set('notice', 'update post successfully!');
			$this->redirect_to('/posts/show/' . $this->params['id']);
		} else {
			$this->flash->set('notice', 'edit post failure!');
			$this->redirect_to('/posts/edit/' . $this->params['id']);
		}
	}

	// POST /posts/delete/1
	public function delete() {
		$deleted = Posts::update($this->params['id'], array (
			'status' => 'deleted'
		));
		if ($deleted) {
			$this->render("post deleted successfully.");
		} else {
			$this->render("post deleted failure.");
		}
	}

	// POST or GET
	public function search() {
		$q = isset ($this->params['id']) ? $this->params['id'] : $this->params['q'];
		$this->posts = Posts::find_by_sql("SELECT * FROM posts WHERE title LIKE '%$q%' OR content LIKE '%$q%'");

		$this->render(array (
			'file' => APP_THEMES_DIR . DS . THEME . DS . 'search.php',
			'layout' => APP_THEMES_DIR . DS . THEME . DS . 'layout.php'
		));
	}
}
?>
