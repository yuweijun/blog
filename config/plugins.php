<?php
include_once (FIREFLY_LIB_DIR . DS . 'plugin.php');

add_plugin('cache');

add_plugin(array( 'name' => 'comments_observer', 'class_name' => 'CommentsObserver' ));
add_plugin(array( 'name' => 'posts_observer', 'class_name' => 'PostsObserver' ));

add_action('dispatch.start', array( 'Profiler', 'start' ));
add_action('dispatch.end', array( 'Profiler', 'end' ));

?>