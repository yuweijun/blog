<?php
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
		$pager .= '<li class="previous"><a href="' . $url . '/page/' . $i . $query . '" class="prn" rel="nofollow" title="go to page ' . $i . '">&lt; Previous</a></li>';
	}

	if ($start > 1) {
		$pager .= '<li><a href="' . $url . '/page/1' . $query . '" title="go to page ' . $i . '">1</a></li>';
		if ($start > 2) {
			$pager .= '<li class="prn">...</li>';
		}
	}

	for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
		if ($i == $current) {
			$pager .= '<li><span>' . $i . '</span></li>';
		} else {
			$pager .= '<li><a href="' . $url . '/page/' . $i . $query . '" title="go to page ' . $i . '">' . $i . '</a></li>';
		}
	}

	if ($total_pages > $end) {
		$i = $total_pages;
		$pager .= '<li class="prn">...</li>';
		$pager .= '<li><a href="' . $url . '/page/' . $i . $query . '" title="go to page ' . $i . '">' . $i . '</a></li>';
	}

	if ($current < $total_pages) {
		$i = $current +1;
		//echo '<li class="prn">...</li>';
		$pager .= '<li class="next"><a href="' . $url . '/page/' . $i . $query . '" class="prn" rel="nofollow" title="go to page ' . $i . '">Next &gt;</a></li>';
	} else {
		$pager .= '<li class="next">Next &gt;</li>';
	}

	return $pager . '</ul>';
}

function newline_to_breakline($value) {
	return preg_replace("/(\n|\r\n|\r)/", '<br/>', $value);
}

function show_user_name() {
	if (is_admin()) {
		return 'admin';
	} else {
		return '';
	}
}

// add_action('show.post.content', 'newline_to_breakline');
add_action('show.comment.content', 'remove_html_tags');
add_action('show.comment.content', 'truncate');
add_action('show.latest_post.title', 'truncate');
?>