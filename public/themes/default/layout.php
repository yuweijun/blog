<!DOCTYPE html>
<html>
<head>
<title><?=$page_title?></title>
<meta charset="UTF-8" />
<meta name="robots" content="follow, all" />
<link rel="stylesheet" type="text/css" media="screen" href="/themes/default/style.css">
</head>
<body>
	<div id="page_wrap">
		<div id="header">
			<div class="blog_title">
				<h1>
					<a href="http://www.phpfirefly.com">david.yu's blog</a>
				</h1>
				<p class="description">{"select": "jQuery", "from": "javascript frameworks"}</p>
			</div>

			<div id="search">
				<form id="searchform" action="/posts/search" method="post">
					<input type="text" id="searchinput" name="q" class="searchinput" title="search" />
					<input type="submit" id="searchsubmit" class="button" value="" title="search" />
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<?=$content_for_layout?>
	</div>
	<div id="footer">
		<div class="footer_wrapper">
			<div class="footer_left">
				&copy; david.yu's blog.
				Based on <a href="http://github.com/yuweijun/phpfirefly">phpfirefly</a> framework.
			</div>
		</div>
	</div>
</body>
</html>
