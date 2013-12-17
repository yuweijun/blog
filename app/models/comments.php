<?php
class Comments extends ActiveRecord {

	protected static $primary_key = 'id';

	protected static $belongs_to = array('post' => array('foreign_key' => 'post_id'));

	public function after_save() {
		Logger::warn('after_save callback return in comment model.');
		return true;
	}
	
	public static function get_latest_comments() {
		return Comments::find('all', array('order' => 'created_on DESC', 'limit' => 2));
	}
	
}
?>