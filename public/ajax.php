ajax request testing:
<?php
echo '$_SERVER["HTTP_X_REQUESTED_WITH"] = ' . $_SERVER['HTTP_X_REQUESTED_WITH'];
?>
<pre>
<?php
var_dump($_SERVER);
?>	
</pre>