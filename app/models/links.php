<?php
class Links extends ActiveRecord {
	
	protected static $primary_key = 'id';

	public static function get_latest_links() {
		return Links::find('all', array('order' => 'created_on DESC', 'limit' => 6));
	}
	
}
?>