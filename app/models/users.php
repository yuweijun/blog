<?php
class Users extends ActiveRecord {
	protected static $table_name = 'users';
	protected static $primary_key = 'id';
	protected static $has_many = array('posts' => array('foreign_key' => 'user_id'),
									   'links' => array('foreign_key' => "user_id"));

	public static function authenticate($username, $password) {
		$user = Users::find('first', array('conditions' => array("name = '$username'")));
		if($user) {
			$check = self::check_password($password, $user->password);
			if($check) {
				return $user;
			}
		}
		return false;
	}

	private static function check_password($password, $stored_hash) {
		$wp_hasher = new PasswordHash(8, true);
		return $wp_hasher->CheckPassword($password, $stored_hash);
	}

}
?>