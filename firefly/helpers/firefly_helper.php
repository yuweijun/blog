<?php
// create url by options
function url_for($options) {
	return Router :: url_for($options);
}

function debug($object) {
	new Debugger($object);
}

function logger($msg) {
	Logger::info($msg);
}

/**
 * These functions can be replaced via plugins. If plugins do not redefine these
 * functions, then these will be used instead.
 * Most of below functions are based on WordPress.
 */
if (!function_exists('paginate')) {
	/**
	 * Based on SachinKRaj php paging function.
	 * http://blog.sachinkraj.com/how-to-create-simple-paging-with-php-cs/
	 */
	function paginate($total, $current = 1, $per_page = 5, $url = '', $query_string = '') {
		//per page count
		$index_limit = 10;

		//set the query string to blank, then later attach it with $query_string
		$query = '';

		if (strlen($query_string) > 0) {
			$query = "&amp;" . $query_string;
		}

		$total_pages = ceil($total / $per_page);
		$start = max($current -intval($index_limit / 2), 1);
		$end = $start + $index_limit -1;

		$pager = '<ul class="paging">';

		if ($current == 1) {
			$pager .= '<li class="previous">&lt; Previous</li>';
		} else {
			$i = $current -1;
			$pager .= '<li class="previous"><a href="' . $url . '?page=' . $i . $query . '" class="prn" rel="nofollow" title="go to page ' . $i . '">&lt; Previous</a></li>';
		}

		if ($start > 1) {
			$pager .= '<li><a href="' . $url . '?page=1' . $query . '" title="go to page ' . $i . '">1</a></li>';
			if ($start > 2) {
				$pager .= '<li class="prn">...</li>';
			}
		}

		for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
			if ($i == $current) {
				$pager .= '<li><span>' . $i . '</span></li>';
			} else {
				$pager .= '<li><a href="' . $url . '?page=' . $i . $query . '" title="go to page ' . $i . '">' . $i . '</a></li>';
			}
		}

		if ($total_pages > $end) {
			$i = $total_pages;
			$pager .= '<li class="prn">...</li>';
			$pager .= '<li><a href="' . $url . '?page=' . $i . $query . '" title="go to page ' . $i . '">' . $i . '</a></li>';
		}

		if ($current < $total_pages) {
			$i = $current +1;
			//echo '<li class="prn">...</li>';
			$pager .= '<li class="next"><a href="' . $url . '?page=' . $i . $query . '" class="prn" rel="nofollow" title="go to page ' . $i . '">Next &gt;</a></li>';
		} else {
			$pager .= '<li class="next">Next &gt;</li>';
		}

		return $pager . '</ul>';
	}

}

if (!function_exists('hash_password')) {
	/**
	 * Create a hash (encrypt) of a plain text password.
	 */
	function hash_password($password) {
		$hasher = new PasswordHash(8, TRUE);

		return $hasher->HashPassword($password);
	}

}

if (!function_exists('generate_password')) {
	/**
	 * Generates a random password drawn from the defined set of characters.
	 **/
	function generate_password($length = 12, $special_chars = true) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ($special_chars)
			$chars .= '!@#$%^&*()';

		$password = '';
		for ($i = 0; $i < $length; $i++)
			$password .= substr($chars, rand(0, strlen($chars) - 1), 1);
		return $password;
	}

}
?>
