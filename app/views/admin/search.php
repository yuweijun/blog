<div id="wrapper">
	<div id="searchcontainer">
		<form method="post" id="searchform" action="/admin/search">
			<p>
				<input type="text" name="q" id="searchbox" /> 
				<input type="submit" value="search" />
			</p>
		</form>
		<div class="cleared"></div>
	</div>
	<div class="cleared"></div>
	<div id="main">
		<div id="contentwrapper">
			<?php foreach ($posts as $post) { ?>
			<div class="posts" id="post-<?php echo $post->id ?>">
				<h2 class="postTitle">
					<a href="/posts/show/<?php echo $post->id ?>"><?php echo $post->title ?></a>
				</h2>
				<p class="postMeta">
					by <?php echo $post->user->name ?> on <?php echo date('M.d, Y', $post->created_on) ?>,
					<a href="/posts/edit/<?php echo $post->id ?>">edit</a>
					<a href="/posts/delete/<?php echo $post->id ?>">delete</a>
				</p>
				<div class="postContent">
					<p><?php echo do_filter('show.post.content.part', $post->content, 500) ?></p>
				</div>
				<div class="comments">
					<span class="postComments">
					<?php 
					if ($post->comment_status == 'open') {
						echo '<a href="/posts/show/' . $post->id . '#respond">Leave a Comment</a>';
					} else {
						echo 'Comment Closed';
					}
					?>
					</span>
				</div>
				<div class="cleared"></div>
			</div>
			<?php } ?>
			<!-- Closes posts -->

			<div id="nextprevious">
				<?php echo $pager ?>
			</div>
		</div>
		<div class="cleared"></div>
		<!-- Closes contentwrapper-->
	</div>
	<!-- Closes Main -->
</div>
<!-- Closes wrapper -->
<?php
// debug($this->controller);
?>
