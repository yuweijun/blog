<?php
function edit_post_content($content) {
    return preg_replace("/&/", "&amp;", $content);
}

add_action('edit.post.content.part', 'edit_post_content');
?>
