<div id="container">
	<div id="main">
		<div class="post" id="post-1">
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
					<span><a href="#respond">Add new comment</a> </span>
				</div>
			</div>
			<div class="clear"></div>

            <div class="entry"><?php echo do_filter("show.post.content", $post->content); ?>
			</div>
            <div id="post-nav">
                <?php if ($next_post) { ?>
                <div id="nextpost">
                    <a href="/posts/show/<?= $next_post->id ?>" title="<?= $next_post->title ?>"><?= $next_post->title ?></a>&gt;&gt;
                </div>
                <?php } ?>
                <?php if ($previous_post) { ?>
                <div id="previouspost">
                    &lt;&lt;<a href="/posts/show/<?= $previous_post->id ?>" title="<?= $previous_post->title ?>"><?= $previous_post->title ?></a>
                </div>
                <?php } ?>
            </div>
			<div class="clear"></div>
		</div>
		<!-- end post -->

		<div id="comments" class="hide">
			<h3><?php echo $comment_count ?> comment so far</h3>
			<span class="add_your_comment"><a href="#respond">Add Your Comment</a></span>
			<div class="clear"></div>
		</div>

		<ol class="commentlist hide">
			<?php foreach ($comments as $key => $comment) { ?>
			<li class="comment even thread-even depth-1" id="li-comment-1">
				<div id="comment-<?php echo $comment->id ?>">
					<div class="left"></div>
					<div class="right">
						<div class="comment-meta commentmetadata">
							<a href="<?php echo $comment->author_url ?>" rel="external nofollow" class="url">
							<?php echo do_filter('show.comment.author', $comment->author) ?>
							</a>
							
							<?php if (is_admin()) { ?>
								<span>[<a href="/comments/delete/<?php echo $comment->id ?>"> delete </a>]</span>
							<?php } ?>
							
							<span>
							<?php echo date('Y-m-d H:i:s', $comment->created_on) ?>
							</span>
							<span>said:</span>
						</div>

						<p>
							<?= do_filter('show.comment.content', $comment->content, 140) ?>
						</p>
					</div>
					<div class="clear"></div>
				</div>
			</li>
			<?php } ?>
		</ol>

		<div id="respond" class="hide">
			<div class="h3_cancel_reply">
				<h3>Your Comment</h3>
				<div class="clear"></div>
			</div>

			<form action="/comments/create" method="post" id="commentform">
				<div class="input_area">
					<textarea name="comment" id="comment" cols="60" rows="5" tabindex="1" class="message_input"></textarea>
				</div>

				<div class="user_info">
					<div class="single_field">
						<label for="author" class="desc">Name<abbr title="Required">*</abbr> :</label> 
						<input type="text" name="author" id="author" value="<?= show_user_name() ?>" size="22" tabindex="2" class="comment_input" aria-required="true" />
					</div>

					<div class="single_field">
						<label for="email" class="desc">Email :</label>
						<input type="text" name="email" id="email" value="" size="22" tabindex="3" class="comment_input" />
					</div>

					<div class="single_field">
						<label for="url" class="desc">URI :</label>
						<input type="text" name="url" id="url" value="" size="22" tabindex="4" class="comment_input" />
					</div>
					<div class="clear"></div>
				</div>

				<div class="submit_button">
					<input type="hidden" name="post_id" value="<?php echo $post->id ?>" id="post_id" />
					<input type="submit" id="submit" tabindex="5" value="Submit" class="button" />
					<div class="clear"></div>
				</div>
			</form>

		</div>
	</div>
	<div class="clear"></div>
</div>

<div id="sidebar">
	<div id="list_posts">
		<ul>
			<li>
				<h2>Recent Posts</h2>
				<ul class="latest_post">
				<?php foreach ($latest_posts as $key => $latest_post) { ?>
					<li><a href="/posts/show/<?php echo $latest_post->id ?>" title="<?= $latest_post->title ?>"><?= do_filter('show.latest_post.title', $latest_post->title, 40) ?></a></li>
				<?php } ?>
				</ul>
			</li>
		</ul>
	</div>
	<div id="list_comments">
		<ul>
			<li>
				<h2>Recent Comments</h2>
				<ul class="recentcomment">
				<?php foreach ($latest_comments as $key => $rc) { ?>
					<li class="rc">
						<a href="/posts/show/<?php echo $rc->post_id ?>#comment-<?php echo $rc->id ?>" title="<?php echo $rc->author ?>">
							<?= $rc->author ?>:
						</a>
						<?= do_filter('show.comment.content', $rc->content, 140) ?>
					</li>
				<?php } ?>
				</ul>
			</li>

		</ul>
	</div>
	<div id="list_links">
		<ul>
			<li>
				<h2>Links</h2>
				<ul class="recentcomment">
				<?php foreach ($latest_links as $key => $link) { ?>
					<li class="link">
						<a href="<?php echo $link->url ?>" title="<?php echo $link->description ?>">
							<?php echo $link->name ?>
						</a>
					</li>
				<?php } ?>
				</ul>
			</li>
		</ul>
	</div>
</div>
