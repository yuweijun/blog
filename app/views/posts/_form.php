<form action="/posts/<?= $post->id ? "update/" . $post->id : "create" ?>" id="postEditForm" method="post" accept-charset="utf-8">
	<div id="titleContainer">
		<input type="text" name="title" id="title" value="<?= $post->title ?>"/>
	</div>
	<div id="contentContainer">
		<textarea name="content" id="content"><?php echo do_filter('edit.post.content.part', $post->content) ?></textarea>
	</div>
	<div id="actionContainer">
		<input type="submit" value="save"/>
		<input type="button" value="cancel" id="cancel"/>
	</div>
</form>
<script type="text/javascript">
	document.getElementById('cancel').onclick = function() {
		history.back();
	}
</script>
