<?php
class Posts extends ActiveRecord {
	protected static $primary_key = 'id';

	protected static $belongs_to = array('user' => array('class_name' => 'Users', 'foreign_key' => 'user_id'));

	protected static $has_many = array('comments' => array('foreign_key' => 'post_id', 'conditions' => array('approved' => 1)));
	
	public static function get_latest_posts() {
		return Posts::find('all', array('order' => 'created_on DESC', 'limit' => 6));
	}
	
	public function get_previous_post() {
		return Posts::first(array('order' => 'created_on DESC', 'conditions' => 'created_on < ' . $this->created_on));
	}
	
	public function get_next_post() {
		return Posts::first(array('order' => 'created_on ASC', 'conditions' => 'created_on > ' . $this->created_on));
	}
	
}
?>