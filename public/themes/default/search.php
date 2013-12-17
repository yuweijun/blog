<div id="container">
	<div id="main">
	<?php foreach ($posts as $index => $post) { ?>
		<div class="post" id="post-<?= $index ?>">
			<div class="date">
				<?php echo date('Y', $post->created_on) ?><br>
				<?php echo date('m.d', $post->created_on) ?>
			</div>
			<div class="title">
				<h2>
					<a href="/posts/show/<?php echo $post->id ?>" rel="bookmark" title="<?= $post->title; ?>">
						<?= $post->title; ?>
					</a>
				</h2>

				<div class="postmeta">
					<span>Author: <?php echo $post->user->name ?> </span>
					<?php 
					if (is_admin()) {
						echo '<span><a href="/posts/edit/' . $post->id . '">edit</a></span>';
					}
					?>
				</div>
			</div>
			<div class="clear"></div>

			<div class="entry">
				<?php echo do_filter("show.post.content", $post->content); ?>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	<?php } ?>
	<!-- end posts -->
	</div>
</div>
