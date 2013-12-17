<?php
/**
 * dispatch.php
 * dispatch process: load config -> load environment -> dispatch request.
 * DOCUMENT_ROOT can be set as {dirname(__FILE__)} or {dirname(__FILE__) . DIRECTORY_SEPARATOR . 'public'}
 */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'boot.php');

$dispatcher = new Dispatcher();
$dispatcher->dispatch();
?>