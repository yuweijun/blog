<!DOCTYPE html>
<html>
<head>
<title><?=$page_title?></title>
<meta charset="UTF-8" />
<link rel="stylesheet" href="/themes/default/admin/style.css" type="text/css" media="screen" />
<body>
<div id="header">
	<div id="headerleft">
		<a href="/posts/index"><?= _t("david.yu's blog") ?></a>
	</div>
    <div id="newpost">
        <a href="/posts/add"><?= _t("new") ?></a>
    </div>
	<div id="headerright">
		<a href="/admin/logout"><?= _t('logout') ?></a>
	</div>
</div>
<!-- Closes header -->

<?=$content_for_layout?>

<div id="footer">
	<div id="footerleft">
		<p>
			<?= _t('Powered by <a href="http://www.phpfirefly.com/">phpfirefly</a> framework.') ?>
		</p>
	</div>
	<div class="cleared"></div>
</div>
<!-- Closes footer -->

</body>
</html>
