<?php
/** 
 * observer for posts.
 * posts cache sweeper.
 */
class PostsObserver {
	
	public function __construct() {
		add_action('posts.after_destroy', array($this, 'sweeper'));
		add_action('posts.after_save', array($this, 'sweeper'));
	}

	public function sweeper($post) {
		Cache::remove('posts');
		Cache::remove('admin');
	}

}

?>
