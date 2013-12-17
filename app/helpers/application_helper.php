<?php
// class static functions in helpers.
function h($string) {
	return htmlentities($string);
}

function flash_notice($flash) {
	if (isset($flash['notice'])) {
		echo '<div class="firefly-notice">';
		echo $flash['notice'];
		echo '</div>';
	}
}

function flash_error($flash) {
	if (isset($flash['error'])) {
		echo '<div class="firefly-error">';
		echo $flash['error'];
		echo '</div>';
	}
}

function is_login() {
	return isset($_SESSION['user_id']);
}

function is_admin() {
	return isset($_SESSION['user_name']) && $_SESSION['user_name'] == 'admin';
}
?>
