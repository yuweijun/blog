<?php
/**
 * plugin for comments observer.
 * clear cache pages of posts controller.
 */
class CommentsObserver {
	
	public function __construct() {
		add_action('comments.after_destroy', array($this, 'sweeper'));
		add_action('comments.after_save', array($this, 'sweeper'));
		add_action('show.comment.author', array($this, 'add_author_company'));
		add_action('show.comment.author', array($this, 'add_author_city'));
	}

	public function add_author_city($value) {
		if ($value == 'admin') {
			return $value . '@Shanghai';
		} else {
			return $value;
		}
	}
	
	public function add_author_company($value) {
		if ($value == 'admin') {
			return $value . '@Darwin';
		} else {
			return $value;
		}
	}
	
	public function sweeper($post) {
		Cache::remove('posts, admin');
	}
}

?>
