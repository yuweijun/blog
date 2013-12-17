<?php
/**
 * These functions can be replaced via plugins. If plugins do not redefine these
 * functions, then these will be used instead.
 * Most of below functions are based on WordPress.
 */

if(!function_exists('utf8_substr')) {
	function utf8_substr($str,$start) {
	    preg_match_all("/./su", $str, $ar);
	
	    if(func_num_args() >= 3) {
	       $end = func_get_arg(2);
	       return join("",array_slice($ar[0], $start, $end));
	    } else {
	       return join("",array_slice($ar[0], $start));
	    }
	}
}

if(!function_exists('truncate')) {
	function truncate($text, $length = 30, $truncate_string = '...', $break = false) {
		return utf8_substr($text, 0, $length) . $truncate_string;
	}
}

if(!function_exists('h')) {
	function h($html) {
		return htmlentities($html);
	}
}

if(!function_exists('remove_html_tags')) {
	function remove_html_tags($document) {
	$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
	                 "'<[\/\!]*?[^<>]*?>'si",           // 去掉 HTML 标记
	                 "'([\r\n])[\s]+'",                 // 去掉空白字符
	                 "'&(quot|#34);'i",                 // 替换 HTML 实体
	                 "'&(amp|#38);'i", "'&(nbsp|#160);'i",
	                 "'&(iexcl|#161);'i", "'&(cent|#162);'i", "'&(pound|#163);'i", "'&(copy|#169);'i",
	                 "'&#(\d+);'e");                    // 作为 PHP 代码运行
	
	$replace = array ("", "", "\\1", "\"", "&", " ", chr(161), chr(162), chr(163), chr(169), "chr(\\1)");

	return preg_replace ($search, $replace, $document);
}
}
?>