<!DOCTYPE html>
<html>
    <head>
    	<meta charset="UTF-8" />
        <title>flash message</title>
		<script type="text/javascript">
			setTimeout(function(){window.location="<?=$redirect_url?>"}, <?= $pause ?>);
		</script>
    </head>
    <body>
		<?php echo $message; ?>
    </body>
</html>