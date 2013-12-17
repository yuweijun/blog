<!DOCTYPE html>
<html>
	<head>
		<title>
			Redirecting ...
		</title>
		<meta charset="UTF-8" />
	</head>
	<body>
		<script type="text/javascript">
			setTimeout(function(){window.location="<?=$redirect_url?>"},<?= $pause ?>);</script>
		<?= $message ?>
	</body>
</html>