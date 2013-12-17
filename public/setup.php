<!DOCTYPE html>
<html>
<head>
<title><?=$page_title?></title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="/themes/default/admin/style.css" type="text/css" media="screen" />
<body>
<?php
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'environment.php');
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'boot.php');
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly' . DIRECTORY_SEPARATOR . 'dispatcher.php');
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly' . DIRECTORY_SEPARATOR . 'router.php');
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'firefly' . DIRECTORY_SEPARATOR . 'controller.php');

// 1. Check url rewrite enabled.
$url_rewritable = Router :: url_rewritable();
if (!$url_rewritable) {
	if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules(), true)) {
		$htaccess_file = APP_ROOT . DS . '.htaccess';
		$contents = "\tRewriteEngine on \n\n\tRewriteCond %{REQUEST_FILENAME} !-f \n\tRewriteCond %{REQUEST_FILENAME} !-d \n\tRewriteRule ^(.*)$ dispatch.php?fireflypath=$1 [QSA,L]\n\n\tRewriteCond %{REQUEST_FILENAME}index.html !-f\n\tRewriteCond %{REQUEST_FILENAME}index.php !-f\n\tRewriteRule ^$ dispatch.php?fireflypath=/ [QSA,L]\n";
		if (is_writable($htaccess_file)) {
			file_put_contents($htaccess_file, $contents);
		} else {
			echo "The file <b>$htaccess_file</b> is not writable, add below content<pre>$contents</pre> to <b>$htaccess_file</b>";
		}
	} elseif (preg_match("/nginx/", $_SERVER["SERVER_SOFTWARE"])) {
		echo "please add below configure to your nginx server settings:";
		echo '<div style="white-space: pre-wrap;">';
        echo "location / {\n\ttry_files \$uri /index.php;\n}\n\nif (!-f \$request_filename) {\n\trewrite ^(.*)\?(.*)$ /dispatch.php?fireflypath=$1&$2 last;\n\trewrite ^(.*)$ /dispatch.php?fireflypath=$1 last;\n\tbreak;\n}";
		echo "</div>";
	} else {
		echo "please include web server url rewrite module (such as mod_rewrite for apache)!";
	}
} else {
	echo "URL rewrite is enabled!";
}
echo "<br />";

// 2. Check database connection config.
// TODO:
echo "<br />";

// 3. Check log file writable.
// TODO:
// echo phpinfo();

?>
<div id="root"></div>
<script type="text/javascript" src="/javascripts/jquery.js"></script>
<script type="text/javascript">
	$('#root').load('/ajax.php');
</script>
</body>
</html>
