<form action="" method="post" accept-charset="utf-8" id="loginform">
	<?php flash_error($flash); ?>
	<div>
		<input type="text" name="username" value="admin" />
	</div>
	<div>
		<input type="password" name="password"/>
	</div>
	<div>
		<input type="submit" value="<?php echo _t('login') ?>" />
	</div>
</form>
