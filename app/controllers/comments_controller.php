<?php
class CommentsController extends ApplicationController {

	protected $before_filter = array('is_login', 'except' => array('create'));

	// GET all comments
	public function index() {
		$this->comments = Comments :: find('all');
		$this->render("all comments list");
	}

	// GET /comments/show/1
	public function show() {
		$this->page_title = "Show Comment Content";
		$this->comment = Comments :: find($this->params['id']);
		$this->render($this->comment->content);
	}

	//	POST /comments/create
	public function create() {
		$comment = new comments;
		$comment->approved = 1;
		$comment->author = $this->params['author'];
		$comment->author_email = $this->params['email'];
		$comment->author_url = $this->params['url'];
		$comment->author_ip = $_SERVER['REMOTE_ADDR'];
		$comment->content = $this->params['comment'];
		$comment->post_id = $this->params['post_id'];
		if ($comment->save()) {
			$this->flash_page("comment add successfully.", $_SERVER['HTTP_REFERER'], 1);
		} else {
			$this->flash_page("comment add failure.", $_SERVER['HTTP_REFERER'], 1);
		}
	}

	// POST /comments/delete/1
	public function delete() {
		$deleted = Comments :: update($this->params['id'], array('approved' => 0));
		if ($deleted) {
			$this->flash_page("comment deleted successfully.", $_SERVER['HTTP_REFERER'], 1);
		} else {
			$this->flash_page("comment deleted failure.", $_SERVER['HTTP_REFERER'], 1);
		}
	}

	// GET OR POST
	public function search() {
		$q = isset($this->params['id']) ? $this->params['id'] : $this->params['q'];
		$this->comments = Comments :: find_by_sql("SELECT * FROM comments WHERE title LIKE '%$q%' OR content LIKE '%$q%'");
	}

}
?>
