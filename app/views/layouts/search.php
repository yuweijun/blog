<!DOCTYPE html>
<html>
<head>
<title><?=$page_title?></title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="/themes/default/admin/search.css" type="text/css" media="screen" />
<body>
<div id="header">
	<div id="headerleft">
		<a href="/posts/add">david.yu's blog</a>
	</div>
	<div id="headerright">
		<a href="/admin/logout">logout</a>
	</div>
</div>
<!-- Closes header -->

<?=$content_for_layout?>

<div id="footer">
	<div id="footerleft">
		<p>
			Powered by <a href="http://www.wordpress.org/">phpfirefly</a> framework.
		</p>
	</div>
	<div class="cleared"></div>
</div>
<!-- Closes footer -->

</body>
</html>
