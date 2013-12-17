<?php
add_action('show.post.content.part', 'remove_html_tags');
add_action('show.post.content.part', 'truncate');
?>