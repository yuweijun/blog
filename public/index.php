<?php
/**
 * dispatch.php
 * dispatch process: load config -> load environment -> dispatch request.
 */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'boot.php');

$dispatcher = new Dispatcher();
$dispatcher->dispatch();
?>