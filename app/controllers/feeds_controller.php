<?php
class FeedsController extends ApplicationController {

	protected $layout = false;
	
	public function index() {
		$this->rss2();
	}

	// GET latest posts /feeds/rss2.xml
	public function rss2() {
		$this->posts = Posts::get_latest_posts();

		$this->render(array ('action' => '/feeds/rss2.xml', 'layout' => false ));
	}
	
}
?>
